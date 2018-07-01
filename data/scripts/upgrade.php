<?php
namespace CSSEditor;

$services = $serviceLocator;
/** @var \Doctrine\DBAL\Connection $connection */
$connection = $serviceLocator->get('Omeka\Connection');
/** @var \Omeka\Settings\Settings $settings */
$settings = $serviceLocator->get('Omeka\Settings');
$config = require dirname(dirname(__DIR__)) . '/config/module.config.php';

if (version_compare($oldVersion, '3.0.1', '<')) {
    $css = $settings->get('css_editor_css');
    if ($css) {
        $settings->set('csseditor_css', $css);
    }
    $settings->delete('css_editor_css');

    $sql = <<<SQL
UPDATE site_setting SET id = "csseditor_css" WHERE `id` = "css_editor_css";
SQL;
    $connection->exec($sql);
}
