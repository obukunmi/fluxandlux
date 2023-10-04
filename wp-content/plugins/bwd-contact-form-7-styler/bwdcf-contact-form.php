<?php
/**
 * Plugin Name: Contact Form 7 Styler
 * Description: BWD Contact Form 7 Styler is an Elementor plugin that customizes all input fields style of contact form 7.
 * Plugin URI:  https://bestwpdeveloper.com/contact-form7/
 * Version:     1.0
 * Author:      Best WP Developer
 * Author URI:  https://bestwpdeveloper.com/
 * Text Domain: bwdcf7-contact-form
 * Elementor tested up to: 3.0.0
 * Elementor Pro tested up to: 3.7.3
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once ( plugin_dir_path(__FILE__) ) . '/includes/requires-check.php';

final class Finalbwdcontactform{

	const VERSION = '1.0';

	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

	const MINIMUM_PHP_VERSION = '7.0';

	public function __construct() {
		// Load translation
		add_action( 'bwdcf_init', array( $this, 'bwdcf_loaded_textdomain' ) );
		// bwdcf_init Plugin
		add_action( 'plugins_loaded', array( $this, 'bwdcf_init' ) );
	}

	public function bwdas_loaded_textdomain() {
		load_plugin_textdomain( 'bwdcf7-contact-form' );
	}

	public function bwdcf_init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			// Elementor  activation check
			add_action( 'admin_notices', 'bwdcf_contact_form_register_required_plugins');
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'bwdcf_admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'bwdcf_admin_notice_minimum_php_version' ) );
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once( 'bwdcf-boots.php' );
	}


	public function bwdcf_admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'bwdcf7-contact-form' ),
			'<strong>' . esc_html__( 'BWD Contact Form 7 Styler', 'bwdcf7-contact-form' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'bwdcf7-contact-form' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('%1$s', 'bwdcf7-contact-form') . '</p></div>', $message );
	}

	public function bwdcf_admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'bwdcf7-contact-form' ),
			'<strong>' . esc_html__( 'BWD Contact Form 7 Styler', 'bwdcf7-contact-form' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'bwdcf7-contact-form' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('%1$s', 'bwdcf7-contact-form') . '</p></div>', $message );
	}
}

// Instantiate bwdcf7-contact-form.
new Finalbwdcontactform();
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );