<?php
/*
Plugin Name: Bol.com plugin for Wordpress
Description: You can add products on your website of Bol.com products tagged with your associate id.
Author: Daxx
Version: 1.0
*/


include_once('bol-plugin.class.php');
include_once('bol-plugin-widget.php');
include_once('bol-plugin-widget-search.php');
include_once('bol-plugin-widget-bestsellers.php');
include_once('bol-plugin-widget-selected.php');

$my_plugin = new bol_plugin_base();

$folder = plugin_basename(dirname(__FILE__));

$path_to_php_file_plugin = $folder.'/bol-plugin.php';

$my_plugin->page_title = 'bol.com plugin';
$my_plugin->menu_title = 'bol.com plugin (menu)'; 
$my_plugin->short_description = 'bol.com plugin description';
$my_plugin->access_level = 5;
$my_plugin->add_page_to = 1;

wp_enqueue_script('jquery', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)) . '/tinymce/bol/js/jquery-1.4.2.min.js', '1.4.2' );
wp_enqueue_script('jquery-ui', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)) . '/tinymce/bol/js/jquery-ui-1.8.13.custom.min.js', array('jquery'), '1.8.13', true );
wp_enqueue_script('jquery-ui-dialog', false, array('jquery'), false, true);
wp_enqueue_script('jquery-ui-tabs', false, array('jquery'), false, true);
wp_enqueue_script('colorpicker', false, array('jquery'), false, true);

wp_enqueue_script('bol-frontend', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)) . '/tinymce/bol/js/bol.frontend.js');

wp_enqueue_style("bol.css", plugins_url("bol.css", __FILE__));
wp_enqueue_style("jquery-ui", WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)) . '/tinymce/bol/css/jquery-ui-1.8.13.custom.css');

add_action('admin_menu', array($my_plugin, 'add_admin_menu'));
add_action('deactivate_' . $path_to_php_file_plugin, array($my_plugin, 'deactivate'));
add_action('activate_' . $path_to_php_file_plugin, array($my_plugin, 'activate'));

add_action('admin_notices', array(&$my_plugin, 'displayErrorMessage'));

add_action('widgets_init', create_function('', 'return register_widget("Bol_Plugin_Widget_Search");'));
add_action('widgets_init', create_function('', 'return register_widget("Bol_Plugin_Widget_Bestsellers");'));
add_action('widgets_init', create_function('', 'return register_widget("Bol_Plugin_Widget_Selected");'));

add_action('wp_print_scripts', 'my_deregister_javascript', 100 );

$my_plugin->init();


function my_deregister_javascript() {
	if ( !is_page(‘events’) ) {
		wp_deregister_script( 'jquery-ui' );
		wp_deregister_script( 'jquery.css' );
	}
}

?>