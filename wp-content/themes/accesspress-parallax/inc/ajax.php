<?php
	
	$parallax_section_array = get_option('accesspress_parallax_count');
	
	// Parallax Defaults
	$parallax_defaults = NULL;

	// Pull all the pages into an array
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');

	$options_categories_obj = get_categories();

	$countsettings = rand(0, 50);
	while(in_array($countsettings, $parallax_section_array)){
		$countsettings = rand(0, 50);
	}
?>	

<div class="sub-option clearfix">
<h3 class="title">Page Title: <span></span><div class="section-toggle"><i class="fa fa-chevron-down"></i></div></h3>
<div class="sub-option-inner">

<div class="inline-label">
<label>Page</label>
<select class="parallax_section_page" name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][page]" class="of-input">
<option value="">Select a page:</option>
<?php foreach ($options_pages_obj as $page) { ?>
	<option value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
<?php } ?>
</select>
</div>

<div class="color-picker inline-label">
<label class="">Font Color</label>
<input name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][font_color]" class="of-color" type="text">
</div>

<div class="color-picker inline-label">
<label class="">Background Color</label>
<input name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][color]" class="of-color" type="text">
</div>

<div class="inline-label">
<label class="">Layout</label>
<select class="parallax_section_layout" class="of-section of-section-layout" name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][layout]">
	<option value="default_template">Default Section</option>
	<option value="service_template">Service Section</option>
	<option value="team_template">Team Section</option>
	<option value="portfolio_template">Portfolio Section</option>
	<option value="testimonial_template">Testimonial Section</option>
	<option value="blog_template">Blog Section</option>
	<option value="action_template">Call to Action Section</option>
	<option value="googlemap_template">Google Map Section</option>
	<option value="blank_template">Blank Section</option>
</select>
</div>

<div class="inline-label">
<label class="">Category</label>
<select class="parallax_section_category" name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][category]" class="of-input">
	<option value="">Select a Category:</option>
<?php foreach ($options_categories_obj as $category) { ?>
	<option value="<?php echo $category->cat_ID; ?>"><?php echo $category->cat_name; ?></option>
<?php } ?>
</select>
</div>

<div class="inline-label">
<label class="">Background Image</label>
<input type="text" placeholder="No file chosen" value="" name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][image]" class="upload" id="parallax_section">
<input type="button" value="Upload" class="upload-button button" id="upload-parallax_section">
<div id="parallax_section-image" class="screenshot"></div>
</div>


<div class="of-background-properties hide">
<div class="clearfix">
<select id="parallax_section_repeat" name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][repeat]" class="of-background of-background-repeat">
	<option value="no-repeat">No Repeat</option>
	<option value="repeat-x">Repeat Horizontally</option>
	<option value="repeat-y">Repeat Vertically</option>
	<option value="repeat">Repeat All</option>
</select>

<select id="parallax_section_position" name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][position]" class="of-background of-background-position">
<option value="top left">Top Left</option>
<option value="top center">Top Center</option>
<option value="top right">Top Right</option>
<option value="center left">Middle Left</option>
<option value="center center">Middle Center</option>
<option value="center right">Middle Right</option>
<option value="bottom left">Bottom Left</option>
<option value="bottom center">Bottom Center</option>
<option value="bottom right">Bottom Right</option>
</select>

<select id="parallax_section_attachment" name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][attachment]" class="of-background of-background-attachment">
<option value="scroll">Scroll Normally</option>
<option value="fixed">Fixed in Place</option>
</select>

<select id="parallax_section_size" name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][size]" class="of-background of-background-size">
<option value="auto">Auto</option>
<option value="cover">Cover</option>
<option value="contain">Contain</option>
</select>
</div>

<div class="inline-label">
<label class="">Overlay</label>
<select id="parallax_section_overlay" class="of-background of-background-overlay" name="accesspress_parallax[parallax_section][<?php echo $countsettings; ?>][overlay]">
<option value="overlay0">No Overlay</option>
<option value="overlay1">Overlay 1</option>
<option value="overlay2">Overlay 2</option>
<option value="overlay3">Overlay 3</option>
<option value="overlay4">Overlay 4</option>
</select>
</div>
</div>
<div class="remove-parallax button-primary">Remove</div>
</div>
</div>

