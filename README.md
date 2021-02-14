# Google Translate
Plugin Google translate with Rapid API (Translate V2) for [magixcms 3](https://www.magix-cms.com)

![1017853ad6caff0e4486825248299cad](https://user-images.githubusercontent.com/356674/106892769-d5c98180-66ec-11eb-975a-8665a0271fda.png)

### version 

[![release](https://img.shields.io/github/release/magix-cms/gtranslate.svg)](https://github.com/magix-cms/gtranslate/releases/latest)

Authors
-------

* Gerits Aurelien (aurelien[at]magix-cms[point]com)

## Description
Connexion à l'API Google Translate sur Rapid API pour vous permettre de développer vos propres outils

## Installation
 * Décompresser l'archive dans le dossier "plugins" de magix cms
 * Connectez-vous dans l'administration de votre site internet
 * Cliquer sur l'onglet plugins du menu déroulant pour sélectionner Google Translate.
 * Une fois dans le plugin, laisser faire l'auto installation
 * Il ne reste que la configuration du plugin pour correspondre avec vos données.

Requirements
   ------------
   * CURL (http://php.net/manual/en/book.curl.php)
   
### Exemple d'utilisation dans vos plugins

```php
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
````
Ressources
 -----
  * https://rapidapi.com/googlecloud/api/google-translate1
  * https://www.magix-cms.com
