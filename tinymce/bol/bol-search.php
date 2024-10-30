<?
define('WP_USE_THEMES', false);
require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

$upload_dir = wp_upload_dir();
$upload_dir = $upload_dir['basedir'].'/bol-css/';
$filename = 'bol_'. time() .'_'.'search.css';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title> Invoegen bol.com zoekwidget</title>
        <?php if(!$_REQUEST['widget']):?>
        <script type="text/javascript" src="../../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <?php endif;?>
        <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="js/colorpicker.js"></script>
        <script type="text/javascript" src="js/prepareFields.js"></script>
        <script type="text/javascript" src="js/bol-search.js"></script>

        <?php if($_REQUEST['widget']):?>
        <link rel="stylesheet" type="text/css" href="../../../../../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/dialog.css" />
        <?php endif;?>
        <link rel="stylesheet" type="text/css" href="css/bol.css" />
        <link rel="stylesheet" media="screen" type="text/css" href="css/colorpicker.css" />
        <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.5.custom.css" />
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery.ajax({
                    url: "/wp-content/plugins/bol/bol-search.php?get=categories",
                    type: 'post',
                    data: {},
                    success: function(response) {
                        jQuery("#ddlBolCategory").append(response);
                    }
                })
            });

        </script>
    </head>
    <body >
    <table width="100%" border="0" cellpadding="3">
        <tr>
            <td style="vertical-align: top; width: 175px">
                <span class="label">Selecteer categorie:</span><br />
                    <input type="radio" name="rbShowCat" id="rbShowCat1" value="true" checked="checked"/>
                    <select name="ddlBolCategory" id="ddlBolCategory" style="">
                        <option value="0">- Alle categorieen -</option>
                    </select>
                    <br/>
                    <input type="radio" name="rbShowCat" id="rbShowCat2" value="false"/> Selectieveld op site weergeven
                <br />

                <label class="hideElement" id="labelBolSubCategory">
                    <span class="label">Selecteer subcategorie:</span>
                    <select name="ddlBolSubCategory" id="ddlBolSubCategory" style="">
                        <option value="0">- Selecteer subcategorie -</option>
                    </select><br/>
                </label>
                <label class="hideElement" id="labelBolSub2Category">
                    <span class="label">Selecteer subcategorie:</span>
                    <select name="ddlBolSub2Category" id="ddlBolSub2Category" style="">
                        <option value="0">- Selecteer subcategorie -</option>
                    </select><br/>
                </label>
                <label class="hideElement" id="labelBolSub3Category">
                    <span class="label">Selecteer subcategorie:</span>
                    <select name="ddlBolSub3Category" id="ddlBolSub3Category" style="">
                        <option value="0">- Selecteer subcategorie -</option>
                    </select><br/>
                </label>

                <label class="hideElement">
                    <span class="label">Select prijs:</span>
                    <select id="priceRangeList" name="priceRangeList"><option value="0">Select prijs...</option><option value="7143">Tot &euro; 10</option><option value="4854">Tot &euro; 20</option><option value="4855">Tot &euro; 30</option><option value="4856">Tot &euro; 40</option><option value="4857">Tot &euro; 50</option><option value="4858">Tot &euro; 100</option><option value="5014">Tot &euro; 200</option><option value="4860">Tot &euro; 300</option><option value="4861">Tot &euro; 400</option><option value="4862">Tot &euro; 500</option><option value="4863">Tot &euro; 750</option><option value="4864">Tot &euro; 1000</option><option value="4865">Tot &euro; 1500</option><option value="4866">Tot &euro; 2000</option><option value="7346">Tot &euro; 2500</option></select>
                    <br/>
                </label>

                <label><span class="label">Default zoeken: </span><input type="text" name="txtSearch" id="txtSearch" style="width: 50px"></label> <br/>
                <label><span class="label">Limiet: </span><input type="text" name="txtLimit" id="txtLimit" value="10" style="width: 50px"></label><br/>
                <br/>

                <label><span class="label">Naam:
                </span><input type="text" id="txtName" name="txtName" value="" style="width: 50px">
                </label>
                <br/>
                <label><span class="label">SubId:
                </span><input type="text" id="txtSubid" name="txtSubid" value="" style="width: 50px">
                </label>
                <br/>
                <br/>
                <label><span class="label">Achtergrond:
                </span><input type="text" class="colorpickerfield" name="txtBackgroundColor" id="txtBackgroundColor" value="FFFFFF" style="width: 50px"/>
                </label>
                <br/>
                <label><span class="label">Tekst:
                </span><input type="text" class="colorpickerfield" name="txtTextColor" id="txtTextColor" value="CB0100" style="width: 50px"/>
                </label>
                <br/>
                <label><span class="label">Link:
                </span><input type="text" class="colorpickerfield" name="txtLinkColor" id="txtLinkColor" value="0000FF" style="width: 50px"/>
                </label>
                <br/>
                <label><span class="label">Rand:
                </span><input type="text" class="colorpickerfield" name="txtBorderColor" id="txtBorderColor" value="D2D2D2" style="width: 50px"/>
                </label>
                <br/>
                <label><span class="label">Breedte blok:
                <?php $width = ($_REQUEST['widget']) ? 200 : 500;?>
                </span><input type="text" id="txtWidth" name="txtWidth" value="<?php echo $width?>" style="width: 50px">
                </label>
                <br/>
                <label><span class="label">Kolom:
                <?php $columns = ($_REQUEST['widget']) ? 1 : 2;?>
                </span><input type="text" id="txtCols" name="txtCols" value="<?php echo $columns?>" style="width: 50px">
                </label>
                <br/><br/>
                <label>
                    <input type="checkbox" name="chkRating" id="chkRating" checked/>
                    <span class="label">Toon sterren </span>
                </label>
                <br/>
                <label><input type="checkbox" name="chkPrice" id="chkPrice" checked/>
                    <span class="label"> Toon prijs</span>
                </label>
                <br/>
                <label>
                    <input type="checkbox" name="show_bolheader" id="show_bolheader" checked/>
                    <span class="label">Toon bol.com logo</span>
                </label>
                <br/>
                <span class="labelOption">Link opent in:</span>
                <label></label><input type="radio" name="rbTarget" id="rbTarget1" value="true" checked/>
                Nieuw venster
                </label>
                <br/>
                <label><input type="radio" name="rbTarget" id="rbTarget2" value="false"/>
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
                <br />
                <label for="showcss" >
                    <input type="checkbox" name="showcss" id="showcss" onclick="showcssFunc(jQuery(this))" />
                    <span class="label">Toevoegen css style</span>
                </label><br />

                <label id="labelCssstyle" class="hideElement">Invoegen your css styles (this overrules the criteria above. For instance: .bol_pml_price {color: green !important;})<br/>
                    <textarea name="cssstyle" id="cssstyle"></textarea>
                </label>
            </td>
            <td valign="top">
                <script>
                    var bol_pml = '', bol_pml_search = '';
                    var bol_cats = 'cats';
                    var bolScriptName = 'clientSearchBoxGenerator';
                    var shopId = "<?=  get_option("bol_site_id")?>";
                </script>
                <script type="text/javascript" src="js/preview.js"></script>
                <div style="float:left">Preview:</div>
                <div style="float:right"><input type="button" id="apply" name="preview" value="Vernieuwen" onclick="pr();" /></div>


                <div id="previewDiv"></div>
                <div id="preview-box"></div>
                <div id="bol_previewParent"><div id="<?=str_replace('.css', '', $filename)?>"></div></div>
                <div id="previewCssDiv" class="hideElement"></div>
            </td>
        </tr>
    </table>

            <div class="mceActionPanel">
                <input type="hidden" name="filename" id="filename" value="<?=$filename?>" />
                <?php if ($_REQUEST['widget']):?>
                    <input type="hidden" name="widget" id="widget" value="<?php echo strip_tags($_REQUEST['widget'])?>" />
                    <input type="button" id="save-button" name="save" class="updateButton" value="Save" onclick="BolProductDialog.insert(<?php echo !empty($_REQUEST['widget'])?>)" />
                    <span id="save-result"></span>
                <?php else: ?>
                <input type="button" id="insert" name="insert" value="Invoegen" onclick="BolProductDialog.insert();" />
                <input type="button" id="cancel" name="cancel" value=" Annuleren" onclick="tinyMCEPopup.close();" />
                <?php endif;?>
            </div>



<iframe class="hideElement" id="iframeForm" name="iframeForm" src="savecss.php"></iframe>
<form id="saveCss" class="hideElement" target="iframeForm" method="post" action="savecss.php">
    <textarea name="cssstyle1" id="cssstyle1" style="width:600px;height:70px;"></textarea>
    <input type="hidden" name="filename" id="filename" value="<?=$filename?>" />
    <input type="submit" id="save" name="save" value="save" />
</form>

    </body>
</html>
