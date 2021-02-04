<?php
require_once ('db.php');
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2013 magix-cms.com <support@magix-cms.com>
 #
 # OFFICIAL TEAM :
 #
 #   * Gerits Aurelien (Author - Developer) <aurelien@magix-cms.com> <contact@aurelien-gerits.be>
 #
 # Redistributions of files must retain the above copyright notice.
 # This program is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 #
 # This program is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.
 #
 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------
 #
 # DISCLAIMER
 #
 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
class plugins_gtranslate_admin extends plugins_gtranslate_db
{
    protected $controller, $message, $template, $plugins, $modelLanguage, $collectionLanguage, $data, $header, $routingUrl, $domain;
    /**
     * GET
     * @var $getlang ,
     * @var $edit
     */
    public $getlang, $action, $edit, $tab;

    /**
     * POST
     * @var $slide
     * @var $sliderorder
     */
    public $apiGData, $id;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->template = new backend_model_template();
        $this->plugins = new backend_controller_plugins();
        $this->message = new component_core_message($this->template);
        $this->modelLanguage = new backend_model_language($this->template);
        $this->collectionLanguage = new component_collections_language();
        $this->data = new backend_model_data($this);
        $this->header = new http_header();
        $this->domain = new backend_controller_domain();

        $this->routingUrl = new component_routing_url();

        $formClean = new form_inputEscape();

        // --- Get
        if (http_request::isGet('controller')) {
            $this->controller = $formClean->simpleClean($_GET['controller']);
        }
        if (http_request::isGet('edit')) {
            $this->edit = $formClean->numeric($_GET['edit']);
        }
        if (http_request::isGet('action')) {
            $this->action = $formClean->simpleClean($_GET['action']);
        } elseif (http_request::isPost('action')) {
            $this->action = $formClean->simpleClean($_POST['action']);
        }
        if (http_request::isGet('tabs')) {
            $this->tab = $formClean->simpleClean($_GET['tabs']);
        }
        if (http_request::isPost('apiGData')) {
            $this->apiGData = $formClean->arrayClean($_POST['apiGData']);
        }
    }
    /**
     * Method to override the name of the plugin in the admin menu
     * @return string
     */
    public function getExtensionName()
    {
        return $this->template->getConfigVars('gtranslate_plugin');
    }
    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @param boolean $pagination
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true, $pagination = false) {
        return $this->data->getItems($type, $id, $context, $assign, $pagination);
    }

    /**
     * @param $data
     * @throws Exception
     */
    private function upd($data)
    {
        switch ($data['type']) {
            case 'config':
                parent::update(
                    array(
                        //'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }

    /**
     * Update data
     * @param $data
     * @throws Exception
     */
    private function add($data)
    {
        switch ($data['type']) {
            case 'newConfig':
                parent::insert(
                    array(
                        //'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }

    /**
     * @throws Exception
     */
    private function save(){
        $setData = $this->getItems('root',NULL,'one',false);
        $newData = array();
        
        $newData['url_ge'] = $this->apiGData['url_ge'];
        $newData['key_ge'] = $this->apiGData['key_ge'];

        if($setData['id_ge']){

            $this->upd(
                array(
                    'type' => 'config',
                    'data' => array(
                        'url_ge'     =>  $newData['url_ge'],
                        'key_ge'     =>  $newData['key_ge'],
                        'id'             =>  $setData['id_ge'],
                    )
                )
            );
        }else{
            $this->add(
                array(
                    'type' => 'newConfig',
                    'data' => array(
                        'url_ge'     =>  $newData['url_ge'],
                        'key_ge'     =>  $newData['key_ge']
                    )
                )
            );
        }

        $this->message->json_post_response(true, 'update');
    }
    /**
     * @return mixed
     */
    public function setItemData(){
        $setData = $this->getItems('root',NULL,'one',false);
        return $setData;
    }
    /**
    $dataApiGtranslate = $this->setItemData();
    $newData['url'] = $dataApiGtranslate['url_ge'];
    $newData['rapidApiKey'] = $dataApiGtranslate['key_ge'];
    $newData['debug'] = false;
    $newData['translate'] = array(
    'q'=>"hello the world",
    'source'=>"en",
    'target'=>"fr"
    );

    $setApi = $this->getApiRequest($newData);
    $parseJson = json_decode($setApi, true);
    $parseJson['data']['translations'][0]['translatedText'];
     * @param $data
     * @return bool|string|void
     */
    public function getApiRequest($data){
        try {

            $curl_params = array();
            $encodedAuth = $data['rapidApiKey'];
            $headers = array("X-RapidAPI-Key: " . $encodedAuth,'content-type: application/x-www-form-urlencoded','accept-encoding: application/gzip');

            $options = array(
                CURLOPT_RETURNTRANSFER  => true,
                CURLINFO_HEADER_OUT     => true,
                CURLOPT_URL             => $data['url'],
                CURLOPT_HTTPHEADER      => $headers,
                CURLOPT_CONNECTTIMEOUT  => 300,
                CURLOPT_TIMEOUT => 300,
                //CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CUSTOMREQUEST   => "POST",
                CURLOPT_POSTFIELDS      => http_build_query($data['translate']),
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER  => false
            );

            $ch = curl_init();
            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);
            $curlInfo = curl_getinfo($ch);
            curl_close($ch);
            if (array_key_exists('debug', $data) && $data['debug']) {
                var_dump($curlInfo);
                var_dump($response);
            }
            if ($curlInfo['http_code'] == '200') {
                if ($response) {
                    return $response;
                }
            }elseif($curlInfo['http_code'] == '0'){
                print 'Error HTTP: code 0';
                return;
            }

        }catch (Exception $e){
            $logger = new debug_logger(MP_LOG_DIR);
            $logger->log('php', 'error', 'An error has occured : ' . $e->getMessage(), debug_logger::LOG_MONTH);
        }
    }
    /**
     *
     */
    public function run(){
        if(isset($this->action)) {
            switch ($this->action) {
                case 'edit':
                    $this->save();
                    break;
            }
        }else{
            $data = $this->getItems('root',NULL,'one',false);
            $this->template->assign('apige', $data);
            $this->template->display('index.tpl');
        }
    }
}
