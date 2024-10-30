<?
/**
 * Bol_Plugin_Widget Class
 */
class Bol_Plugin_Widget extends WP_Widget {
    protected $AccessKey;
    protected $SecretAccessKey;
    protected $server;
    protected $port;
//    protected $keyword;
    protected $output;


    public function __construct($name = null) {
        $cfg = parse_ini_file(dirname(__FILE__).'/bol-keys.ini', true);
        $this->AccessKey = get_option('bol_access_key');
        $this->SecretAccessKey = get_option('bol_secret_key');
        $this->server = $cfg['server'];
        $this->port = $cfg['port'];
        $this->output = '';	

        parent::__construct(false, $name ? $name : 'bol.com Plugin Widget');
    }

  
    private function calculateSignature($AccessKey, $SecretAccessKey, $today, $method, $url, $contentType, $sessionId) {
        $stringToSign = "{$method}\n\n{$contentType}\n{$today}\nx-openapi-date:{$today}\n";
        if(!is_null($sessionId)) $stringToSign .= "x-openapi-session-id:".$sessionId."\n";
        $stringToSign .= $url;

        $signature = base64_encode(hash_hmac("SHA256", $stringToSign, $SecretAccessKey, true));

        return $AccessKey.":".$signature;
    }


    protected function doRequest($method, $server, $port, $url, $parameters, $AccessKey, $SecretAccessKey, $content, $sessionId) {
        $today = gmdate('D, d F Y H:i:s \G\M\T');

        if ($method == 'GET') $contentType = 'application/xml';
        elseif ($method == 'POST') $contentType = 'application/x-www-form-urlencoded';

        $headers = "{$method} {$url}{$parameters} HTTP/1.0
Content-type: {$contentType}
Host: {$server}
Content-length: ".strlen($content)."
Connection: close
X-OpenAPI-Authorization: ".$this->calculateSignature($AccessKey, $SecretAccessKey, $today, $method, $url, $contentType, $sessionId)."
X-OpenAPI-Date: {$today}
";
        if (!is_null($sessionId)) $headers .= "X-OpenAPI-Session-ID: {$sessionId}\r\n";
        $headers .= "\r\n";

        $socket = fsockopen('ssl://'.$server, $port, $errno, $errstr, 30);
        if (!$socket) echo "{$errstr} ({$errno})<br />\n";
        fputs($socket, $headers);
        fputs($socket, $content);

        $ret = "";
        while (!feof($socket)) $ret .= fgets($socket);
        fclose($socket);

        return $ret;
    }

    function getCategories($id = 0) {
        $this->output = $this->doRequest('GET', $this->server, $this->port, '/openapi/services/rest/catalog/v2/categorylists/'.$id, '', $this->AccessKey, $this->SecretAccessKey, '', null);
        // You can check for the right statuscode in the xml response, for now we will discard the statuscode

        $xml = strstr($this->output, '<?xml');

        $phpobject = simplexml_load_string($xml); 

        $categories = array();
        foreach($phpobject->category[0]->categoryRefinement as $item) {
            $categories[] = $item;
        }

        return $categories;
    }

    function getCategoriesAll() {
        $this->output = $this->doRequest('GET', $this->server, $this->port, '/openapi/services/rest/catalog/v2/categorylists/0', '', $this->AccessKey, $this->SecretAccessKey, '', null);
        // You can check for the right statuscode in the xml response, for now we will discard the statuscode

        $xml = strstr($this->output, '<?xml');

        $phpobject = simplexml_load_string($xml);

        $categories = array();
        foreach($phpobject->category[0]->categoryRefinement as $item) {
            $sub = $this->getCategories($item->id);
            $categories[] = array($item, $sub);
        }

        return $categories;
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        return $instance;
    }                                

