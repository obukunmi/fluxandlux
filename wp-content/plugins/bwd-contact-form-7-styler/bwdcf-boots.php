<?php
namespace Creativecontact;

use Creativecontact\PageSettings\Page_Settings;
define( "BWDCF_ASFSK_ASSETS_PUBLIC_DIR_FILE", plugin_dir_url( __FILE__ ) . "assets/public" );
define( "BWDCF_ASFSK_ASSETS_ADMIN_DIR_FILE", plugin_dir_url( __FILE__ ) . "assets/admin" );

class ClassbwdcfContactForm {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function bwdcf_admin_editor_scripts() {
		add_filter( 'script_loader_tag', [ $this, 'bwdcf_admin_editor_scripts_as_a_module' ], 10, 2 );
	}

	public function bwdcf_admin_editor_scripts_as_a_module( $tag, $handle ) {
		if ( 'bwdcf_the_contact_form_editor' === $handle ) {
			$tag = str_replace( '<script', '<script type="module"', $tag );
		}

		return $tag;
	}

	private function include_widgets_files() {
		require_once( __DIR__ . '/widgets/bwdcf-contactform.php' );
	}

	public function bwdcf_register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\bwdcfContactForm() );
	}

	private function add_page_settings_controls() {
		require_once( __DIR__ . '/page-settings/bwdcf-contact-form-manager.php' );
		new Page_Settings();
	}

	// Register Category
	function bwdcf_add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'bwdcf-contact-category',
			[
				'title' => esc_html__( 'Contact Form 7', 'bwdcf7-contact-form' ),
				'icon' => 'eicon-person',
			]
		);
	}
	public function bwdcf_all_assets_for_the_public(){
		wp_enqueue_script( 'bwdcf-main-interactive', plugin_dir_url( __FILE__ ) . 'assets/public/js/main.js', array('jquery'), '1.0', true );
		$all_css_js_file = array(
            'bwdcf_contact_form_style_css' => array('bwdcf_path_define'=>BWDCF_ASFSK_ASSETS_PUBLIC_DIR_FILE . '/css/style.css'),
        );
        foreach($all_css_js_file as $handle => $fileinfo){
            wp_enqueue_style( $handle, $fileinfo['bwdcf_path_define'], null, '1.0', 'all');
        }
	}
	public function bwdcf_all_assets_for_elementor_editor_admin(){
		$all_css_js_file = array(
            'bwdcf_contact_form_admin_icon_css' => array('bwdcf_path_admin_define'=>BWDCF_ASFSK_ASSETS_ADMIN_DIR_FILE . '/icon.css'),
        );
        foreach($all_css_js_file as $handle => $fileinfo){
            wp_enqueue_style( $handle, $fileinfo['bwdcf_path_admin_define'], null, '1.0', 'all');
        }
	}

	public function __construct() {
		// For public assets
		add_action('wp_enqueue_scripts', [$this, 'bwdcf_all_assets_for_the_public']);

		// For Elementor Editor
		add_action('elementor/editor/before_enqueue_scripts', [$this, 'bwdcf_all_assets_for_elementor_editor_admin']);
		
		// Register Category
		add_action( 'elementor/elements/categories_registered', [ $this, 'bwdcf_add_elementor_widget_categories' ] );

		// Register widgets
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'bwdcf_register_widgets' ] );

		// Register editor scripts
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'bwdcf_admin_editor_scripts' ] );
		
		$this->add_page_settings_controls();
	}
}

// Instantiate Plugin Class
ClassbwdcfContactForm::instance();