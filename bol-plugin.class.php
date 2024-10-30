<?php
include_once("Request.class.php");

class bol_plugin_base
{
	var $page_title;
	var $menu_title;
	var $access_level;
	var $add_page_to;
	var $short_description;
 
	function bol_plugin_base()
	{
		$this->get_options();
	}
 
	function get_options()
	{
	}
 
	function add_admin_menu()
	{
        add_menu_page("WP - bol.com", "WP - bol.com", $this->access_level, "bol_config", array(&$this, 'configPage'));
	}

	function activate()
	{
	}
 
	function deactivate()
	{
	}
 
	function admin_page()
	{

    }

    /**
     * output Main Settings Page
     */
    public function configPage() {
        if( isset( $_POST["bol_config_edit"]) ) {
                //update site id
                if (isset($_POST['SiteID'])) {
                    update_option("bol_site_id", $_POST['SiteID']);
                }
                if (isset($_POST['key'])) {
                    update_option("bol_access_key", $_POST['key']);
                }
                if (isset($_POST['secret_key'])) {
                    update_option("bol_secret_key", $_POST['secret_key']);
                }

                $this->message = "<strong>Success:</strong> Settings successfully updated.";
        }
?>
<div class="wrap">
    <h2>BOL Plugin settings:</h2>
    <div class="postbox-container" style="width:500px;" >
        <div class="metabox-holder">
            <div>
                <?= $this->message ?>
            </div>
            <div class="meta-box-sortables">
                <form action="<?php echo admin_url('admin.php?page=bol_config') ?>" method="post" id="bol-conf">
                    <input value="bol_config_edit" type="hidden" name="bol_config_edit" />
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 150px">Site ID:</td>
                            <td><input type="text" name="SiteID" value="<?=get_option("bol_site_id")?>"/></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top">Access Key:</td>
                            <td><input type="text" name="key" value="<?=get_option("bol_access_key")?>" style="width: 260px"/><br />
                                <small>If you don't have a key please contact <a href="mailto:partnerprogramma@bol.com">partnerprogramma@bol.com</a> to receive your Api Key</small></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top">API Secret Key:</td>
                            <td><input type="text" name="secret_key" value="<?=get_option("bol_secret_key")?>" style="width: 260px"/><br /></td>
                        </tr>
                        <tr>
                            <td><input class="button-primary" type="submit" name="submit" value="Update Settings &raquo;" /></td>
                            <td></td>
                        </tr>
                    </table>

                </form>
            </div>
        </div>
    </div>
</div>
<?
    }


    function init() {
        add_filter("mce_external_plugins", array(&$this,"register_bol_tinymce_plugin"));
        add_filter('mce_buttons', array(&$this,'register_bol_button'));
        add_shortcode('bol_product', array(&$this,'bolProductHandler'));
        add_shortcode('bol_bestsellers', array(&$this,'bolBestsellersHandler'));
        add_shortcode('bol_search', array(&$this,'bolSearchHandler'));
    }
 
    public function bolProductHandler( $atts, $content=null, $code="" ) {
        $atts = shortcode_atts( array( 
                'type' => 'list', 
                'id' => null, 
                'name' => null, 
                'subid' => null,
                'background_color' => '#FFFFFF', 
                'text_color' => '#CB0100', 
                'link_color' => '#0000FF', 
                'border_color' => '#D2D2D2', 
                'width' => '250', 
                'cols' => '1', 
                'rating' => 'true', 
                'price' => 'true',
                'bolheader' => 'true',
                'css_file' => '',
                'price_range' => '',
                'target' => 'true', 
                'image_size' => 'true', 
                'image_position' => 'left',
            ), $atts );

        $obj = new Bol_Plugin_Widget_Selected();

        $productIds = explode(",", $atts['id']);
        $products = array();
        foreach ($productIds as $id) {
            if ($id) {
                $tmp = $obj->getProduct($id);
                $products[] = $tmp->product;
            }
        }
        $html = $obj->getBolWidgetJS($atts, $products);

        return $html;
    }

    public function bolBestsellersHandler( $atts, $content=null, $code="" ) {
        $atts = shortcode_atts( array( 
                'type' => 'bestsellers', 
                'category' => null, 
                'limit' => null, 
                'name' => null, 
                'subid' => null,
                'title' => '',
                'background_color' => '#FFFFFF', 
                'text_color' => '#CB0100', 
                'link_color' => '#0000FF', 
                'border_color' => '#D2D2D2', 
                'width' => '250', 
                'cols' => '1', 
                'rating' => 'true', 
                'price' => 'true',
                'bolheader' => 'true',
                'css_file' => '',
                'price_range' => '',
                'target' => 'true', 
                'image_size' => 'true', 
                'image_position' => 'left',
            ), $atts );
        $obj = new Bol_Plugin_Widget_Bestsellers();
        $products = array();
        $html = $obj->getBolWidgetJS($atts);

        return $html;
    }

    public function bolSearchHandler( $atts, $content=null, $code="" ) {

        $atts = shortcode_atts( array( 
                'type' => 'search', 
                'category' => null, 
                'default' => null,
                'showcat' => false,
                'limit' => null, 
                'name' => null, 
                'subid' => null,
                'background_color' => '#FFFFFF', 
                'text_color' => '#CB0100', 
                'link_color' => '#0000FF', 
                'border_color' => '#D2D2D2', 
                'width' => '250', 
                'cols' => '1', 
                'rating' => 'true', 
                'price' => 'true',
                'bolheader' => 'true',
                'css_file' => '',
                'price_range' => '',
                'target' => 'true', 
                'image_size' => 'true', 
                'image_position' => 'left',
            ), $atts );
        $obj = new Bol_Plugin_Widget_Search();

        $html = $obj->getBolWidgetJS($atts);

        return $html;
    }

    public function register_bol_tinymce_plugin($plugin_array) {
        $plugin_array['bolproductlink'] = get_bloginfo('wpurl') . '/' . PLUGINDIR . '/bol/tinymce/bol/editor_plugin.js';
        return $plugin_array;
    }

    public function register_bol_button($buttons) {
        array_push($buttons, "separator", "bolproductlink");
        return $buttons;
    }

    function displayErrorMessage() {
        if (!get_option("bol_site_id")) {
            echo '<div class="updated fade">WordPress bol.com Plugin: Plugin is not configured! Please correct in the <a href="' . admin_url('admin.php?page=bol_config') . '" target="_self">settings page</a></div>';
        }
    }
} // class
?>