<?php
/**
 * Class for the Redux importer.
 *
 * @see https://wordpress.org/plugins/redux-framework/
 *
 * @package Merlin WP
 */

class Merlin_Redux_Importer {
	/**
	 * Import Redux data from a JSON file, generated by the Redux plugin.
	 *
	 * @param array $import_data Array of arrays. Child array contains 'option_name' and 'file_path'.
	 *
	 * @return boolean
	 */
	public static function import( $import_data ) {
		// Redux plugin is not active!
		if ( ! class_exists( 'ReduxFramework' ) || ! class_exists( 'ReduxFrameworkInstances' ) || empty( $import_data ) ) {
			return false;
		}

		foreach ( $import_data as $redux_item ) {
			$redux_options_raw_data = file_get_contents( $redux_item['file_path'] );
			$redux_options_data     = json_decode( $redux_options_raw_data, true );
			$redux_framework        = ReduxFrameworkInstances::get_instance( $redux_item['option_name'] );

			if ( isset( $redux_framework->args['opt_name'] ) ) {
				$redux_framework->set_options( $redux_options_data );

				Merlin_Logger::get_instance()->debug( esc_html__( 'The Redux Framework data was imported' , 'powerlegal'), $redux_item );
			}
		}

		return true;
	}
}
