(function($){
	$(function(){
		$('.flexslider').flexslider();
	});

	// Sticky Header 
	$(window).scroll(function() {
	if ($(this).scrollTop() > 1){  
	    $('#masthead').addClass("site-header-sticky");
	  }
	  else{
	    $('#masthead').removeClass("site-header-sticky");
	  }
	});

})(jQuery);