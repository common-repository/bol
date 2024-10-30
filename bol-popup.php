<?
    $productList = isset($_GET['id']) ? $_GET['id'] : null;
    $productArray = array();
    if ($productList) {
        $ids = explode(",", $productList);
        foreach ($ids as $id) {
            if ($id) $productArray[] = $id;
        }
        $productList = implode(",", $productArray);
    }

    $controlId = isset($_GET['control']) ? $_GET['control'] : null;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>bol.com Product Link invoegen</title>
        <script type="text/javascript" src="tinymce/bol/js/jquery-1.4.2.min.js"></script>
        <link rel="stylesheet" type="text/css" href="bol.css" />
        <link rel="stylesheet" type="text/css" href="bolSrch.css" />
        <script type="text/javascript" src="bolSrch.js"></script>
        <script type="text/javascript">
            function productSearchChanged(e) {
                if (e.keyCode == 13) getProductSearch();
            }

            function getProductSearch() {
                if (jQuery('#txtBolSearch').val() == '') {
                    alert('Vul een trefwoord in');
                    return false;
                }
                jQuery('#dvResults').html('<span class="loader"><img src="tinymce/bol/img/loader.gif" /></span>');

                jQuery.ajax({
                    url: "bol-search.php",
                    type: 'post',
                    data: {'text': jQuery('#txtBolSearch').val(), 'category': jQuery('#ddlBolCategory').val(), 'limit': jQuery('#srchLimit').val()},
                    success: function(response) {
                        jQuery("#dvResults").html(response);

                        jQuery("#dvResults .productlist li").each(function() {
                            productId = jQuery("input", this).val();
                            html = "<a href='javascript: toggleProduct("+productId+")'>Select product</a>";
                            jQuery(this).append(html);
                        });
                        
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
                    jQuery("#dvSelectedProducts .productlist input[value="+id+"]").parent().parent().remove();
                } else {
                    tmp = jQuery("#dvResults .productlist input[value="+id+"]").parent().parent();
                    var htmlProduct = tmp.html();
                    htmlProduct = htmlProduct.replace('>Select product</', '>Remove product</');
                    jQuery("#dvSelectedProducts .productlist").append("<li>"+htmlProduct+"</li>");
                    jQuery("#dvResults .productlist input[value="+id+"]").parent().parent().remove();
                }

            }

            function submitForm() {
                <? if ($controlId): ?>
                var tmp = jQuery("#<?= $controlId ?>", window.parent.document);
                tmp.val(jQuery("#hdnBolProducts").val());
                tmp.parents("form").find("input[type=submit]").click();

                <? endif; ?>
                closePopup();
            }

            function closePopup() {
                parent.jQuery(".bol-popup-dialog").addClass('hideElement');
                parent.jQuery(".bol-popup-dialog").dialog("close");
            }

            // onload
            jQuery(function () {
                jQuery.ajax({
                    url: "bol-search.php?get=categories",
                    type: 'post',
                    data: {},
                    success: function(response) {
                        jQuery("#ddlBolCategory").append(response);
                    }
                });

                jQuery.ajax({
                    url: "bol-search.php?get=selected",
                    type: 'post',
                    data: {'id': jQuery('#hdnBolProducts').val()},
                    success: function(response) {
                        jQuery("#dvProductList").html(response);

                        jQuery("#dvProductList .productlist li").each(function() {
                            id = jQuery("input", this).val();
                            html = "<br/><a href='javascript: toggleProduct("+id+")'>add/remove</a>";
                            jQuery(this).append(html);
                        });
                        
                    }
                });
            });

        </script>
    </head>
    <body>

        <p>
            <label for="txtBolSearch" style="width: 50px;display: inline-block">Search:</label>
            <input type="text" id="txtBolSearch" name="txtBolSearch" value="" style="width: 50%;margin-bottom: 3px;" onkeyup="productSearchChanged(event)">
            <select name="ddlBolCategory" id="ddlBolCategory" style="width: 30%">
                <option value="0">- Selecteer categorie -</option>
            </select>
            <br />
            <label for="srchLimit"  style="width: 50px;display: inline-block">Limit:</label>
            <input type="text"  style="width: 70;margin-bottom: 3px;" name="srchLimit" id="srchLimit" value="10" />            <br />
             <input type="button" value="Search" onclick="getProductSearch()" style="width: 100px;" /><br /><br />
            <div class="mceActionPanel">
                <input type="button" id="insert" name="insert" value="Submit" style="width: 100px;" onclick="submitForm();" />
                <input type="button" id="cancel" name="cancel" value="Close" style="width: 100px;" onclick="closePopup();" />
            </div>
                    <!--/form-->
<table width="100%" border="0" cellpadding="3"><tr><td width="50%" valign="top">
            <div id="dvResults" class="searchResults"></div>
        </td><td valign="top">
        <div class="selectedProducts" id="dvSelectedProducts">
            <input type="hidden" name="hdnBolProducts" id="hdnBolProducts" value="<?= $productList ?>"/>
            <label for="products">Selected products:</label>
            <div id="dvProductList"></div>
        </div>
        </td></tr>
</table>
        </p>

        </div>

    </body>
</html>
