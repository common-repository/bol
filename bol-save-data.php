<?php
require_once '../../../wp-load.php';
$widgets = array('widget_bol_plugin_selected', 'widget_bol_plugin_bestsellers', 'widget_bol_plugin_search');

if (!empty($_POST['widget']) && in_array($_POST['widget'], $widgets)) {
    $widget_name = $_POST['widget'];
    unset($_POST['widget']);

    $settings = get_option($widget_name);
    $max = 0;
    foreach ($settings as $key => $stack) {
        if (is_int($key) && $key > $max) {
            $max = $key;
        }
    }

    $settings[$max] = array_merge($settings[$max], $_POST);
    update_option($widget_name, $settings);
    echo 'success';
}

 
