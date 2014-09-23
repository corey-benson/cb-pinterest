<?php

/*
Plugin Name: CB Pinterest API Plugin
Plugin URI: https://github.com/corey-benson/cb-pinterest
Description: A plugin to pull the lastest images from a users Pinterest feed. Using http://pinterestapi.co.uk. Use the shortcode [pins] to pull in the feed and the parameters image_size and api_count to determine display [instagramed image_size="" api_count=""].
Version: 1.0
Author: Corey Benson
Author URI: https://github.com/corey-benson/
*/


define('CB-PIN-API_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
define('CB-PIN-API_NAME', "PinterestAPI");
define("CB-PIN-API_VERSION", "1.0");
define("CB-PIN-API_SLUG", 'cb_pins');

class CB_Pins {

	// Add User Credentials
	private $user_name = "######";
	private $api = "http://www.pinterest.com";


	public function __construct() {

		add_shortcode( 'pins', array($this, 'render_shortcode'));
		add_action( 'http_request_args', 'no_ssl_http_request_args', 10, 2 );

	}

	public function no_ssl_http_request_args( $args, $url ) {

		// Fix SSL request error
		$args['sslverify'] = false;
		return $args;

	}


	public function render_shortcode($atts) {


		$pins_str = "";
		$url = "http://pinterestapi.co.uk/" . $this->user_name . "/boards";

		// Get the remote data
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response )) {

			// Handle errors
			$error_str = $response->get_error_message();
			$pins_str = $this->get_preset_images();


		} else {	

			// Process response
			$response = json_decode( $response['body'] );
			$data_array = array();
			$index = 0;

			// Check if response is valid
			if ( is_null($response) ) {

				// Loop through preset array of images and display
				$pins_str = '<div class="error"><p>' . $error_str . '</p></div>';	

			} else {

				// If valid, loop through api
				foreach ($response->body as $obj) {
					
					$data_array[ $index ][ 'name' ] = $obj->name;
					$data_array[ $index ][ 'href' ] = $obj->href;
					$data_array[ $index ][ 'image' ] = $obj->src;

					$index++;
				}

				// Construct HTML
				$pins_str .= '<div class="pins">';
				$pins_str .= '</div>';

			}

		}

		return $pins_str;

	}

}

$cb_pins = new CB_Pins();

?>