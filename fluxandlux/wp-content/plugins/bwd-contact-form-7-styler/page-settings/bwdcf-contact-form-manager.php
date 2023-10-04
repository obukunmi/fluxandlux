<?php
namespace Creativecontact\PageSettings;

use Elementor\Controls_Manager;
use Elementor\Core\DocumentTypes\PageBase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Page_Settings {

	const PANEL_TAB = 'new-tab';

	public function __construct() {
		add_action( 'elementor/init', [ $this, 'bwdcf_contact_form_add_panel_tab' ] );
		add_action( 'elementor/documents/register_controls', [ $this, 'bwdcf_contact_form_register_document_controls' ] );
	}

	public function bwdcf_contact_form_add_panel_tab() {
		Controls_Manager::add_tab( self::PANEL_TAB, esc_html__( 'Contact Form 7', 'bwdcf7-contact-form' ) );
	}

	public function bwdcf_contact_form_register_document_controls( $document ) {
		if ( ! $document instanceof PageBase || ! $document::get_property( 'has_elements' ) ) {
			return;
		}

		$document->start_controls_section(
			'bwdcf_contact_form_new_section',
			[
				'label' => esc_html__( 'Settings', 'bwdcf7-contact-form' ),
				'tab' => self::PANEL_TAB,
			]
		);

		$document->add_control(
			'bwdcf_contact_form_text',
			[
				'label' => esc_html__( 'Title', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Title', 'bwdcf7-contact-form' ),
			]
		);

		$document->end_controls_section();
	}
}
