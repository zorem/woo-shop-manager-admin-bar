jQuery('document').ready(function(){
	jQuery('#wp-admin-bar-wsmab_main_switch_user').click(function(){
		jQuery('input[id="sma_search_username"]').focus();
	});
});

jQuery(document).on("click", "#sma_switch_user_search_submit", function(){
	"use strict";
	var user = jQuery('#sma_search_username').val();
	
	jQuery.ajax({
		type : 'POST',
		url : sma_switch_user_object.ajaxurl,
		data : {
			action : 'sma_switch_user_search',
			username : user,
			security : jQuery('#sma_search_user_nonce').val(),
		},
		beforeSend : function() {
			jQuery('#sma_search_username').prop('disabled',true);
		},
		success : function( response ) {
			jQuery('#sma_search_username').prop( 'disabled', false );
			jQuery('#sma_user_search_result').html( response );
		},
	});
	return false;
});
