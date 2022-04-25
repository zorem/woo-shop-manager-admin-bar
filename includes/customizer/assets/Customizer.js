/* sma_snackbar jquery */
(function( $ ){
	$.fn.zorem_snackbar = function(msg) {
		if ( jQuery('.snackbar-logs').length === 0 ){
			$("body").append("<section class=snackbar-logs></section>");
		}
		var zorem_snackbar = $("<article></article>").addClass('snackbar-log snackbar-log-success snackbar-log-show').text( msg );
		$(".snackbar-logs").empty();
		$(".snackbar-logs").append(zorem_snackbar);
		setTimeout(function(){ zorem_snackbar.remove(); }, 3000);
		return this;
	}; 
})( jQuery );

/* sma_snackbar_warning jquery */
(function( $ ){
	$.fn.zorem_snackbar_warning = function(msg) {
		if ( jQuery('.snackbar-logs').length === 0 ){
			$("body").append("<section class=snackbar-logs></section>");
		}
		var zorem_snackbar_warning = $("<article></article>").addClass( 'snackbar-log snackbar-log-error snackbar-log-show' ).html( msg );
		$(".snackbar-logs").append(zorem_snackbar_warning);
		setTimeout(function(){ zorem_snackbar_warning.remove(); }, 3000);
		return this;
	}; 
})( jQuery );
/*header script end*/ 

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};

jQuery(document).on("click", ".zoremlogin-menu-submenu-title", function(){
	if (jQuery(this).next('.zoremlogin-menu-contain').hasClass('active')) {
        jQuery(this).next('.zoremlogin-menu-contain').removeClass('active');
		jQuery(this).find('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');	
    } else {
		jQuery('.zoremlogin-menu-submenu-title').find('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');
		jQuery('.zoremlogin-menu-contain').removeClass('active');
		jQuery(this).next('.zoremlogin-menu-contain').addClass('active');
		jQuery(this).find('.dashicons').removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
		jQuery('.zoremlogin-menu-submenu-title').css('color', '#072050');
		jQuery(this).css('color', '#424242');
		
		
	}	
});

function save_customizer_setting(){
	var form = jQuery('#zoremlogin_customizer_options');
	jQuery.ajax({
		url: ajaxurl,//csv_workflow_update,		
		data: form.serialize(),
		type: 'POST',
		dataType:"json",		
		success: function(response) {
			if( response.success === "true" ){
				//jQuery('iframe').attr('src', jQuery('iframe').attr('src'));
			} else {
				
			}
		},
		error: function(response) {
			console.log(response);			
		}
	});
}

jQuery(document).on("click", "#zoremlogin_customizer_options .wclp-save", function(){
	"use strict";
	var form = jQuery('#zoremlogin_customizer_options');
	var btn = jQuery('#zoremlogin_customizer_options .wclp-save');
	jQuery.ajax({
		url: ajaxurl,//csv_workflow_update,		
		data: form.serialize(),
		type: 'POST',
		dataType:"json",
		beforeSend: function(){
			btn.prop('disabled', true).html('Please wait..');
		},		
		success: function(response) {
			if( response.success === "true" ){
				jQuery(document).zorem_snackbar( "Settings Successfully Saved." );
				//jQuery('iframe').attr('src', jQuery('iframe').attr('src'));
				btn.prop('disabled', true).html( 'Saved' );
				jQuery('.zoremlogin-back-wordpress-link').removeClass('back_to_notice');
			} else {
				if( response.permission === "false" ){
					btn.prop('disabled', false).html('Save Changes');
					jQuery(document).zorem_snackbar_warning( "you don't have permission to save settings." );
				}
			}
		},
		error: function(response) {
			console.log(response);			
		}
	});
});


/**
 * image uploade and remove
 */

