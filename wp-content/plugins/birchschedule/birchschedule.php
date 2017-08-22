<?php

/*
  Plugin Name: BirchPress Scheduler
  Plugin URI: http://www.birchpress.com
  Description: An appointment booking and online scheduling plugin that allows service businesses to take online bookings.
  Version: 1.11.1
  Author: BirchPress
  Author URI: http://www.birchpress.com
  License: GPLv2
 */

if ( defined( 'ABSPATH' ) && ! function_exists( 'birchschedule_main' ) ) {

	function birchschedule_main() {

		require_once 'loader.php';

		birchschedule_load( array(
				'plugin_file_path' => __FILE__,
				'product_version' => '1.11.1',
				'product_name' => 'BirchPress Scheduler',
				'product_code' => 'birchschedule',
				'global_name' => 'birchschedule'
			) );
	}

	birchschedule_main();
}
