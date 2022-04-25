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
(function( $ ){
	$.fn.sma_snackbar = function(msg) {
		if ( jQuery('.snackbar-logs').length === 0 ){
			$("body").append("<section class=snackbar-logs></section>");
		}
		var sma_snackbar = $("<article></article>").addClass('snackbar-log snackbar-log-success snackbar-log-show').text( msg );
		$(".snackbar-logs").append(sma_snackbar);
		setTimeout(function(){ sma_snackbar.remove(); }, 3000);
		return this;
	}; 
})( jQuery );

/* zorem_snackbar_warning jquery */
(function( $ ){
	$.fn.sma_snackbar_warning = function(msg) {
		if ( jQuery('.snackbar-logs').length === 0 ){
			$("body").append("<section class=snackbar-logs></section>");
		}
		var sma_snackbar_warning = $("<article></article>").addClass( 'snackbar-log snackbar-log-error snackbar-log-show' ).html( msg );
		$(".snackbar-logs").append(sma_snackbar_warning);
		setTimeout(function(){ sma_snackbar_warning.remove(); }, 3000);
		return this;
	}; 
})( jQuery );

/* panels checkbox event */
jQuery(document).on("click", "#hide-checkbox.is-upgraded input#hide_all_panels", function(){
    "use strict";
	if (jQuery(this).is(':checked') ) {
       	jQuery('label.panel-checkbox.is-upgraded').addClass('is-checked');
		jQuery('label.panel-checkbox.is-upgraded input').prop('checked', true);
    } else {
        jQuery('label.panel-checkbox.is-upgraded').removeClass('is-checked');
		jQuery('label.panel-checkbox.is-upgraded input').prop('checked', false);
    }
});
 
jQuery(document).on("click", "label.panel-checkbox.is-upgraded input", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
		jQuery('#hide-checkbox.is-upgraded').addClass('is-checked');
	    jQuery('#hide-checkbox.is-upgraded input#hide_all_panels').prop('checked', true);
    }
});

/*disable for changes in checkbox*/
jQuery(document).on("change", "#sma_general_tab_form input", function(){
	jQuery('.launch_customizer_btn').attr("disabled","disabled");
	jQuery(".launch_customizer_btn").css('pointer-events','none');
});

