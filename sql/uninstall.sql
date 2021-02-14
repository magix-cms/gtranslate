TRUNCATE TABLE `mc_gtranslate`;
DROP TABLE `mc_gtranslate`;

DELETE FROM `mc_admin_access` WHERE `id_module` IN (
    SELECT `id_module` FROM `mc_module` as m WHERE m.name = 'gtranslate'
);