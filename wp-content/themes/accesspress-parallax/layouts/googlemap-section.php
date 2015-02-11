<?php
/**
 * The template for displaying all Parallax Templates.
 *
 * @package accesspress_parallax
 */
?>

	<div class="content-area googlemap-section">
	<script type="text/javascript">
		    var map;
		    function initialize() {
		      var mapOptions = {
		        zoom: 18,
		        center: new google.maps.LatLng(<?php echo of_get_option('map_latitude') ?>, <?php echo of_get_option('map_longitude') ?> ),
		        mapTypeId: google.maps.MapTypeId.ROADMAP,
		        scrollwheel: false,
		        mapTypeControlOptions: {
		            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
		        },
		      };
		      map = new google.maps.Map(document.getElementById('ap-map-canvas'),
		          mapOptions);
		    }
	</script>
	<div class="googlemap-toggle">Map</div>
	<div class="googlemap-content">
		<?php echo $page->post_content; ?>
		<div id="ap-map-canvas"></div>
	</div>
	</div><!-- #primary -->



