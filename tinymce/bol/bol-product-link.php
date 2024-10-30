<?
define('WP_USE_THEMES', false);
require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

$upload_dir = wp_upload_dir();
$upload_dir = $upload_dir['basedir'].'/bol-css/';
$filename = 'bol_'. time() .'_'.'links.css';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>bol.com Product Link invoegen</title>
        <?php if(!$_REQUEST['widget']):?>
        <script type="text/javascript" src="../../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <?php endif;?>
        <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="js/bol.js"></script>
        <script type="text/javascript" src="js/colorpicker.js"></script>
        <script type="text/javascript" src="js/easySlider1.7.js"></script>
        <script type="text/javascript" src="js/prepareFields.js"></script>
        <script type="text/javascript" src="../../bolSrch.js"></script>

        <?php if($_REQUEST['widget']):?>
        <link rel="stylesheet" type="text/css" href="../../../../../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/dialog.css" />
        <?php endif;?>
        <link rel="stylesheet" type="text/css" href="css/bol.css" />
        <link rel="stylesheet" type="text/css" href="../../bolSrch.css" />
        <link rel="stylesheet" media="screen" type="text/css" href="css/colorpicker.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.5.custom.css" />
    </head>
    <body>
        <script type="text/javascript">
            function productSearchChanged(e) {
                if (e.keyCode == 13) getProductSearch();
            }

            function getProductSearch() {
                if (jQuery('#txtBolSearch').val() == '') {
                    alert('Vul een trefwoord in');
                    return false;
                }
                jQuery('#dvResults').html('<span class="loader">Producten worden geladen </span>');
                var category = jQuery('#ddlBolCategory').val();
                if (jQuery('#ddlBolSubCategory').val() > 0) {
                    category = jQuery('#ddlBolSubCategory').val();
                }

                jQuery.ajax({
                    url: "/wp-content/plugins/bol/bol-search.php",
                    type: 'post',
                    data: {'text': jQuery('#txtBolSearch').val(), 'category': category, 'limit': jQuery('#srchLimit').val()},
                    success: function(response) {
                        jQuery("#dvResults").html(response);
                        if (jQuery("#dvResults .productlist li").size() > 0) {
                            jQuery("#dvResults .productlist li").each(function() {
                                productId = jQuery("input", this).val();
                                html = '<a href="javascript: toggleProduct('+productId+')" title="Select product" class="toggle-product-icon"></a>';
                                jQuery(this).append(html);
                            });
                        } else {
                            jQuery("#dvResults").html('<div class="productlist">No products found</div>');
                        }
                    }
                });

                return false;
            }

            function toggleProduct(id) {
                var aProd = jQuery("#hdnBolProducts").val().split(",");
                var inSelected = false;

                // if in selected array - remove
                for (var i in aProd) {
                    if (aProd[i] == id) { 
                        inSelected = true;
                        aProd.splice(i, 1);
                        break; 
                    }
                }

                // if not in selected array - add
                if (!inSelected) {
                    aProd.push(id);
                }

                var sProd = aProd.join(",");
                jQuery("#hdnBolProducts").val(sProd);

                if (inSelected) {
                    // remove
                    jQuery("#dvSelectedProducts .productlist input[value="+id+"]").parent().parent().remove();
                } else {
                    // add to selected
                    tmp = jQuery("#dvResults .productlist input[value="+id+"]").parent().parent();
                    var htmlProduct = tmp.html();
                    htmlProduct = htmlProduct.replace('>Select product</', '>Remove product</');
                    jQuery("#dvSelectedProducts .productlist").append("<li>"+htmlProduct+"</li>");
                    jQuery("#dvResults .productlist input[value="+id+"]").parent().parent().remove();
                }

                // Check if any product is selected
                if (jQuery("#dvSelectedProducts ul.productlist").children('li').length) {
                    jQuery('#tabs-container').tabs("enable", 1);
                    jQuery('#next-step').removeAttr("disabled").removeClass("disabled");
                    jQuery('#no-products-label').hide();
                    jQuery('#selected-products-label').show();
                } else {
                    jQuery('#no-products-label').show();
                    jQuery('#selected-products-label').hide();
                    jQuery('#tabs-container').tabs("disable", 1);
                    jQuery('#next-step').attr("disabled", "disabled").addClass("disabled");
                }

            }

            jQuery(function () {
                jQuery('#tabs-container').tabs({
                    show: function(event, ui) {
                        if (ui.index == 1) {
                            pr();
                        }
                    }
                }).fadeIn(300);
                jQuery('#tabs-container').tabs("disable", 1);
                jQuery('#next-step').click(function(){
                    jQuery('#tabs-container').tabs("select", 1);
                }).attr("disabled", "disabled").addClass("disabled");
                jQuery('#ddlBolCategory').after('<span class="loader" id="categories-loader"></span>');
                jQuery.ajax({
                    url: "/wp-content/plugins/bol/bol-search.php?get=selected-categories",
                    type: 'post',
                    data: {},
                    success: function(response) {
                        jQuery("#categories-loader").remove();
                        jQuery("#ddlBolCategory").append(response);
                    }
                });


            });
        </script>

        <div id="tabs-container">
            <ul id="tabs">
                <li><a href="#tab-search">1. Selecteer Product</a></li>
                <li><a href="#tab-widget">2. Configureer Widget</a></li>
            </ul>
            <div id="tab-search">
                <p style="text-align: right; overflow: hidden;"><u>2 stappen om je widget te configureren:</u>
                    <ul style="float: right; list-style-type: none; margin-top:0; padding-top:0">
                    <li>1. Selecteer product</li>
                    <li>2. Invoegen en configureren widget </li>
                    </ul>
                </p>
                <h4>Zoeken producten</h4>
                <table width="100%" border="0" cellpadding="3">
                    <tr>
                        <td valign="top">
                            <label for="txtBolSearch"><span class="label">Trefwoord:</span>
                                <input type="text" id="txtBolSearch" name="txtBolSearch" value="" style="width: 150px; margin-left: -4px;" onkeyup="productSearchChanged(event)"/>
                            </label><br/>

                            <label for="srchLimit"><span class="label">Limiet:</span>
                                <input type="text" style="width: 150px; margin-left: -4px;" name="srchLimit" id="srchLimit" value="10" />
                            </label><br />

                            <label for="ddlBolCategory"><span class="label">Selecteer categorie:</span>
                                <select name="ddlBolCategory" id="ddlBolCategory" style="width: 152px;" >
                                    <option value="0">- Selecteer categories -</option>
                                </select><br/>
                            </label>

                        </td>

                        <td style="vertical-align: middle; width: 20%; text-align: center; line-height: 30px">
                            <input type="button" class="updateButton" value="Zoeken" id="apply-search" onclick="getProductSearch()">
                            <input type="button" class="updateButton" value="Stap 2" id="next-step">
                        </td>
                    </tr>
                </table>
                <h4>Selecteer producten om in te voegen</h4>
                <table width="100%" border="0" cellpadding="3">
                    <tr>
                        <td style="vertical-align: top; width: 50%">
                            <div id="dvResults" class="searchResults"></div>
                        </td>
                        <td style="vertical-align: top">
                            <div class="selectedProducts" id="dvSelectedProducts">
                                <input type="hidden" name="hdnBolProducts" id="hdnBolProducts" value=""/>
                                <span id="selected-products-label" style="display: none">Geselecteerde producten</span>
                                <span id="no-products-label">Er zijn geen producten geselecteerd</span>
                                <ul class="productlist">
                                </ul>
                            </div>
                        </td>
                    </tr>
                </table>

            </div>

            <div id="tab-widget">
                <table width="100%" border="0" cellpadding="3">
                    <tr>
                        <td style="vertical-align: top">

                            <label><span class="label">Naam:
                </span><input type="text" id="txtName" name="txtName" value="">
                            </label>
                            <br/>
                            <label><span class="label">SubId:
                </span><input type="text" id="txtSubid" name="txtSubid" value="">
                            </label>
                            <br/>
                            <br/>
                            <label><span class="label">Achtergrond:
                </span><input type="text" class="colorpickerfield" name="txtBackgroundColor" id="txtBackgroundColor" value="FFFFFF"/>
                            </label>
                            <br/>
                            <label><span class="label">Tekst:
                </span><input type="text" class="colorpickerfield" name="txtTextColor" id="txtTextColor" value="CB0100"/>
                            </label>
                            <br/>
                            <label><span class="label">Link:
                </span><input type="text" class="colorpickerfield" name="txtLinkColor" id="txtLinkColor" value="0000FF"/>
                            </label>
                            <br/>
                            <label><span class="label">Rand:
                </span><input type="text" class="colorpickerfield" name="txtBorderColor" id="txtBorderColor" value="D2D2D2"/>
                            </label>
                            <br/>
                            <label><span class="label">Wijdte:
                </span><input type="text" id="txtWidth" name="txtWidth" value="250" style="width: 50px">
                            </label>
                            <br/>
                            <label><span class="label">Kolom:
                </span><input type="text" id="txtCols" name="txtCols" value="1" style="width: 50px">
                            </label>
                            <br/><br/>
                            <label>
                                <input type="checkbox" name="chkRating" id="chkRating" checked/>
                                <span class="label">Toon sterren</span>
                            </label>
                            <br/>
                            <label>
                                <input type="checkbox" name="chkPrice" id="chkPrice" checked/>
                                <span class="label">Toon prijs</span>
                            </label>
                            <br/>

                            <span class="labelOption">Link opent in:</span>
                            <label>
                                <input type="radio" name="rbTarget" id="rbTarget1" value="true" checked/>
                                Nieuw venster
                            </label>
                            <br/>
                            <label>
                                <input type="radio" name="rbTarget" id="rbTarget2" value="false"/>
                                Zelfde venster
                            </label>
                            <br/>
                            <span class="labelOption">Formaat plaatje:</span>
                            <label>
                                <input type="radio" name="rbImageSize" id="rbImageSize1" value="true" checked/>
                                 Groot
                            </label>
                            <br/>
                            <label>
                                <input type="radio" name="rbImageSize" id="rbImageSize2" value="false"/>
                                 Klein
                            </label>
                            <br/>
                            <br/>
                            <br/>
                            <label for="showcss" >
                                <input type="checkbox" name="showcss" id="showcss" onclick="showcssFunc(jQuery(this))" />
                                <span class="label">Toevoegen css style</span>
                            </label>
                            <br />

                            <label id="labelCssstyle" class="hideElement">Invoegen your css styles (this overrules the criteria above. For instance: .bol_pml_price {color: green !important;})<br/>
                                <textarea name="cssstyle" id="cssstyle"></textarea>
                            </label>
                        </td>
                        <td style="width: 50%; vertical-align: top">
                            <div style="float:left">Preview:</div>
                            <div style="float:right"><input type="button" id="apply" name="preview" value="Vernieuwen" onclick="pr();" /></div>
                            <script>
                                var bol_pml = '';
                                var bolScriptName = 'clientProductlink';
                                var shopId = "<?=  get_option("bol_site_id")?>";
                            </script>
                            <script type="text/javascript" src="js/preview.js"></script>
                            <div id="previewDiv"></div>
                            <div id="preview-box"></div>
                            <div id="bol_previewParent"><div id="<?=str_replace('.css', '', $filename)?>"></div></div>
                            <div id="previewCssDiv" class="hideElement"></div>
                        </td>
                    </tr>
                </table>

                <p>
                <div class="mceActionPanel">
                    <input type="hidden" name="filename" id="filename-field" value="<?=$filename?>" />
                    <?php if ($_REQUEST['widget']):?>
                    <input type="hidden" name="widget" id="widget" value="<?php echo strip_tags($_REQUEST['widget'])?>" />
                    <input type="button" id="save-button" name="save" class="updateButton" value="Save" onclick="BolProductDialog.insert(<?php echo !empty($_REQUEST['widget'])?>)" />
                    <span id="save-result"></span>
                    <?php else: ?>
                    <input type="button" id="insert" name="insert" value="Invoegen" onclick="BolProductDialog.insert(<?php echo !empty($_REQUEST['widget'])?>);" />
                    <input type="button" id="cancel" name="cancel" value=" Annuleren" onclick="tinyMCEPopup.close();" />
                    <?php endif;?>
                </div>
                <!--/form-->
                </p>
            </div>
        </div>

<iframe class="hideElement" id="iframeForm" name="iframeForm" src="savecss.php"></iframe>
<form id="saveCss" class="hideElement" target="iframeForm" method="post" action="savecss.php">
    <textarea name="cssstyle1" id="cssstyle1" style="width:600px;height:70px;"></textarea>
    <input type="hidden" name="filename" id="filename" value="<?=$filename?>" />
    <input type="submit" id="save" name="save" value="save" />
</form>

    </body>
</html>
