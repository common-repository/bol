<?
    define('WP_USE_THEMES', false);
    require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

    $instance = current(current($_POST));

    $instance['rating'] = ($instance['rating'])?"true":"false";
    $instance['price'] = ($instance['price'])?"true":"false";
    $instance['bolheader'] = ($instance['bolheader'])?"true":"false";
    $instance['background_color'] = '#'.$instance['background_color'];
    $instance['text_color'] = '#'.$instance['text_color'];
    $instance['link_color'] = '#'.$instance['link_color'];
    $instance['border_color'] = '#'.$instance['border_color'];
    $instance['css_file'] = 'bol_'.time();
    $products = array();
    
    switch ($instance['filename'])
    {
        case 'bol_previewBestsellers':
            $instance['type'] = 'bestsellers';

            $ctg = $instance['category'];
            if ($instance['ddlBolSub3Category'])
            {
                $instance['category'] = $instance['ddlBolSub3Category'];
            }
            elseif ($instance['ddlBolSub2Category'])
            {
                $instance['category'] = $instance['ddlBolSub2Category'];
            }elseif ($instance['ddlBolSubCategory'])
            {
                $instance['category'] = $instance['ddlBolSubCategory'];
            }
            break;
        case 'clientSearchBoxGenerator':
            $instance['type'] = 'search';
            $instance['default'] = $instance['search'];
            break;
        case 'clientProductlink':
            $instance['type'] = 'list';
            $productIds = explode(",", $instance['products']);
            $products = array();
            foreach ($productIds as $id) {
                 $products[] = (object)array('id' => $id);
            }
            $instance['category'] = 0;
            break;
    }

    $w = new Bol_Plugin_Widget_Selected();
    $html = $w->getBolWidgetJS($instance, $products);
    $html = str_replace('script', '[script]', $html);
    $html .= '<style>'.$instance['cssstyle'].'</style>';
    echo $html;
?>