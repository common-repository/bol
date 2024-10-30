<?
/**
 * Bol_Plugin_Widget_Selected Class
 */
class Bol_Plugin_Widget_Selected extends Bol_Plugin_Widget {

    private $keyword;

    /** constructor */
    function __construct() {
        parent::__construct('bol.com Selected products Widget');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        
        $productIds = explode(",", $instance['products']);
        $products = array();
        foreach ($productIds as $id) {

            $products[] = (object)array('id' => $id);
        }

        if (!$instance['css_file']) $instance['css_file'] = 'bol_'. time() .'_'.'links.css';

        $instance['type'] = 'list';
        $instance['rating'] = ($instance['rating'])?"true":"false";
        $instance['price'] = ($instance['price'])?"true":"false";
        $instance['bolheader'] = ($instance['bolheader'])?"true":"false";
        $instance['background_color'] = '#'.$instance['background_color'];
        $instance['text_color'] = '#'.$instance['text_color'];
        $instance['link_color'] = '#'.$instance['link_color'];
        $instance['border_color'] = '#'.$instance['border_color'];
        $instance['category'] = 0;

        $html = $this->getBolWidgetJS($instance, $products);

        $title = apply_filters('widget_title', $instance['title']);

        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title ) echo $before_title . $title . $after_title; ?>
                    <?php echo $html; ?>
                    <style><?php echo $instance['cssstyle'];?></style>
              <?php echo $after_widget; ?>
        <?php
    }


    /** @see WP_Widget::form */
    function form($instance) {
        parent::form($instance);
        ?>
            <a href="javascript: bol_openPopupSelected()" id="<?=$this->get_field_id('selectProductsLink')?>">Widget settings...</a><br/><br/>
            <div id="bol-selected-products-widget-popup"></div>
        <?php
    }

    function getProduct($productId) {

        $this->output = $this->doRequest('GET', $this->server, $this->port, '/openapi/services/rest/catalog/v2/products/'.$productId, '', $this->AccessKey, $this->SecretAccessKey, '', null);
        // You can check for the right statuscode in the xml response, for now we will discard the statuscode

        $xml =  strstr($this->output, '<?xml');
        $phpobject = simplexml_load_string($xml); 

        return $phpobject;
    }


    function getCategories() {
        $this->output = $this->doRequest('GET', $this->server, $this->port, '/openapi/services/rest/catalog/v2/categorylists/0', '', $this->AccessKey, $this->SecretAccessKey, '', null);
        // You can check for the right statuscode in the xml response, for now we will discard the statuscode

        $xml = strstr($this->output, '<?xml');

        $phpobject = simplexml_load_string($xml); 

        $categories = array();
        foreach($phpobject->category[0]->categoryRefinement as $item) {
            $categories[] = $item;
        }

        return $categories;
    }

} // class Bol_Plugin_Widget_Selected

function Bol_Plugin_Widget_Selected_init(){
	register_widget('Bol_Plugin_Widget_Selected');
}
add_action('widgets_init', 'Bol_Plugin_Widget_Selected_init');

function Bol_Plugin_Widget_Selected_js() {
	$currentUrl = $_SERVER["PHP_SELF"];
	$fileUrl = explode('/', $currentUrl);
	$fileName = $fileUrl[count($fileUrl) - 1];

	if($fileName == 'widgets.php'){ ?>
        <script type="text/javascript">
            function bol_openPopupSelected() {

                jQuery("#bol-selected-products-widget-popup").html('<div id="dvPopupDialog" class="bol-popup-dialog"><iframe src="<?php echo home_url();?>/wp-content/plugins/bol/tinymce/bol/bol-product-link.php?widget=widget_bol_plugin_selected"></iframe></div>');
                jQuery("#dvPopupDialog").dialog({
                    title: "bol.com Selected products widget settings",
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
add_action('admin_head','Bol_Plugin_Widget_Selected_js');