/*ajex call for general tab form save*/	 
jQuery(document).on("submit", "#sma_general_tab_form", function(){
	"use strict";
	jQuery("#sma_general_tab_form .spinner").addClass("active");
	var form = jQuery('#sma_general_tab_form');
	
	jQuery.ajax({
		url: ajaxurl,		
		data: form.serialize(),
		type: 'POST',
		dataType:"json",	
		success: function(response) {	
			if( response.success === "true" ){
				jQuery("#sma_general_tab_form .spinner").removeClass("active");
				jQuery("#sma_general_tab_form").zorem_snackbar( 'Data saved successfully' );
				jQuery('.launch_customizer_btn').removeAttr("disabled");
				jQuery(".launch_customizer_btn").css('pointer-events','all');
			} else {
				//show error on front
			}
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
});

/** Dashboard tab auto save function **/
jQuery(document).on("change", "#sma_dashboard_tab_form .tgl-flat", function(){
	// save_dashboard_tab();
});

function save_dashboard_tab(){
	"use strict";
	jQuery("#sma_dashboard_tab_form .spinner").addClass("active");
	var form = jQuery('#sma_dashboard_tab_form');
	
	jQuery.ajax({
		url: ajaxurl,		
		data: form.serialize(),
		type: 'POST',
		dataType:"json",	
		success: function(response) {	
			if( response.success === "true" ){
				jQuery("#sma_dashboard_tab_form .spinner").removeClass("active");
				jQuery("#sma_dashboard_tab_form").zorem_snackbar( 'Data saved successfully' );
			} else {
				//show error on front
			}
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
}

/*ajex call for login tab form save*/	 
jQuery(document).on("submit", "#sma_login_tab_form", function(){
	"use strict";
	jQuery("#sma_login_tab_form .spinner").addClass("active");
	var form = jQuery('#sma_login_tab_form');
	
	jQuery.ajax({
		url: ajaxurl,		
		data: form.serialize(),
		type: 'POST',
		dataType:"json",	
		success: function(response) {	
			if( response.success === "true" ){
				jQuery("#sma_login_tab_form .spinner").removeClass("active");
				jQuery("#sma_login_tab_form").zorem_snackbar( 'Data saved successfully' );
			} else {
				//show error on front
			}
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
});

jQuery(document).on("change", "#sma_admin_menu_tab_form .tgl-flat", function(){
	// save_woocommerce_admin_menu_tab();
});

function save_woocommerce_admin_menu_tab(){
	"use strict";
	jQuery("#sma_admin_menu_tab_form .spinner").addClass("active");
	var form = jQuery('#sma_admin_menu_tab_form');
	
	jQuery.ajax({
		url: ajaxurl,		
		data: form.serialize(),
		type: 'POST',
		dataType:"json",	
		success: function(response) {	
			if( response.success === "true" ){
				jQuery("#sma_admin_menu_tab_form .spinner").removeClass("active");
				jQuery("#sma_admin_menu_tab_form").zorem_snackbar( 'Data saved successfully' );
			} else {
				//show error on front
			}
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
}


jQuery(document).on("click", ".sma_tab_input, .sma_sub_tab_input" , function(){
	"use strict";
	var tab = jQuery(this).data('tab');
	var url = window.location.protocol + "//" + window.location.host + window.location.pathname+"?page="+sma_options.page+"&tab="+tab;
	window.history.pushState({path:url},'',url);	
	var label = jQuery(this).data('label');
	jQuery( '.zorem-layout__header-breadcrumbs .header-breadcrumbs-last' ).text( label );
	jQuery(window).trigger('resize');
});

jQuery(document).on("click", ".sma_admin_content > .sma_tab_input", function(){
	"use strict";
	var label = jQuery(this).data('label');
	jQuery('.zorem-layout__header-breadcrumbs .header-breadcrumbs-last').text(label);
});
jQuery(document).on( "click", ".zorem_sma_tab_name .sma_tab_input", function(){
	'use strict';
	var tab = jQuery(this).data( "name" );
	jQuery( '.zorem_sma_tab_wraper .sma_tab_section' ).hide();
	jQuery( '#'+tab+'' ).show();
	jQuery(window).trigger('resize');
});


jQuery(document).ready(function() {
	'use strict';
	jQuery( '.sma_sub_tab_input:checked' ).trigger('click');
	jQuery( '.sma_tab_input:checked' ).trigger('click');
});

//setting tab active and inactive > save

jQuery(document).on("click", ".general-save-button", function(e){	
	var form = jQuery(this).closest('form');
form.find(".spinner").addClass("active");

jQuery.ajax({
	url: ajaxurl,		
	data: form.serialize(),		
	type: 'POST',		
	dataType:"json",	

	success: function(response) {	
		form.find(".spinner").removeClass("active");
		jQuery(document).zorem_snackbar( 'Settings saved' );			
		jQuery( '.accordion' ).removeClass( 'active' );
		jQuery( '.accordion' ).find( 'span.ast-accordion-btn' ).hide();
		jQuery( '.accordion' ).find( 'span.dashicons' ).addClass( 'dashicons-arrow-right-alt2' );
		jQuery( '.panel' ).slideUp( 'slow' );
	},
	error: function(response) {
		console.log(response);			
	}
});
return false;
});

jQuery(document).on("click", ".accordion", function(){
	if ( jQuery(this).hasClass( 'active' ) ) {
		jQuery(this).removeClass( 'active' );
		jQuery(this).siblings( '.panel' ).slideUp( 'slow' );
		jQuery( '.accordion' ).find('span.dashicons').addClass('dashicons-arrow-right-alt2');
		jQuery( '.accordion' ).find('span.ast-accordion-btn').hide();	  
	} else {
		jQuery( '.accordion' ).removeClass( 'active' );
		jQuery(".accordion").find('span.ast-accordion-btn').hide();
		jQuery(".accordion").find('span.dashicons').addClass('dashicons-arrow-right-alt2');	
		jQuery( '.panel' ).slideUp('slow');
		jQuery(this).addClass( 'active' );
		jQuery(this).find('span.dashicons').removeClass('dashicons-arrow-right-alt2');
		jQuery(this).find('span.ast-accordion-btn').show();
		jQuery(this).siblings( '.panel' ).slideDown( 'slow', function() {
			var visible = jQuery(this).isInViewport();
			if ( !visible ) {
				jQuery('html, body').animate({
					scrollTop: jQuery(this).prev().offset().top - 35
				}, 1000);	
			}			
		} );	 
		 
	}
});

(function( $ ){
	$.fn.isInViewport = function( element ) {
		var win = $(window);
		var viewport = {
			top : win.scrollTop()			
		};
		viewport.bottom = viewport.top + win.height();
		
		var bounds = this.offset();		
		bounds.bottom = bounds.top + this.outerHeight();

		if( bounds.top >= 0 && bounds.bottom <= window.innerHeight) {
			return true;
		} else {
			return false;	
		}		
	};
})( jQuery );


// change option and toggle

jQuery(document).ready(function(){
	if(jQuery("#Enable_Switch_to_Customer").is(':checked'))  { 
		jQuery(".fc_hide").show();
	} else{
		jQuery(".fc_hide").hide();
	}
});

jQuery(document).on("change","#Enable_Switch_to_Customer",function(){ 
	if(jQuery(this).is(':checked')) { 
		jQuery(".fc_hide").show();
	} else {
		jQuery(".fc_hide").hide();
	}
});
/* checkbox event  */
jQuery(document).on("click", ".toggle_td input.remove_woocommerce_dashboard_status.for_admin", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.sub_option_admin_checkbox input.for_sub_admin').prop('checked', false);
    }
});
jQuery(document).on("click", ".sub_option_admin_checkbox input.for_sub_admin", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery('.toggle_td input.remove_woocommerce_dashboard_status.for_admin').prop('checked', true);
    }
}); 

jQuery(document).on("click", ".toggle_td input.remove_woocommerce_dashboard_status_sm.for_sma", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.sub_option_shopmanager_checkbox input.for_sub_sma').prop('checked', false);
    }
});
jQuery(document).on("click", ".sub_option_shopmanager_checkbox input.for_sub_sma", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery(".toggle_td input.remove_woocommerce_dashboard_status_sm.for_sma").prop('checked', true);
    }
});

//admin bar menu toggele woocommerce
jQuery(document).on("click", ".toggle_td input.woocommerce_main_menu.for_admin", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.woocommerce_main_menu_sub_option input.for_sub_admin').prop('checked', false);
    }
});
jQuery(document).on("click", ".woocommerce_main_menu_sub_option input.for_sub_admin", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery('.toggle_td input.woocommerce_main_menu.for_admin').prop('checked', true);
    }
}); 

