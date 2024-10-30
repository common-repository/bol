<?
/**
 * Bol_Search ajax function
 */

function calculateSignature($AccessKey, $SecretAccessKey, $today, $method, $url, $contentType, $sessionId) {
    $stringToSign = "{$method}\n\n{$contentType}\n{$today}\nx-openapi-date:{$today}\n";
    if(!is_null($sessionId)) $stringToSign .= "x-openapi-session-id:".$sessionId."\n";
    $stringToSign .= $url;

    $signature = base64_encode(hash_hmac("SHA256", $stringToSign, $SecretAccessKey, true));

    return $AccessKey.":".$signature;
}


function doRequest($method, $server, $port, $url, $parameters, $AccessKey, $SecretAccessKey, $content, $sessionId) {
    $today = gmdate('D, d F Y H:i:s \G\M\T');

    if ($method == 'GET') $contentType = 'application/xml';
    elseif ($method == 'POST') $contentType = 'application/x-www-form-urlencoded';

    $headers = "{$method} {$url}{$parameters} HTTP/1.0
Content-type: {$contentType}
Host: {$server}
Content-length: ".strlen($content)."
Connection: close
X-OpenAPI-Authorization: ".calculateSignature($AccessKey, $SecretAccessKey, $today, $method, $url, $contentType, $sessionId)."
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

    
function getCategories($id) {
    $categories = array();
    $id = str_replace(' ', '+', $id);

    $root_categories = getCategoryXml($id);

    switch ($id) {
        case 8299: // Boeken
            foreach ($root_categories->category as $filter) {
                if ((string)$filter->name == 'taal') {
                    foreach ($filter->categoryRefinement as $subcategory) {
                    	// check if item is a Dutch Book
                        if (8293 == (int)$subcategory->id ) {
                            $combine = (string) $root_categories->category[0]->categoryRefinement[0]->id;
                        } else {
                            $combine = (string) $root_categories->category[0]->categoryRefinement[1]->id;
                        }
                        $categories[$combine.'+'.(string)$subcategory->id] = (string) $subcategory->name;
                    }
                }
            }
            break;
        default:
            foreach ($root_categories->category as $filter) {
                if ((string)$filter->name == 'categorieen') {
                    foreach ($filter->categoryRefinement as $item) {
                        $categories[(string) $item->id] = (string) $item->name;
                    }
                }
            }
            break;
    }


    // Render options
    $options = '';
    foreach ($categories as $id => $name) {
        $options .= "<option value='".$id."'>".$name."</option>";
    }
    return $options;
}

function getSelectedCategories() {
    $categories = array();

    $root_categories = getCategoryXml();
    foreach($root_categories->category[0]->categoryRefinement as $root_item) {
        switch ((string)$root_item->name) {
            case 'Boeken':
                $children = getCategoryXml($root_item->id);
                foreach ($children->category as $filter) {
                    if ((string)$filter->name == 'taal') {
                        foreach ($filter->categoryRefinement as $subcategory) {
                            if ((string)$subcategory->name == 'Nederlandse boeken') {
                                $combine = (string) $children->category[0]->categoryRefinement[0]->id;
                            } else {
                                $combine = (string) $children->category[0]->categoryRefinement[1]->id;
                            }
                            $categories[$combine.'+'.(string)$subcategory->id] = (string) $subcategory->name;
                        }
                    }
                }
                break;
            case 'Computer':
            case 'Elektronica':
                $children = getCategoryXml($root_item->id);
                foreach ($children->category[0]->categoryRefinement as $subcategory) {
                    $categories[(string)$subcategory->id] = (string) $subcategory->name;
                }
                break;
            default:
                $categories[(string)$root_item->id] = (string)$root_item->name;
                break;
        }
    }

    // Render options
    $options = '';
    foreach ($categories as $id => $name) {
        $options .= "<option value='".$id."'>".$name."</option>";
    }
    return $options;
}

function getCategoryXml($parent_id = 0) {
    global $cfg;
    $tmp = doRequest('GET', $cfg['server'], $cfg['port'], '/openapi/services/rest/catalog/v2/categorylists/'.$parent_id, '', $cfg['AccessKey'], $cfg['SecretAccessKey'], '', null);
    $xml = strstr($tmp, '<?xml');
    return simplexml_load_string($xml);
}

function getSearch($string, $categoryId = 0, $limit = null, $offset = 0) {
    global $cfg;
    if (!$limit) $limit = 100;

    // searching
    if ($string) $output = doRequest('GET', $cfg['server'], $cfg['port'], '/openapi/services/rest/catalog/v2/searchproducts/'.urlencode($string), '?offset='.$offset.'&nrProducts='.$limit.'&output=product&'.($categoryId ? "categoryId=".$categoryId : ""), $cfg['AccessKey'], $cfg['SecretAccessKey'], '', null);
    // if just category list
    elseif ($category) $output = doRequest('GET', $cfg['server'], $cfg['port'], '/openapi/services/rest/catalog/v2/productlists/toplist_default/'.$category, '?ofset='.$offset.'&nrProducts='.$limit.'&output=product&sortingMethod=sales_ranking&sortingAscending=false', $cfg['AccessKey'], $cfg['SecretAccessKey'], '', null);
    // nothing
    else return false;

    $xml =  strstr($output, '<?xml');
    $phpobject = simplexml_load_string($xml); 

    $products = array();
    foreach($phpobject->product as $item) {
        $products[] = $item;
    }

    $html = getProductsHtml($products);

    return $html;
}

function getProducts($ids) {
    $productIds = explode(",", $ids);
    $products = array();
    foreach ($productIds as $id) {
        if ($id) {
            $tmp = getProduct($id);
            $products[] = $tmp->product;
        }
    }
    return getProductsHtml($products);
}

function getProduct($productId) {
    global $cfg;
    
    $tmp = doRequest('GET', $cfg['server'], $cfg['port'], '/openapi/services/rest/catalog/v2/products/'.$productId, '', $cfg['AccessKey'], $cfg['SecretAccessKey'], '', null);

    $xml =  strstr($tmp, '<?xml');
    $phpobject = simplexml_load_string($xml); 


    return $phpobject;
}

function getProductsHtml($products) {
    $perPage = 5;
    $result = '<ul class="productlist" id="productlist">';
    $pager = ceil( count($products) / $perPage );

    foreach ($products as $item) {
        if (!@GetImageSize($item->thumbnailUrl)) $item->thumbnailUrl = "http://www.bol.com/nl/static/images/main/noimage_124x100default.gif";

        $result .= '<li class="item"><div>
            <input type="hidden" name="id" value="'.$item->id.'"/>
            <a class="product" href="'.$item->externalUrl.'" target="_blank">
            <span class="imageBox"><img alt="'.$item->title.'" src="'.$item->thumbnailUrl.'"></span>
            <span class="productName">'.$item->title.'</span><br/>';

        $result .= ' <span class="price">&euro;'.number_format((double)$item->price, 2, '.','').'</span><br/>';

        if ($item->rating) {
            $nicerating = substr($item->rating, 0, 1); 
            $nicerating .= '_'.substr($item->rating, -1); 
            $altrating = str_replace("_", ".",$nicerating);
            $result .= '<span class="rating"><img alt="Score '.$altrating.' van de 5 sterren" src="http://review.bol.com/7628-nl_nl/'.$nicerating.'/5/rating.gif"></span>';
        }
        $result .= '</a></div></li>';
    }

    $result.= '</ul>';
    if (count($products) > $perPage)
    {
        $result.= '
<div class="toolbar">
    <div class="pager">
        <div class="pages">
            <ul>
                <li class="current">1</li>';
 for ($i = 2; $i <= $pager; $i++)
 {
     $result.= '<li><a href="#">'.$i.'</a></li>';
 }

 $result.= '<li><a title="vorige" href="#" class="previous disable">vorige</a></li>
                <li><a title="volgende" href="#" class="next i-next">volgende</a></li>
            </ul>
        </div>
    </div>
</div>

    <script>
        var QVAN = '.$perPage.';
        var productFilterObj = [];
        var productPage = 0;
        setupPager(jQuery("#productlist li").length);
        showProducts(1);
    </script>
    ';
    }

    return $result;
}

require_once '../../../wp-load.php';
$cfg = parse_ini_file(dirname(__FILE__).'/bol-keys.ini', true);
$cfg['AccessKey'] = get_option('bol_access_key');
$cfg['SecretAccessKey'] = get_option('bol_secret_key');
$limit = isset($_POST['limit']) ? $_POST['limit'] : 100;

$get = (isset($_GET['get']))?$_GET['get']:'';
switch ($get) {
    case "categories":
        $parentId = (isset($_GET['parentId']))?$_GET['parentId']:0;
        echo getCategories($parentId);
        break;
    case "selected": 
        echo getProducts($_POST['id']);
        break;
    case "selected-categories":
        echo getSelectedCategories();
    default:
        echo getSearch($_POST['text'], $_POST['category'], $limit, 0);
}