<?php
if (!empty($_POST['cssstyle1'])) {
    define('WP_USE_THEMES', false);
    require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

    $upload_dir = wp_upload_dir();
    $upload_dir = $upload_dir['basedir'].'/bol-css/';
    $filename = $_POST['filename'];
    $cssStyles = '';
    if (!file_exists($upload_dir))
    {
        mkdir($upload_dir);
    }
    $f = fopen($upload_dir.$filename, "w+");
    fwrite($f, $_POST['cssstyle1']);
    fclose($f);
}
?>