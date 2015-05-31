<?php

	function stronghold_footer_credits() {
		printf( '<p>%1$s<a href="%2$s">%3$s</a>', __( 'Powered by ','stronghold' ), esc_url( __( 'http://wordpress.org/', 'stronghold' ) ), __( 'WordPress','stronghold')  ); ?>
		<span class="sep"> .</span>
		<?php printf( __( 'Theme: %1$s by %2$s', 'stronghold' ), 'StrongHold', '<a href="http://www.webulousthemes.com/" rel="designer">Webulous Themes</a>' ); ?></p>
<?php
	}
	
	add_action('stronghold_credits','stronghold_footer_credits');