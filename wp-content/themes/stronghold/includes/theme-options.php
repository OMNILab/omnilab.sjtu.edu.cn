<?php
require_once get_template_directory() . '/includes/options-config.php';
require_once get_template_directory() . '/admin/class.wbls-customizer-api-wrapper.php';

$obj = new Wbls_Customizer_API_Wrapper($options);