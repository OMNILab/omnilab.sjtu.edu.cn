/**
 * Custom scripts needed for the colorpicker, image button selectors,
 * and navigation tabs.
 */

jQuery(document).ready(function($) {

	// Loads the color pickers
	$('.of-color').wpColorPicker();

	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');
	});

	$('.of-radio-img-label').hide();
	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();

	// Loads tabbed sections if they exist
	if ( $('.nav-tab-wrapper').length > 0 ) {
		options_framework_tabs();
	}

	function options_framework_tabs() {

		var $group = $('.group'),
			$navtabs = $('.nav-tab-wrapper a'),
			active_tab = '';

		// Hides all the .group sections to start
		$group.hide();

		// If active tab is saved and exists, load it's .group
		if ( active_tab != '' && $(active_tab).length ) {
			$(active_tab).fadeIn();
			$(active_tab + '-tab').addClass('nav-tab-active');
		} else {
			$('.group:first').fadeIn();
			$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
		}

		// Bind tabs clicks
		$navtabs.click(function(e) {

			e.preventDefault();

			// Remove active class from all tabs
			$navtabs.removeClass('nav-tab-active');

			$(this).addClass('nav-tab-active').blur();

			var selected = $(this).attr('href');

			$group.hide();
			$(selected).fadeIn();

		});
	}

	$('#enable_parallax').click(function() {
  		$('#section-sticky_header').fadeToggle(400);
	});

	if ($('#enable_parallax:checked').val() == undefined) {
		$('#section-sticky_header').show();
	}

	$( "#section-parallax_section .controls" ).sortable();
	$( "#sub-option-inner" ).disableSelection();

	$( '#section-parallax_section .section-toggle').on('click', function(){
		$(this).parent('.title').next('.sub-option-inner').slideToggle();
	});

	$('.parallax_section_page').on('change',function(){
		var sled = $(this).find("option:selected").text();
		$(this).parents('.sub-option').find('.title span').text(sled);
	}).change();

	$(document).on('click','.remove-parallax', function(){
		var $this = $(this);
		$this.parents('.sub-option').slideUp(800);
		setTimeout( function() {
      	$this.parents('.sub-option').remove();
		},750 );
	});
    
    $('.ap-popup-bg, .ap-popup-close').click(function(){
		$('.ap-popup-bg, .ap-popup-wrapper').fadeOut();
	});
});