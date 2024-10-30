<?
/**
 * Bol_Plugin_Widget_Bestsellers Class
 */
class Bol_Plugin_Widget_Bestsellers extends Bol_Plugin_Widget {

    private $keyword;

    /** constructor */
    function __construct() {
        parent::__construct('bol.com Bestsellers Widget');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );

        $instance['type'] = 'bestsellers';
        $instance['rating'] = ($instance['rating'])?"true":"false";
        $instance['price'] = ($instance['price'])?"true":"false";
        $instance['bolheader'] = ($instance['bolheader'])?"true":"false";
        $instance['background_color'] = '#'.$instance['background_color'];
        $instance['text_color'] = '#'.$instance['text_color'];
        $instance['link_color'] = '#'.$instance['link_color'];
        $instance['border_color'] = '#'.$instance['border_color'];

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

        $html = $this->getBolWidgetJS($instance);
        $title = apply_filters('widget_title', $instance['title']);
        ?>

              <?php echo $before_widget; ?>
                  <?php if ( $title ) echo $before_title . $title . $after_title; ?>

                    <?php echo $html; ?>
<style><?php echo $instance['cssstyle'];?></style>
              <?php echo $after_widget; ?>
        <?php

        $instance['category'] = $ctg;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance = parent::update($new_instance, $old_instance);
        $instance['limit'] = (int)strip_tags($new_instance['limit']);
        $instance['category'] = strip_tags($new_instance['category']);
        $instance['name'] = strip_tags($new_instance['name']);
        $instance['subid'] = strip_tags($new_instance['subid']);
        $instance['background_color'] = strip_tags($new_instance['background_color']);
        $instance['text_color'] = strip_tags($new_instance['text_color']);
        $instance['link_color'] = strip_tags($new_instance['link_color']);
        $instance['border_color'] = strip_tags($new_instance['border_color']);
        $instance['width'] = strip_tags($new_instance['width']);
        $instance['cols'] = strip_tags($new_instance['cols']);
        $instance['rating'] = strip_tags($new_instance['rating']);
        $instance['price'] = strip_tags($new_instance['price']);
        $instance['bolheader'] = strip_tags($new_instance['bolheader']);
        $instance['target'] = strip_tags($new_instance['target']);
        $instance['image_size'] = strip_tags($new_instance['image_size']);
        $instance['image_position'] = strip_tags($new_instance['image_position']);
        $instance['cssstyle'] = strip_tags($new_instance['cssstyle']);
        $instance['type'] = 'bestsellers';
        $instance['css_file'] = 'bol_'.time();
        $instance['showcss'] = strip_tags($new_instance['showcss']);
        $instance['ddlBolSubCategory'] = strip_tags($new_instance['ddlBolSubCategory']);
        $instance['ddlBolSub2Category'] = strip_tags($new_instance['ddlBolSub2Category']);
        $instance['ddlBolSub3Category'] = strip_tags($new_instance['ddlBolSub3Category']);

        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        parent::form($instance); ?>
            <a href="javascript: bol_openPopupBestsellers()" id="<?=$this->get_field_id('selectBestsellers')?>">Widget settings...</a><br/><br/>
            <div id="bol-bestsellers-widget-popup"></div>
        <?php

    }

    function getBestsellers($categoryId, $limit="") {
        if (!$limit) $limit = 5;

        $this->output = $this->doRequest('GET', $this->server, $this->port, '/openapi/services/rest/catalog/v2/productlists/toplist_default/'.$categoryId, '?ofset=0&nrProducts='.$limit.'&output=product&sortingMethod=sales_ranking&sortingAscending=false', $this->AccessKey, $this->SecretAccessKey, '', null);
        // You can check for the right statuscode in the xml response, for now we will discard the statuscode

        $xml =  strstr($this->output, '<?xml');
        $phpobject = simplexml_load_string($xml); 

        $products = array();
        foreach($phpobject->product as $item) {
            $products[] = $item;
        }

        return $products;
    }


} // class Bol_Plugin_Widget

function Bol_Plugin_Widget_Bestsellers_init(){
	register_widget('Bol_Plugin_Widget_Bestsellers');
}
add_action('widgets_init', 'Bol_Plugin_Widget_Bestsellers_init');

function Bol_Plugin_Widget_Bestsellers_js(){
	$currentUrl = $_SERVER["PHP_SELF"];
	$fileUrl = explode('/', $currentUrl);
	$fileName = $fileUrl[count($fileUrl) - 1];

	if($fileName == 'widgets.php'){ ?>
        <script type="text/javascript">
            function bol_openPopupBestsellers() {

                jQuery("#bol-bestsellers-widget-popup").html('<div id="dvPopupDialog" class="bol-popup-dialog"><iframe src="<?php echo home_url();?>/wp-content/plugins/bol/tinymce/bol/bol-bestsellers.php?widget=widget_bol_plugin_bestsellers"></iframe></div>');
                jQuery("#dvPopupDialog").dialog({
                    title: "bol.com Bestsellers widget settings",
                    autoOpen: true,
                    modal: true,
                    resizable: false,
                    width: "auto",
                    close: function() {
                        jQuery("#dvPopupDialog").dialog('destroy').remove();
                    }
                });

            }
        </script>
	<?}
}
add_action('admin_head','Bol_Plugin_Widget_Bestsellers_js');