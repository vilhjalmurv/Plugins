<?php
/*
Plugin Name: Sanitize Icelandic.
Plugin URI: http://www.d70.is
Description: Replaces icelandic letters like á, æ, ö, ú and other letters from uploaded file names.
Version: 1.1
Author: D70 ehf.
Author URI: http://www.d70.is
*/

class sanitizeIcelandic {
	/**
	 * Array that holds icelandic letters and their counterparts.
	 *
	 * @since version 1.1
	 * @var array
	 */
	var $letters = array(
					array( 'é', 'É', 'ý', 'Ý', 'ú', 'Ú', 'í', 'Í', 'ó', 'Ó', 'ð', 'Ð', 'ö', 'Ö', 'á', 'Á', 'æ', 'Æ', 'þ', 'Þ' ),
					array( 'e', 'E', 'y', 'Y', 'u', 'U', 'i', 'I', 'o', 'O', 'd', 'D', 'o', 'O', 'a', 'A', 'ae', 'AE', 'th', 'TH' )
					);

	function __construct() {
		add_action( 'wp_handle_upload_prefilter', array( $this, 'sanitize' ) );
	}

	/**
	 * Replaces icelandic letters in filenames.
	 *
	 * @param string $str The string that needs to get sanitized.
	 * @since Version 1.1
	 * @return string filenames without icelandic letters.
	 **/
	function replace( $str ) {
		return str_replace( $this->letters[0], $this->letters[1], $str );
	}

	/**
	 * Takes the $file array and runs the filename through replace().
	 *
	 * @param array $file The file array used by WordPress to handle uploads.
	 * @since Version 1.1
	 * @return array Returns the file array with the formatted filename.
	 **/
	function sanitize( $file ) {
		$file['name'] = $this->replace( $file['name'] );
		return $file;
	}
}
$sanitizeIcelandic = new sanitizeIcelandic();