<?php 
/**
 * Customizer Custom Control Class for Disabled Dropdown
 */
if( ! class_exists('Wbls_Customize_Disabled_Select_Control')) {
	class Wbls_Customize_Disabled_Select_Control extends WP_Customize_Control {
		public $type = 'disabled-select';

		public function render_content() {
			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
				<select <?php $this->link(); ?>>
					<?php printf( '<option value="0">%1$s</option>', __( 'Select Color Scheme', 'stronghold' ) );
						foreach ( $this->choices as $value => $label )
							printf( '<option disabled="disabled" value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $this->value(), $value, false ), $label );
					?>
				</select>
			</label>
		<?php
		}
	}
}