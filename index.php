<?php
/**
 * Plugin Name: MBrasil - INIT
 * Plugin URI: http://www.marciobrasil.net.br
 * Description: MBrasil INIT insert personal snippets in project.
 * Version: 1.1.1
 * Author: Márcio Brasil
 * Author URI: http://www.marciobrasil.net.br
 */

require_once dirname(__FILE__) . '/inc/options-page.php';

add_action('init', 'mbrasil_init');

function mbrasil_init()
{
    $plugin_dir = plugin_dir_path(__DIR__);
    if (!file_exists($plugin_dir . "mbrasil-snippets")) {
        mkdir($plugin_dir . "mbrasil-snippets", 0755);
    }

    $root_files = glob($plugin_dir . 'mbrasil-snippets/*.php');
    foreach ($root_files as $file) {
        require $file;
    }

    $root_folders = glob($plugin_dir . 'mbrasil-snippets/*', GLOB_ONLYDIR);
    foreach ($root_folders as $dir) {

        getSubSubFolder($dir);

        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            require $file;
        }

    }
}

function getSubSubFolder($dir)
{

    $root_folders = glob($dir . '/*', GLOB_ONLYDIR);
    foreach ($root_folders as $dir) {
        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            require $file;
        }

    }

}