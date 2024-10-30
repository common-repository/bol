function pr(buttonName)
{
    prefix = buttonName.replace('preview', '');

    if (jQuery('#'+prefix+'limit').val() > 25 || jQuery('#'+prefix+'limit').val() < 1)
    {
        alert('Please, enter a limit from 1-25');
        return false;
    }
    
    var blockId = jQuery('#'+prefix+'filename').val();
    var catId = getCatId();
    if (catId == 0 && bolScriptName != 'clientProductlink') return false;
    jQuery('#bol_previewParent').html('<span class="loader"><img src="../img/loader.gif" /></span><div id="'+blockId+'"></div>');

    var nrProducts = jQuery('#'+prefix+'limit').val();
    var name = jQuery('#'+prefix+'name').val();
    var header = jQuery('#'+prefix+'bolheader').attr('checked');
    var target = jQuery('#'+prefix+'target1').attr('checked');
    var rating = jQuery('#'+prefix+'rating').attr('checked');
    var price = jQuery('#'+prefix+'price').attr('checked');
    var link_subid = jQuery('#'+prefix+'subid').val();
    var image_size = jQuery('#'+prefix+'image_size1').attr('checked');
    var rbImagePosition1 = (jQuery('#'+prefix+'image_position1').attr('checked'))?"left":"rigth";
    var txtWidth = jQuery('#'+prefix+'width').val();
    var txtCols = jQuery('#'+prefix+'cols').val();
    var txtBackgroundColor = jQuery('#'+prefix+'background_color').val();
    var txtTextColor = jQuery('#'+prefix+'text_color').val();
    var txtLinkColor = jQuery('#'+prefix+'link_color').val();
    var txtBorderColor = jQuery('#'+prefix+'border_color').val();
    var txtSearch = jQuery('#'+prefix+'search').val();

    bol_pml={"id":blockId,"secure":false,"baseUrl":"partnerprogramma.bol.com","urlPrefix":"http://aai.bol.com/aai",
                "title":"",
                "header":header, "logoColor":"black", "site_id": shopId,
                "target":target,"rating":rating,"price":price,
                "link_name":name,"link_subid":link_subid, "image_size":image_size,
                "image_position":rbImagePosition1,
                "width":txtWidth,"cols":txtCols, "background_color":"#"+txtBackgroundColor,
                "text_color":"#"+txtTextColor,"link_color":"#"+txtLinkColor,
                "border_color":"#"+txtBorderColor,
                "letter_type":"verdana","letter_size":"11"};

     switch (bolScriptName)
     {
         case 'clientBestsellerGenerator':
             bol_pml.nrProducts = nrProducts;
             bol_pml.catID = catId;
             break;
         case 'clientSearchBoxGenerator':
             bol_pml.searchFor = txtSearch;
             bol_pml.default_results = nrProducts;
             bol_pml.catID = catId;
             bol_pml_search = bol_pml;
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

    content = '<[script] type="text/javascript" src="https://partnerprogramma.bol.com/partner/static/js/aai/'+bolScriptName+'.js"></[script]>';

    jQuery('#previewDiv').html(content.replace('[script]', 'script', 'g'));
    jQuery('#previewCssDiv').html('<style>'+jQuery('#'+prefix+'cssstyle').val()+'</style>');
    return false;
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