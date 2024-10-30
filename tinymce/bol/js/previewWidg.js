function previewIt(el){
    var elGlobal = jQuery(el).parent().parent();
    jQuery.ajax({
          url: widgetPreviewFile,
          global: false,
          type: 'POST',
          data: elGlobal.serialize(),
          success: function(msg){
              var txt = msg;
              jQuery('.previewWidg', elGlobal).html('<div id=w2>'+txt.replace('[script]', 'script', 'g')+'</div>');
          }
       }
    ).responseText
}