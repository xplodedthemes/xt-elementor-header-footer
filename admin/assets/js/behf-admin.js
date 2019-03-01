jQuery(document).ready(function ($) {

	var ehf_hide_shortcode_field = function() {

		var selected = jQuery('#ehf_template_type').val();

		if( 'custom' == selected ) {
			jQuery( '.xt-ehf-options-row.xt-ehf-shortcode' ).show();
		} else {
			jQuery( '.xt-ehf-options-row.xt-ehf-shortcode' ).hide();
		}
	}

	jQuery(document).on( 'change', '#ehf_template_type', function( e ) {
			
		ehf_hide_shortcode_field();

	});

	ehf_hide_shortcode_field();

});
