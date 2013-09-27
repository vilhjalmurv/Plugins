<?php
	/*
	Plugin Name: Replace icelandic.
	Plugin URI: http://www.d70.is
	Description: Replaces icelandic letters like á, æ, ö, ú and other letters from uploaded file names.
	Version: 1.0
	Author: D70 ehf.
	Author URI: http://www.d70.is
	*/

	/**
	 * Replaces icelandic letters in filenames.
	 * @since Version 1.0
	 * @return string filenames without icelandic letters.
 	 **/
	function replace_icelandic($str) {
		$icelandic = array( "á", "Á", "ð", "é", "É", "í", "Í", "ó", "Ó", "ú", "Ú", "ý", "Ý", "þ", "Þ", "æ", "Æ", "ö", "Ö" );
		$replacement = array( "a", "A", "d", "e", "E", "i", "I", "o", "O", "u", "U", "y", "Y", "th", "TH", "ae", "AE", "o", "O" );
		return str_replace( $icelandic, $replacement, $str );
	}

	/**
	 * Takes the $file array and runs the filename through replace_icelandic().
	 * @param array $file The file array used by WordPress to handle uploads
	 * @since Version 1.0
	 * @return array Returns the file array with the formatted filename.
	 **/
	function nonicelandic_file_name( $file ) {
		$file['name'] = replace_icelandic( utf8_decode( $file['name'] ) );
		return $file;
	}
	
	add_filter( 'wp_handle_upload_prefilter', 'nonicelandic_file_name', 10 );
?>