jQuery(document).on("click", ".toggle_td input.woocommerce_main_menu_sm.for_sma", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.woocommerce_main_menu_sub_option input.for_sub_sma').prop('checked', false);
    }
});
jQuery(document).on("click", ".woocommerce_main_menu_sub_option input.for_sub_sma", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery(".toggle_td input.woocommerce_main_menu_sm.for_sma").prop('checked', true);
    }
});


//admin bar menu toggele elementor
jQuery(document).on("click", ".toggle_td input.elementor_main_menu.for_admin", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.elementor_main_menu_sub_option input.for_sub_admin').prop('checked', false);
    }
});
jQuery(document).on("click", ".elementor_main_menu_sub_option input.for_sub_admin", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery('.toggle_td input.elementor_main_menu.for_admin').prop('checked', true);
    }
}); 

jQuery(document).on("click", ".toggle_td input.elementor_main_menu_sm.for_sma", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.elementor_main_menu_sub_option input.for_sub_sma').prop('checked', false);
    }
});
jQuery(document).on("click", ".elementor_main_menu_sub_option input.for_sub_sma", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery(".toggle_td input.elementor_main_menu_sm.for_sma").prop('checked', true);
    }
});

//admin bar menu toggele trackship
jQuery(document).on("click", ".toggle_td input.trackship_for_woocommerce.for_admin", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.trackship_for_woocommerce_sub_option input.for_sub_admin').prop('checked', false);
    }
});
jQuery(document).on("click", ".trackship_for_woocommerce_sub_option input.for_sub_admin", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery('.toggle_td input.trackship_for_woocommerce.for_admin').prop('checked', true);
    }
}); 

jQuery(document).on("click", ".toggle_td input.trackship_for_woocommerce_sm.for_sma", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.trackship_for_woocommerce_sub_option input.for_sub_sma').prop('checked', false);
    }
});
jQuery(document).on("click", ".trackship_for_woocommerce_sub_option input.for_sub_sma", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery(".toggle_td input.trackship_for_woocommerce_sm.for_sma").prop('checked', true);
    }
});

//admin bar menu toggele AutomateWoo
jQuery(document).on("click", ".toggle_td input.automatewoo.for_admin", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.automatewoo_sub_option input.for_sub_admin').prop('checked', false);
    }
});
jQuery(document).on("click", ".automatewoo_sub_option input.for_sub_admin", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery('.toggle_td input.automatewoo.for_admin').prop('checked', true);
    }
}); 

jQuery(document).on("click", ".toggle_td input.automatewoo_sm.for_sma", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.automatewoo_sub_option input.for_sub_sma').prop('checked', false);
    }
});
jQuery(document).on("click", ".automatewoo_sub_option input.for_sub_sma", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery(".toggle_td input.automatewoo_sm.for_sma").prop('checked', true);
    }
});


//admin bar menu toggele ux-blocks
jQuery(document).on("click", ".toggle_td input.ux-blocks.for_admin", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.ux-blocks_sub_option input.for_sub_admin').prop('checked', false);
    }
});
jQuery(document).on("click", ".ux-blocks_sub_option input.for_sub_admin", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery('.toggle_td input.ux-blocks.for_admin').prop('checked', true);
    }
}); 

jQuery(document).on("click", ".toggle_td input.ux-blocks_sm.for_sma", function(){
    if (jQuery(this).is(':checked') ) {
    } else {
		jQuery('.ux-blocks_sub_option input.for_sub_sma').prop('checked', false);
    }
});
jQuery(document).on("click", ".ux-blocks_sub_option input.for_sub_sma", function(){
	"use strict";
    if (jQuery(this).is(':checked') ) {
	    jQuery(".toggle_td input.ux-blocks_sm.for_sma").prop('checked', true);
    }
});