jQuery(document).ready(function($){
	/**
	 * logo image , wpColorPicker , logo width , form bordre-radius , ajax call
	 */
	// jQuery('.zoremlogin-input.color').wpColorPicker();
	
	if ( jQuery('input#btn_expand').is(':checked') ) {
		jQuery(".customizer_preview").contents().find('input#wp-submit.button.button-primary.button-large').addClass('login-full-width-button');
		jQuery(".customizer_preview").contents().find('input#wp-submit.button.button-primary.button-large').removeClass('login-width-auto-button');
	} else {
		jQuery(".customizer_preview").contents().find('input#wp-submit.button.button-primary.button-large').removeClass('login-full-width-button');
		jQuery(".customizer_preview").contents().find('input#wp-submit.button.button-primary.button-large').addClass('login-width-auto-button');
	}
	
	var border_color = jQuery( '#form_border_color' ).val();
	var form_bg_color = jQuery( '#form_bg_color' ).val();
	var bg_color = jQuery( '#bg_color' ).val();
	if ( 'simple' == jQuery( '#sma_login_box_type' ).val() ) {
	
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'border', '0' );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'background-color', bg_color );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'padding', '0 8px' );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'box-shadow', 'none' );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'margin-bottom', '0px !important' );
		jQuery('.zoremlogin-menu.simple_class').addClass('simple_box_hide');
	} else{				
		jQuery('.zoremlogin-menu').removeClass('simple_box_hide');
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'border','1px solid '+border_color );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'background-color', form_bg_color );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'padding', '26px 24px 30px' );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'margin-bottom', '10px' );
		
	}

	var mediaUploader;
	
	jQuery('.upload-button, #widget-image, .sma-replace-btn').click(function(e) {
		
		e.preventDefault();

		// If the uploader object has already been created, reopen the dialog
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}
		
		// Extend the wp.media object
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
			text: 'Choose Image'
		}, multiple: false });
		mediaUploader.open();
		// When a file is selected, grab the URL and set it as the text field's value
		mediaUploader.on('select', function() {
			attachment = mediaUploader.state().get('selection').first().toJSON();
			jQuery('.upload-button').hide();
			jQuery('#uploaded_image').val(attachment.url);
			jQuery('#widget-image').attr('src' , attachment.url).show();			
			jQuery("#customizer_preview").contents().find( 'div#login h1 img' ).attr('src' , attachment.url).show();
			jQuery('.sma-replace-btn,.sma-remove-btn').css( 'display', 'inline-block' );
			setting_change_trigger();
		});
		// Open the uploader dialog
	});
});

jQuery(document).on('click','.sma-remove-btn',function(){
	"use strict"; 
	setting_change_trigger();
	jQuery('#uploaded_image').val('');
	//jQuery("#customizer_preview").contents().find( 'div#login h1 img' ).;
	jQuery("#customizer_preview").contents().find( 'div#login h1 img' ).attr('src', sma_customizer.default_admin_logo);
	jQuery('#widget-image,.sma-replace-btn,.sma-remove-btn').hide();
	jQuery('.upload-button').show();	
});

jQuery(document).on("click", "#form_bg_color", function(){
	setting_change_trigger();
	var value = jQuery( this ).val();
	jQuery(".customizer_preview").contents().find( 'body.login.login-action-login.wp-core-ui' ).css( 'background-color', value );
});

jQuery(document).on("change", ".menu_sub_input_range .logo_width", function(){
	var value = jQuery( this ).val();
	setting_change_trigger();
	jQuery(".customizer_preview").contents().find( '.login h1 img' ).css( 'width', value );
});
jQuery(document).on("change", ".logo_width .slider__value", function(){
	var value = jQuery( this ).val();
	setting_change_trigger();
	jQuery('.logo_width').val(value);
	jQuery('.menu_sub_input_range .logo_width').trigger('change');
	
});

