jQuery(function(){
    jQuery('table input, table select').change(function(){
        pr();
    });
});

function pr()
{

    if (jQuery('#txtLimit').val() > 25 || jQuery('#txtLimit').val() < 1)
    {
        alert('Please, enter a limit from 1-25');
        return false;
    }

    var blockId = jQuery('#filename').val().replace('.css','');
    var catId = getCatId();

    jQuery('#bol_previewParent').html('<span class="loader">Loading preview</span><div id="'+blockId+'"></div>');

    var nrProducts = jQuery('#txtLimit').val();
    var name = jQuery('#txtName').val();
    var header = jQuery('#show_bolheader').attr('checked');
    var target = jQuery('#rbTarget1').attr('checked');
    var rating = jQuery('#chkRating').attr('checked');
    var price = jQuery('#chkPrice').attr('checked');
    var link_subid = jQuery('#txtSubid').val();
    var image_size = jQuery('#rbImageSize1').attr('checked');
    var rbImagePosition1 = "left";
    var txtWidth = jQuery('#txtWidth').val();
    var txtCols = jQuery('#txtCols').val();
    var txtBackgroundColor = jQuery('#txtBackgroundColor').val();
    var txtTextColor = jQuery('#txtTextColor').val();
    var txtLinkColor = jQuery('#txtLinkColor').val();
    var txtBorderColor = jQuery('#txtBorderColor').val();
    var txtSearch = jQuery('#txtSearch').val();
    var txtTitle = jQuery('#txtTitle').val();
    var priceRangeId = jQuery('#priceRangeList').val();
    var showCat = (jQuery('#rbShowCat1').attr('checked')) ? false : true;

    bol_pml={
        "id":blockId,
        "secure":false,
        "baseUrl":"partnerprogramma.bol.com",
        "urlPrefix":"http://aai.bol.com/aai",
        "header":header,
        "logoColor":"black",
        "site_id": shopId,
        "target":target,
        "rating":rating,
        "price":price,
        "link_name":name,
        "link_subid":link_subid,
        "image_size":image_size,
        "image_position":rbImagePosition1,
        "width":txtWidth,
        "cols":txtCols,
        "background_color":"#"+txtBackgroundColor,
        "text_color":"#"+txtTextColor,
        "link_color":"#"+txtLinkColor,
        "border_color":"#"+txtBorderColor,
        "letter_type":"verdana","letter_size":"11"
    };

     switch (bolScriptName)
     {
         case 'clientBestsellerGenerator':
             bol_pml.nrProducts = nrProducts;
             bol_pml.catID = catId;
             bol_pml.title = jQuery('#txtTitle').val();
             if (priceRangeId.length) {
                 bol_pml.priceRangeId = priceRangeId;
                bol_pml.catID += '+' + priceRangeId;
             }
             if (catId == 0) {
                return false;
             }
             break;
         case 'clientSearchBoxGenerator':
             var cats = [];
             jQuery("#ddlBolCategory option").each(function(i, obj){
                 if (jQuery(obj).val() != 0) {
                    cats.push({ "description": jQuery(obj).text(), "id": jQuery(obj).val() });
                 }
             });
             bol_pml.searchFor = txtSearch;
             bol_pml.default_results = nrProducts;
             bol_pml.catId = catId;
             bol_pml.showCat = showCat;
             bol_pml_search = bol_pml;
             bol_cats = { "cats": cats };

             break;
         case 'clientProductlink':
             var selectedId = '';
             jQuery.each(jQuery('#dvSelectedProducts .productlist li a[href^="javascript: toggleProduct"]'), function(){
                thisId = jQuery(this).attr('href').replace('javascript: toggleProduct(', '').replace(')','');
                selectedId += 'productid='+thisId+'&';
             })
             bol_pml.productId = selectedId;
             break;
     }


    jQuery('#previewDiv').html('<script type="text/javascript" src="http://partnerprogramma.bol.com/partner/static/js/aai/'+bolScriptName+'.js"></script>');
    jQuery('#previewCssDiv').html('<style>'+jQuery('#cssstyle').val()+'</style>');
}
function getCatId()
{
    var tmp = jQuery("#ddlBolCategory").val();
    if (jQuery("#ddlBolSubCategory") && jQuery("#ddlBolSubCategory").val() > 0)
    {
        tmp = jQuery("#ddlBolSubCategory").val();
    }
    if (jQuery("#ddlBolSub2Category") && jQuery("#ddlBolSub2Category").val() > 0)
    {
        tmp = jQuery("#ddlBolSub2Category").val();
    }
    if (jQuery("#ddlBolSub3Category") && jQuery("#ddlBolSub3Category").val() > 0)
    {
        tmp = jQuery("#ddlBolSub3Category").val();
    }
    return tmp;
}

function productLoader(){
    blockId = jQuery('#filename').val().replace('.css','');

    if (jQuery('#bol_previewParent .loader') &&
        !jQuery('#bol_previewParent .loader').hasClass('hideElement'))
    {
        if (jQuery('#bol_previewParent #S'+blockId))
        {
            jQuery('#bol_previewParent .loader').addClass('hideElement');
        }
    }
}

jQuery(document).ready(function () {
    setInterval("productLoader()",100);
});