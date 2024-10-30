jQuery(document).ready(function(){
	jQuery('input[name=bolSearchField]').live('focus', function(){
		if (!jQuery(this).is('[old-value]')) {
			jQuery(this).attr('old-value', jQuery(this).val());
			jQuery(this).val('');
		}
	});
	jQuery('input[name=bolSearchField]').live('blur', function(){
		if ('' == jQuery(this).val()) {
			jQuery(this).val(jQuery(this).attr('old-value'));
			jQuery(this).removeAttr('old-value');
		}
	});
})