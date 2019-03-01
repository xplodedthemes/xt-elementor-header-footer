jQuery(document).ready(function ($) {

	jQuery( '.xt-ehf-notice.is-dismissible .notice-dismiss' ).on( 'click', function() {
		_this 		= jQuery( this ).parents( '.xt-ehf-active-notice' );
		var $id 	= _this.attr( 'id' ) || '';
		var $time 	= _this.attr( 'dismissible-time' ) || '';
		var $meta 	= _this.attr( 'dismissible-meta' ) || '';

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action 	: 'xt-ehf-notices',
				id 		: $id,
				meta 	: $meta,
				time 	: $time,
			},
		});

	});

});