jQuery(document).ready( function($) {
	$('#add_new_section').click(function(){
		$.ajax({
		     url: ajaxurl,
		     data: ({'action' : 'get_my_option'}),
		     success: function(data) {
		      $('#section-parallax_section .controls').append(data);
		      $('.of-color').wpColorPicker();
		      
		      $('.parallax_section_page').on('change',function(){
				var sled = $(this).find("option:selected").text();
				$(this).parents('.sub-option').find('.title span').text(sled);
			   });
		     }
		});
	});
});