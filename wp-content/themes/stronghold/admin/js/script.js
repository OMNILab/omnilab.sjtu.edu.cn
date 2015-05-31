( function( $ ) {
	// Add Make Plus message
		upgrade = $('<a class="stronghold-buy-pro"></a>')
			.attr('href', 'http://www.webulousthemes.com/?add-to-cart=33')
			.attr('target', '_blank')
			.text(stronghold_upgrade.message)
		;
		$('.preview-notice').append(upgrade);
		// Remove accordion click event
		$('.stronghold-buy-pro').on('click', function(e) {
			e.stopPropagation();
		});
} )( jQuery );