jQuery(document).on("change", "#btn_expand", function(){
	setting_change_trigger();
		if(jQuery('input#btn_expand').is(':checked')) {
		jQuery(".customizer_preview").contents().find('input#wp-submit.button.button-primary.button-large').addClass('login-full-width-button');
		jQuery(".customizer_preview").contents().find('input#wp-submit.button.button-primary.button-large').removeClass('login-width-auto-button');

	} else if(jQuery('input#btn_expand').not(':checked')){
		jQuery(".customizer_preview").contents().find('input#wp-submit.button.button-primary.button-large').removeClass('login-full-width-button');
		jQuery(".customizer_preview").contents().find('input#wp-submit.button.button-primary.button-large').addClass('login-width-auto-button');

	}
});

jQuery( ".zoremlogin-input.heading" ).keyup( function( event ) {
	setting_change_trigger();
	var str = event.target.value;
	if( str ) {				
		jQuery("#customizer_preview").contents().find( 'div#login > p.header_text' ).text(str)
		jQuery("#customizer_preview").contents().find( '.login h1' ).css('padding-bottom','0');

	} else {
		jQuery("#customizer_preview").contents().find( 'div#login > p.header_text' ).text('');
	}
});  

jQuery(document).on("change", ".menu_sub_input_range .border_radius", function(){
	var value = jQuery( this ).val();
	setting_change_trigger();
	jQuery(".customizer_preview").contents().find('body.wp-core-ui #loginform' ).css( 'border-radius', value+'px' );
});

jQuery(document).on("change", ".border_radius .slider__value", function(){
	var value = jQuery( this ).val();
	
	setting_change_trigger();
	jQuery('#sma_border_radius').val(value);
	jQuery('.menu_sub_input_range .border_radius').trigger('change');
	
});

function setting_change_trigger() {	
	jQuery('.woocommerce-save-button').removeAttr("disabled");
	jQuery('#zoremlogin_customizer_options .wclp-save').html('Save Changes');
	jQuery('.zoremlogin-back-wordpress-link').addClass('back_to_notice');
}

/*
on change alert box open
*/
jQuery(document).on("click", ".back_to_notice", function(){
	var r = confirm( 'The changes you made will be lost if you navigate away from this page.' );
	if (r === true ) {
	} else {	
		return false;
	}
});


if ( jQuery.fn.wpColorPicker ) {
	
	jQuery('#bg_color').wpColorPicker({
		change: function(e, ui) {		
			var color = ui.color.toString();
			jQuery(".customizer_preview").contents().find('body.login.login-action-login.wp-core-ui' ).css( 'background-color', color );
			setting_change_trigger();
			if ( 'simple' == jQuery( '#sma_login_box_type' ).val() ) {
				jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'background-color', color );
			}
		}, 	
	});
	jQuery('#form_font_color').wpColorPicker({
		change: function(e, ui) {		
			var color = ui.color.toString();
			jQuery(".customizer_preview").contents().find('body.wp-core-ui #loginform label' ).css( 'color', color );
			setting_change_trigger();
		}, 	
	});
	jQuery('#header_font_color').wpColorPicker({
		change: function(e, ui) {		
			var color = ui.color.toString();
			jQuery(".customizer_preview").contents().find( 'p.header_text' ).css( 'color', color );
			setting_change_trigger();
		}, 	
	});
	jQuery('#link_color').wpColorPicker({
		change: function(e, ui) {		
			var color = ui.color.toString();
			jQuery(".customizer_preview").contents().find('body.wp-core-ui #backtoblog a, body.wp-core-ui #nav a' ).css( 'color', color );
			setting_change_trigger();
		}, 	
	});
	jQuery('#form_bg_color').wpColorPicker({
		change: function(e, ui) {		
			var color = ui.color.toString();
			jQuery(".customizer_preview").contents().find('body.wp-core-ui #loginform' ).css( 'background-color', color );
			setting_change_trigger();
		}, 	
	});
	jQuery('#form_border_color').wpColorPicker({
		change: function(e, ui) {		
			var color = ui.color.toString();
			jQuery(".customizer_preview").contents().find('body.wp-core-ui #loginform' ).css( 'border-color', color );
			setting_change_trigger();
		}, 	
	});
	jQuery('#btn_color').wpColorPicker({
		change: function(e, ui) {		
			var color = ui.color.toString();
			jQuery(".customizer_preview").contents().find('.wp-core-ui .button.button-large' ).css( 'background-color', color );
			setting_change_trigger();
		}, 	
	});
	jQuery('#btn_font_color').wpColorPicker({
		change: function(e, ui) {		
			var color = ui.color.toString();
			jQuery(".customizer_preview").contents().find('.wp-core-ui .button.button-large' ).css( 'color', color );
			setting_change_trigger();
		}, 	
	});
	jQuery('#form_btn_border_color').wpColorPicker({
		change: function(e, ui) {		
			var color = ui.color.toString();
			jQuery(".customizer_preview").contents().find('.wp-core-ui .button.button-large' ).css( 'border-color', color );
			setting_change_trigger();
		}, 	
	});
}