    /** @see WP_Widget::form */
    function form($instance) {
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget title:'); ?></label><br />
            <input type="text" id="<?=$this->get_field_id('title')?>" name="<?=$this->get_field_name('title')?>" value="<?=$instance['title']?>"/>
        </p>
    <?
    }

    
    function  getBolWidgetJS($atts, $products = array()) {

        $scriptId = str_replace('.css', '', $atts['css_file']);
        $result = '<div id="'.$scriptId.'"><script type="text/javascript">var bol_pml={"id":"'.$scriptId.'","secure":false,"baseUrl":"partnerprogramma.bol.com","urlPrefix":"http://aai.bol.com/aai"';
        $additional = '';
        $scriptname = '';

        if ($atts['type'] == 'list') {
            $result .= ',"productId":"';
            foreach ($products as $item) {
                $result .= 'productid='.$item->id.'&';
            }
            $result .= '"';
            $scriptname = "clientProductlink";
        } elseif ($atts['type'] == 'bestsellers') {
            $result .= ', "catID":"'.$atts['category'].'", "nrProducts":"'.$atts['limit'].'", '. 
                '"title":""';
            $scriptname = "clientBestsellerGenerator";
            if ($atts['bolheader'] == "true")
            {
                $result .= ', "header":true, "logoColor":"black"';
            }
            else
            {
                $result .= ', "header":false';
            }
            if ($atts['price_range'] > 0)
            {
                $result .= ', "priceRangeId":"'.$atts['price_range'].'"';
            }
        } elseif ($atts['type'] == 'search') {

            $result = str_replace("var bol_pml=", "var bol_pml_search=", $result);
            $categories = $this->getCategories();
            $result .= ', "showCat":'.($atts['showcat'] == 'true' ? 'true' : 'false').',"catId":"'.$atts['category'].'", '.
                '"searchFor":"'.$atts['default'].'", "default_results":"'.$atts['limit'].'"';
            $tmp = array();
            foreach ($categories as $item) {
                $tmp[] = "{'description':'".$item->name."', 'id':'".$item->id."'}";
            }
            $additional = "var bol_cats={'cats':[".implode(",", $tmp)."]}";
            $scriptname = "clientSearchBoxGenerator";
            if ($atts['bolheader'] == "true")
            {
                $result .= ', "header":true, "logoColor":"black"';
            }
            else
            {
                $result .= ', "header":false';
            }
        }

        $result .= ', "site_id": "'.get_option("bol_site_id").'","target":'.$atts['target'].',"rating":'.$atts['rating'].',"price":'.$atts['price'].', '.
            '"link_name":"'.$atts['name'].'","link_subid":"'.$atts['subid'].'", "title":"'.$atts['title'].'", '.
            '"image_size":'.$atts['image_size'].',"image_position":"'.$atts['image_position'].'","width":"'.$atts['width'].'","cols":"'.$atts['cols'].'", '.
            '"background_color":"'.$atts['background_color'].'","text_color":"'.$atts['text_color'].'","link_color":"'.$atts['link_color'].'","border_color":"'.$atts['border_color'].'", '.
            '"letter_type":"verdana","letter_size":"11"};'.$additional.'</script> '.
            '<script id="'.$scriptId.'" src="http://partnerprogramma.bol.com/partner/static/js/aai/'.$scriptname.'.js" type="text/javascript"></script>
             </div>';

        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'].'/bol-css/';
        $upload_dir = $upload_dir['baseurl'].'/bol-css/';

        if (!empty($atts['css_file']) && strpos($atts['css_file'], '.css') <0)
        {
            $atts['css_file'] .= '.css';
        }

        if (!empty($atts['css_file']) && file_exists($upload_path.$atts['css_file']))
        {
            $result .= '<link rel="stylesheet" media="screen" type="text/css" href="'.$upload_dir.$atts['css_file'].'" />';
        }

        return $result;
    }


    function getProductsHtml($products) {
        $result = '<ul class="productlist">';

        foreach ($products as $item) {
            if (!@GetImageSize($item->thumbnailUrl)) $item->thumbnailUrl = "http://www.bol.com/nl/static/images/main/noimage_124x100default.gif";
            $item->url = 

            $result .= '<li>
                <input type="hidden" name="id" value="'.$item->id.'"/>
                <a class="product" href="'.$item->externalUrl.'">
                <span class="imageBox"><img alt="'.$item->title.'" src="'.$item->thumbnailUrl.'"></span>
                <span class="productName">'.$item->title.'</span><br/>
                <span class="sectionName">'.$item->section.'</span>';

            if ($item->listPrice) $result .= ' <span class="oldprice">&euro;'.number_format((double)$item->listPrice, 2, '.', '').'</span>';

            $result .= ' <span class="price">&euro;'.number_format((double)$item->price, 2, '.','').'</span><br/>';

            if ($item->rating) {
                $nicerating = substr($item->rating, 0, 1); 
                $nicerating .= '_'.substr($item->rating, -1); 
                $altrating = str_replace("_", ".",$nicerating);
                $result .= '<span class="rating"><img alt="Score '.$altrating.' van de 5 sterren" src="http://review.bol.com/7628-nl_nl/'.$nicerating.'/5/rating.gif"></span>';
            }

            $result .= '</a></li>';
        }

        $result.= '</ul>'; 

      return $result;
    }


    protected function get_function_id($name) {
        $tmp = $this->get_field_id($name);
        return str_replace("-", "_", $tmp);
    }


} // class Bol_Plugin_Widget
