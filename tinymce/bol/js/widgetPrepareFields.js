function loadSubctg(main, sub)
{
    if (sub.indexOf('BolSubCategory') > 0)
    {
        hideSub(sub.replace('BolSubCategory', 'BolSub2Category'));
        hideSub(sub.replace('BolSubCategory', 'BolSub3Category'));
    }
    else if (sub.indexOf('BolSub2Category') > 0)
    {
        hideSub(sub.replace('BolSub2Category', 'BolSub3Category'));
    }
    
    var val = jQuery('#'+main+' option:selected').val();
    if (val == 0 && !jQuery("#label"+sub).hasClass('hideElement'))
    {
        hideSub(sub);
    }
    else
    {
      jQuery("#"+sub).addClass('hideElement');
      jQuery("#label"+sub).removeClass('hideElement');
      jQuery("#label"+sub+' .loader').removeClass('hideElement');

      jQuery.ajax({
        url: "/wp-content/plugins/bol/bol-search.php?get=categories"+"&parentId="+val,
        type: 'post',
        data: {},
        success: function(response) {
            jQuery("#"+sub).html("<option value='0'>- Select subcategory -</option>"+response);
            jQuery("#label"+sub+' .loader').addClass('hideElement');
            jQuery("#"+sub).removeClass('hideElement');
        }
      });
    }
}

function hideSub(name)
{
    jQuery("#label"+name).addClass('hideElement');
    jQuery("#"+name).html("<option value='0'></option>");
}

function showcssFunc(el) {
    mainPrefix = el.attr('id').replace('showcss', '');
    if (el.attr('checked')) {jQuery('#label'+mainPrefix+'cssstyle').removeClass('hideElement');}
    else {
        jQuery('#label'+mainPrefix+'cssstyle').addClass('hideElement');
        jQuery('#'+mainPrefix+'cssstyle').val('');
    }
}

jQuery(document).ready(function () {
    jQuery('.colorpickerfield').live('keyup', function(){
        jQuery(this).ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                jQuery(el).val(hex);
                jQuery(el).ColorPickerHide();
            },
            onBeforeShow: function () {
                jQuery(this).ColorPickerSetColor(this.value);
            }
        });

        jQuery(this).ColorPickerSetColor(this.value);
    });

    jQuery('.colorpickerfield').live('click', function(){
        jQuery(this).ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                jQuery(el).val(hex);
                jQuery(el).ColorPickerHide();
            },
            onBeforeShow: function () {
                jQuery(this).ColorPickerSetColor(this.value);
            }
        });

        jQuery(this).ColorPickerSetColor(this.value);
    });

});