/**
 *  tablet_size , desktop_size , smartphone_size
 */ 


jQuery(document).on("click", ".dashicons-tablet", function(){
	jQuery('.dashicons-tablet').css('color', '#013047');
	jQuery('span').removeClass('desktop_color');
	jQuery('.dashicons-desktop').css('color', '');
	jQuery('.dashicons-smartphone').css('color', '');
	jQuery('.zoremlogin-layout-content-preview.customize-preview').addClass('tablet_size');
	jQuery('.zoremlogin-layout-content-preview.customize-preview').removeClass('desktop_size');
	jQuery('.zoremlogin-layout-content-preview.customize-preview').removeClass('smartphone_size');
	jQuery('.zoremlogin-layout-content-container').css('background-color', '#1d2327');
	
});
jQuery(document).on("click", ".dashicons-desktop", function(){
	jQuery('.zoremlogin-layout-content-preview.customize-preview').addClass('desktop_size');
	jQuery('.dashicons-desktop').css('color', '#013047');
	jQuery('.dashicons-tablet').css('color', '');
	jQuery('.dashicons-smartphone').css('color', '');
	jQuery('.zoremlogin-layout-content-preview.customize-preview').removeClass('tablet_size');
	jQuery('.zoremlogin-layout-content-preview.customize-preview').removeClass('smartphone_size');
});
jQuery(document).on("click", ".dashicons-smartphone", function(){
	jQuery('.dashicons-smartphone').css('color', '#013047');
	jQuery('.dashicons-desktop').css('color', '');
	jQuery('.dashicons-tablet').css('color', '');
	jQuery('.zoremlogin-layout-content-preview.customize-preview').addClass('smartphone_size');
	jQuery('.zoremlogin-layout-content-preview.customize-preview').removeClass('tablet_size');
	jQuery('.zoremlogin-layout-content-preview.customize-preview').removeClass('desktop_size');

});

jQuery(document).on("change", "#sma_login_box_type", function(){
	var value = jQuery( this ).val();
	var border_color = jQuery( '#form_border_color' ).val();
	var form_bg_color = jQuery( '#form_bg_color' ).val();
	var bg_color = jQuery( '#bg_color' ).val();
	setting_change_trigger();
	if( value == 'simple' ){
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'border', '0' );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'background-color', bg_color );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'padding', '0 8px' );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'box-shadow', 'none' );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'margin-bottom', '0' );
		jQuery('.zoremlogin-menu.simple_class').addClass('simple_box_hide');
	} else{				
		jQuery('.zoremlogin-menu').removeClass('simple_box_hide');
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'border','1px solid '+border_color );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'background-color', form_bg_color );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'padding', '26px 24px 34px' );
		jQuery(".customizer_preview").contents().find( 'body.wp-core-ui #loginform' ).css( 'margin-bottom', '10px' );
	}
});
