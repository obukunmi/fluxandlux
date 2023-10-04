<?php
namespace Creativecontact\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class bwdcfContactForm extends \Elementor\Widget_Base {


	public function get_name() {
		return 'bwbcf-contact';
	}

	public function get_title() {
		return esc_html__( 'Contact Form 7 Styler', 'bwdcf7-contact-form' );
	}

	public function get_icon() {
		return 'eicon-mail bwdcf-contact7';
	}

	public function get_categories() {
		return [ 'bwdcf-contact-category' ];
	}

    public function get_keywords() {
		return [ 'contact', 'form', '7', 'bwd' ];
	}

	public function get_script_depends() {
		return [ 'bwdcf-contact-category' ];
	}

	 function bwdcf_cf7form() {
        $wpcf7_form_list = get_posts( array(
            'post_type'	 => 'wpcf7_contact_form',
            'showposts'	 => 999,
        ) );
        $posts			 = array();
        if ( !empty( $wpcf7_form_list ) && !is_wp_error( $wpcf7_form_list ) ) {
			
            foreach ( $wpcf7_form_list as $post ) {
                $options[ $post->ID ] = $post->post_title;
            }
            return $options;
        }
    }
	
    protected function register_controls() {
		function bwdcf7_require_plugin_check(){
			return class_exists( '\WPCF7' );
		}
        $this->start_controls_section(
            'section_tab', [
                'label' => esc_html__( 'Contact Form 7', 'bwdcf7-contact-form' ),
            ]
        );
        if(! bwdcf7_require_plugin_check()):
            $this->add_control(
                'bwdcf7_missing_notice',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'raw' => '<a href="'.esc_url( admin_url( 'plugin-install.php?s=Contact+Form+7&tab=search&type=term' ) ).'" target="_blank" rel="noopener">'.esc_html__('Contact Form 7').'</a>'.esc_html__(' is missing. Please install/activate it.'),
                ]
            );
        else:
			$this->add_control(
				'bwdcf_contact_form7',
				[
					'label' =>esc_html__( 'Choose Contact Form', 'bwdcf7-contact-form' ),
					'type' => Controls_Manager::SELECT,
					'options' => ['' => esc_html__( 'Select Form', 'bwdcf7-contact-form')] + $this->bwdcf_cf7form()
				]
			);
		endif;

        $this->end_controls_section();
        $this->start_controls_section(
            'section_tab', [
                'label' =>esc_html__( 'Contact Form 7', 'bwdcf7-contact-form' ),
            ]
        );
        $this->add_control(
            'bwdcf_contact_form7',
            [
                'label' =>esc_html__( 'Choose Contact Form', 'bwdcf7-contact-form' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'accoedion-primary',
                'options' => $this->bwdcf_cf7form()
            ]
        );

		$this->add_control(
			'bwdcf_contact_form7_title',
			[
				'label' => __('Form Title', 'bwdcf7-contact-form'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'bwdcf7-contact-form'),
				'label_off' => __('Off', 'bwdcf7-contact-form'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'bwdcf_contact_form7_title_text',
			[
				'label' => esc_html__('Title', 'bwdcf7-contact-form'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'default' => 'Form Title Here',
				'condition' => [
					'bwdcf_contact_form7_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'bwdcf_contact_form7_description',
			[
				'label' => __('Form Description', 'bwdcf7-contact-form'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'bwdcf7-contact-form'),
				'label_off' => __('Off', 'bwdcf7-contact-form'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'bwdcf_contact_form7_description_text',
			[
				'label' => esc_html__('Description', 'bwdcf7-contact-form'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => 'Form Description Here',
				'condition' => [
					'bwdcf_contact_form7_description' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'bwdcf_contact_form7_column',
			[
				'label' => esc_html__( 'Form Column', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'colone',
				'options' => [
					'colone' => esc_html__( 'Column One', 'bwdcf7-contact-form' ),
					'coltwo' => esc_html__( 'Column Two', 'bwdcf7-contact-form' ),
					'colthree'  => esc_html__( 'Coloumn Three', 'bwdcf7-contact-form' ),
				],
				'prefix_class' => 'bwdcf-grid%s-'
			]
		);
		
		$this->add_control(
			'bwdcf_contact_form7_input_type_text',
			[
				'label' => esc_html__( 'Input Type Text Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bwdcf7-contact-form' ),
				'label_off' => esc_html__( 'Hide', 'bwdcf7-contact-form' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form7_input_type_text_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'bwdcf7-contact-form' ),
					'full' => esc_html__( 'Full Width', 'bwdcf7-contact-form' ),
					'half' => esc_html__( 'Half Width', 'bwdcf7-contact-form' ),
					'onethird'  => esc_html__( 'One Third', 'bwdcf7-contact-form' ),
				],
				'condition' => [
					'bwdcf_contact_form7_input_type_text' => 'yes',
				],
				'prefix_class' => 'bwdcf-inputtext-width%s-',
			]
		);
		$this->add_control(
			'bwdcf_contact_form7_input_type_email',
			[
				'label' => esc_html__( 'Input Type Email Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bwdcf7-contact-form' ),
				'label_off' => esc_html__( 'Hide', 'bwdcf7-contact-form' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form7_input_type_email_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'bwdcf7-contact-form' ),
					'full' => esc_html__( 'Full Width', 'bwdcf7-contact-form' ),
					'half' => esc_html__( 'Half Width', 'bwdcf7-contact-form' ),
					'onethird'  => esc_html__( 'One Third', 'bwdcf7-contact-form' ),
				],
				'condition' => [
					'bwdcf_contact_form7_input_type_email' => 'yes',
				],
				'prefix_class' => 'bwdcf-inputemail-width%s-',
			]
		);
		$this->add_control(
			'bwdcf_contact_form7_input_type_url',
			[
				'label' => esc_html__( 'Input Type URL Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bwdcf7-contact-form' ),
				'label_off' => esc_html__( 'Hide', 'bwdcf7-contact-form' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form7_input_type_url_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'bwdcf7-contact-form' ),
					'full' => esc_html__( 'Full Width', 'bwdcf7-contact-form' ),
					'half' => esc_html__( 'Half Width', 'bwdcf7-contact-form' ),
					'onethird'  => esc_html__( 'One Third', 'bwdcf7-contact-form' ),
				],
				'condition' => [
					'bwdcf_contact_form7_input_type_url' => 'yes',
				],
				'prefix_class' => 'bwdcf-inputurl-width%s-',
			]
		);
		$this->add_control(
			'bwdcf_contact_form7_input_type_tel',
			[
				'label' => esc_html__( 'Input Type TEL Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bwdcf7-contact-form' ),
				'label_off' => esc_html__( 'Hide', 'bwdcf7-contact-form' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form7_input_type_tel_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'bwdcf7-contact-form' ),
					'full' => esc_html__( 'Full Width', 'bwdcf7-contact-form' ),
					'half' => esc_html__( 'Half Width', 'bwdcf7-contact-form' ),
					'onethird'  => esc_html__( 'One Third', 'bwdcf7-contact-form' ),
				],
				'condition' => [
					'bwdcf_contact_form7_input_type_tel' => 'yes',
				],
				'prefix_class' => 'bwdcf-inputtel-width%s-',
			]
		);
		$this->add_control(
			'bwdcf_contact_form7_input_type_number',
			[
				'label' => esc_html__( 'Input Type Number Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bwdcf7-contact-form' ),
				'label_off' => esc_html__( 'Hide', 'bwdcf7-contact-form' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form7_input_type_number_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'bwdcf7-contact-form' ),
					'full' => esc_html__( 'Full Width', 'bwdcf7-contact-form' ),
					'half' => esc_html__( 'Half Width', 'bwdcf7-contact-form' ),
					'onethird'  => esc_html__( 'One Third', 'bwdcf7-contact-form' ),
				],
				'condition' => [
					'bwdcf_contact_form7_input_type_number' => 'yes',
				],
				'prefix_class' => 'bwdcf-inputnumber-width%s-',
			]
		);
		$this->add_control(
			'bwdcf_contact_form7_input_type_date',
			[
				'label' => esc_html__( 'Input Type Date Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bwdcf7-contact-form' ),
				'label_off' => esc_html__( 'Hide', 'bwdcf7-contact-form' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form7_input_type_date_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'bwdcf7-contact-form' ),
					'full' => esc_html__( 'Full Width', 'bwdcf7-contact-form' ),
					'half' => esc_html__( 'Half Width', 'bwdcf7-contact-form' ),
					'onethird'  => esc_html__( 'One Third', 'bwdcf7-contact-form' ),
				],
				'condition' => [
					'bwdcf_contact_form7_input_type_date' => 'yes',
				],
				'prefix_class' => 'bwdcf-inputdate-width%s-',
			]
		);
		$this->add_control(
			'bwdcf_contact_form7_input_type_select',
			[
				'label' => esc_html__( 'Input Type Select Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bwdcf7-contact-form' ),
				'label_off' => esc_html__( 'Hide', 'bwdcf7-contact-form' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form7_input_type_select_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'bwdcf7-contact-form' ),
					'full' => esc_html__( 'Full Width', 'bwdcf7-contact-form' ),
					'half' => esc_html__( 'Half Width', 'bwdcf7-contact-form' ),
					'onethird'  => esc_html__( 'One Third', 'bwdcf7-contact-form' ),
				],
				'condition' => [
					'bwdcf_contact_form7_input_type_select' => 'yes',
				],
				'prefix_class' => 'bwdcf-inputselect-width%s-',
			]
		);
		$this->add_control(
			'bwdcf_contact_form7_input_type_textarea',
			[
				'label' => esc_html__( 'Input Type Textarea Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bwdcf7-contact-form' ),
				'label_off' => esc_html__( 'Hide', 'bwdcf7-contact-form' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form7_input_type_textarea_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'bwdcf7-contact-form' ),
					'full' => esc_html__( 'Full Width', 'bwdcf7-contact-form' ),
					'half' => esc_html__( 'Half Width', 'bwdcf7-contact-form' ),
					'onethird'  => esc_html__( 'One Third', 'bwdcf7-contact-form' ),
				],
				'condition' => [
					'bwdcf_contact_form7_input_type_textarea' => 'yes',
				],
				'prefix_class' => 'bwdcf-inputtextarea-width%s-',
			]
		);
        $this->end_controls_section();

		$this->start_controls_section(
			'bwdcf_contact_form_container_style',
			[
				'label' => esc_html__( 'Form Container', 'bwdcf7-contact-form' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'bwdcf_contact_form_container_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bwdcf7-wrapper',
			]
		);
		$this->add_responsive_control(
            'bwdcf_contact_form_container_alignment',
            [
                'label' => esc_html__('Form Alignment', 'bwdcf7-contact-form'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'bwdcf7-contact-form'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bwdcf7-contact-form'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'bwdcf7-contact-form'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
				'prefix_class' => 'bwdcf-form-alignment-',
            ]
        );

        $this->add_responsive_control(
            'bwdcf_contact_form_container_width',
            [
                'label' => esc_html__('Form Width', 'bwdcf7-contact-form'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 1500,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bwdcf7-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
			'bwdcf_contact_form_container_padding',
			[
				'label' => esc_html__( 'Padding', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_container_margin',
			[
				'label' => esc_html__( 'Margin', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bwdcf_contact_form_container_border',
				'selector' => '{{WRAPPER}} .bwdcf7-wrapper',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_container_border-radius',
			[
				'label' => esc_html__( 'Border Radius', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bwdcf_contact_form_container_box_shadow',
				'selector' => '{{WRAPPER}} .bwdcf7-wrapper',
			]
		);
		$this->end_controls_section();

		// Form Title And Description

		$this->start_controls_section(
			'bwdcf_contact_form_title_desc',
			[
				'label' => esc_html__( 'Title & Description', 'bwdcf7-contact-form' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_title_desc_alignment',
			[
				'label'    => esc_html__( 'Alignment', 'bwdcf7-contact-form' ),
				'type'     => Controls_Manager::CHOOSE,
				'options'  => [
					'start'   => [
						'title' => esc_html__( 'Left', 'bwdcf7-contact-form' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bwdcf7-contact-form' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'  => [
						'title' => esc_html__( 'Right', 'bwdcf7-contact-form' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'       => true,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-wrapper .bwdcf-title, {{WRAPPER}} .bwdcf7-wrapper .bwdcf-desc' => 'text-align: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'bwdcf_contact_form_title_heading',
			[
				'label' => esc_html__( 'Form Title', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'bwdcf_contact_form_title_typography',
				'selector' => '{{WRAPPER}} .bwdcf7-wrapper .bwdcf-title',
			]
		);
		$this->add_control(
			'bwdcf_contact_form_title_color',
			[
				'label' => esc_html__( 'Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-wrapper .bwdcf-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'bwdcf_contact_form_title_text_shadow',
				'selector' => '{{WRAPPER}} .bwdcf7-wrapper .bwdcf-title',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_title_margin',
			[
				'label' => esc_html__( 'Margin', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-wrapper .bwdcf-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'bwdcf_contact_form_desc_heading',
			[
				'label' => esc_html__( 'Form Description', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'bwdcf_contact_form_desc_typography',
				'selector' => '{{WRAPPER}} .bwdcf7-wrapper .bwdcf-desc',
			]
		);
		$this->add_control(
			'bwdcf_contact_form_desc_color',
			[
				'label' => esc_html__( 'Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-wrapper .bwdcf-desc' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_desc_margin',
			[
				'label' => esc_html__( 'Margin', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-wrapper .bwdcf-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
        // label
		$this->start_controls_section(
			'bwdcf_contact_form_input_label_style',
			[
				'label' => esc_html__( 'Label', 'bwdcf7-contact-form' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_label_typography',
				'label' => esc_html__( 'Typography', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form label',
			]
		);

		$this->add_control(
			'bwdcf_contact_form_input_label_color',
			[
				'label' => esc_html__( 'Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form label' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_input_label_margin_top',
			[
				'label' => esc_html__( 'Top Spacing', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form label' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
        );
		$this->add_responsive_control(
			'bwdcf_contact_form_input_label_margin_bottom',
			[
				'label' => esc_html__( 'Bottom Spacing', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form .wpcf7-form-control-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
        );
		$this->end_controls_section();

		// input style
		$this->start_controls_section(
			'bwdcf_contact_form_input_style',
			[
				'label' => esc_html__( 'Input', 'bwdcf7-contact-form' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'bwdcf_contact_form_input_field_style',
			[
				'label' => esc_html__( 'Field Style', 'bwdcf7-contact-form' ),
				'type'  => Controls_Manager::SELECT,
				'default' => 'box',
				'options' => [
					'box' => esc_html__( 'Box', 'bwdcf7-contact-form' ),
					'underline' => esc_html__( 'Underline', 'bwdcf7-contact-form' ),
				],
				'prefix_class' => 'bwdcf7-contact-form-cf7-style-',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_input_underline_width',
			[
				'label' => esc_html__( 'Underline Width', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), {{WRAPPER}} .bwdcf7-form form select' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form textarea' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'bwdcf_contact_form_input_field_style' => 'underline',
				],
			]
		);
		$this->add_control(
			'bwdcf_contact_form_input_underline_color',
			[
				'label' => esc_html__( 'Underline Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), {{WRAPPER}} .bwdcf7-form form select' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .bwdcf7-form form textarea' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'bwdcf_contact_form_input_field_style' => 'underline',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_input_style_padding',
			[
				'label' => esc_html__( 'Padding', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]), {{WRAPPER}} .bwdcf7-form form select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'bwdcf_contact_form_input_style_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 98,
				],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]), {{WRAPPER}} .bwdcf7-form form select' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form textarea' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'bwdcf_contact_form_input_style_height',
			[
				'label' => esc_html__( 'Height', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]), {{WRAPPER}} .bwdcf7-form form select' => 'height: {{SIZE}}px;',
					'{{WRAPPER}} .bwdcf7-form form textarea' => 'height: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'bwdcf_contact_form_input_style_margin_bottom',
			[
				'label' => esc_html__( 'Margin Bottom', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form .bwdcf7-form-input, {{WRAPPER}} .bwdcf7-form form select, {{WRAPPER}} .bwdcf7-form form input:not([type="submit"])' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form textarea' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
        );
        $this->add_control(
			'bwdcf_contact_form_input_style_textarea_heading',
			[
				'label' => esc_html__( 'Textarea', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
        $this->add_responsive_control(
			'bwdcf_contact_form_input_style_textarea_height',
			[
				'label' => esc_html__( 'Height', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 176,
				],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form textarea' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'bwdcf_contact_form_input_style_padding_textarea',
			[
				'label' => esc_html__( 'Padding', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'bwdcf_contact_form_input_style_select_heading',
			[
				'label' => esc_html__( 'Select Option', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		 $this->add_group_control(
           \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'bwdcf_contact_form_input_select_typography',
                'label' => esc_html__( 'Typography', 'bwdcf7-contact-form' ),
                'selector' => '{{WRAPPER}} .bwdcf7-form form select option',
            ]
        );
		$this->add_control(
			'bwdcf_contact_form_input_select_option_color',
			[
				'label' => esc_html__( 'Option Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form select option' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'bwdcf_contact_form_input_select_background_color',
			[
				'label' => esc_html__( 'Option Background Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form select option' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'bwdcf_contact_form_input_style_padding_textarea_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

        $this->start_controls_tabs(
            'bwdcf_contact_form_input_normal_and_hover_tabs'
        );
        $this->start_controls_tab(
            'bwdcf_contact_form_input_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'bwdcf7-contact-form' ),
            ]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_style_background',
				'label' => esc_html__( 'Background', 'bwdcf7-contact-form' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]) ,{{WRAPPER}} .bwdcf7-form form textarea, {{WRAPPER}} .bwdcf7-form form select',
			]
		);
		$this->add_control(
			'bwdcf_contact_form_input_cursor_color',
			[
				'label' => esc_html__( 'Cursor Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]),
					{{WRAPPER}} .bwdcf7-form form textarea' => 'caret-color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_input_style_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), {{WRAPPER}} .bwdcf7-form form select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_style_border',
				'label' => esc_html__( 'Border', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), {{WRAPPER}} .bwdcf7-form form textarea, {{WRAPPER}} .bwdcf7-form form select',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_style_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'bwdcf7-contact-form' ),
                'selector' => '
                            {{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]),
                            {{WRAPPER}} .bwdcf7-form form textarea, {{WRAPPER}} .bwdcf7-form form select'
                            ,
			]
		);

		$this->end_controls_tab();
        $this->start_controls_tab(
            'bwdcf_contact_form_input_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'bwdcf7-contact-form' ),
            ]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_hover_style_background',
				'label' => esc_html__( 'Background', 'bwdcf7-contact-form' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):hover ,{{WRAPPER}} .bwdcf7-form form textarea:hover, {{WRAPPER}} .bwdcf7-form form select:hover',
				'exclude' => [
					'image'
				]
			]
		);

		$this->add_responsive_control(
			'bwdcf_contact_form_input_hover_style_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):hover, {{WRAPPER}} .bwdcf7-form form select:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form textarea:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_hover_style_border',
				'label' => esc_html__( 'Border', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):hover, {{WRAPPER}} .bwdcf7-form form textarea:hover, {{WRAPPER}} .bwdcf7-form form select:hover',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_hover_style_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'bwdcf7-contact-form' ),
                'selector' => '
                            {{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):hover,
                            {{WRAPPER}} .bwdcf7-form form textarea:hover, {{WRAPPER}} .bwdcf7-form form select:hover'
                            ,
			]
		);

		$this->end_controls_tab();
        $this->start_controls_tab(
            'bwdcf_contact_form_input_focus_tab',
            [
                'label' => esc_html__( 'Focus', 'bwdcf7-contact-form' ),
            ]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_focus_style_background',
				'label' => esc_html__( 'Background', 'bwdcf7-contact-form' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):focus ,{{WRAPPER}} .bwdcf7-form form textarea:focus, {{WRAPPER}} .bwdcf7-form form select:focus',
				'exclude' => [
					'image'
				]
			]
		);

		$this->add_responsive_control(
			'bwdcf_contact_form_input_focus_style_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):focus, {{WRAPPER}} .bwdcf7-form form select:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form textarea:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_focus_style_border',
				'label' => esc_html__( 'Border', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):focus, {{WRAPPER}} .bwdcf7-form form textarea:focus, {{WRAPPER}} .bwdcf7-form form select:focus',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bwdcf_contact_form_input_focus_style_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'bwdcf7-contact-form' ),
                'selector' => ' {{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):focus,
                               {{WRAPPER}} .bwdcf7-form form textarea:focus, {{WRAPPER}} .bwdcf7-form form select:focus' ,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->add_control(
            'bwdcf_contact_form_input_style_typography_heading',
            [
                'label' => esc_html__( 'Input Default Value', 'bwdcf7-contact-form' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
           \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'bwdcf_contact_form_input_typography',
                'label' => esc_html__( 'Typography', 'bwdcf7-contact-form' ),
                'selector' => '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), .wpcf7-form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), .wpcf7-form textarea, .bwdcf7-wrapper .bwdcf7-form form textarea, {{WRAPPER}} .bwdcf7-form form select',
            ]
        );

        $this->add_control(
            'bwdcf_contact_form_input_style_font_color',
            [
                'label' => esc_html__( 'Color', 'bwdcf7-contact-form' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), {{WRAPPER}} .bwdcf7-form form select' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .wpcf7-form textarea' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bwdcf7-wrapper .bwdcf7-form form textarea' => 'color: {{VALUE}}',
                ],
            ]
        );
		$this->end_controls_section();
		$this->start_controls_section(
			'bwdcf_contact_form_placeholder_style',
			[
				'label' => __( 'Placeholder', 'bwdcf7-contact-form' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_input_style_placeholder_font_size',
			[
				'label' => esc_html__( 'Font Size', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 14,
				],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"])::-webkit-input-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"])::-moz-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):-ms-input-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):-moz-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .bwdcf7-form form textarea::-webkit-input-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form textarea::-moz-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form textarea:-ms-input-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bwdcf7-form form textarea:-moz-placeholder' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
            'bwdcf_contact_form_input_placeholder_font_color',
            [
                'label' => esc_html__( 'Placeholder Color', 'bwdcf7-contact-form' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"])::-webkit-input-placeholder' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"])::-moz-placeholder' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):-ms-input-placeholder' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bwdcf7-form form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]):-moz-placeholder' => 'color: {{VALUE}}',

                    '{{WRAPPER}} .bwdcf7-form form textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bwdcf7-form form textarea::-moz-placeholder' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bwdcf7-form form textarea:-ms-input-placeholder' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bwdcf7-form form textarea:-moz-placeholder' => 'color: {{VALUE}}',
                ],
            ]
        );
      $this->end_controls_section();

		$this->start_controls_section(
			'bwdcf_contact_form_radio_style',
			[
				'label' => __( 'Radio Button', 'bwdcf7-contact-form' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_radio_layout',
			[
				'label' => esc_html__( 'Layout', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => [
					'inline' => esc_html__( 'Inline', 'bwdcf7-contact-form' ),
					'block' => esc_html__( 'Vertical ', 'bwdcf7-contact-form' ),
				],
				'prefix_class' => 'bwdcf-radio-layout%s-'
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_radio_button_size',
			[
				'label' => esc_html__( 'Size', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="radio"]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_radio_button_check_size',
			[
				'label' => esc_html__( 'Inner Checked Size', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="radio"]:checked' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'bwdcf_contact_form_radio_button_color',
			[
				'label' => esc_html__( 'Radio Button Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="radio"]' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'bwdcf_contact_form_radio_inner_button_color',
			[
				'label' => esc_html__( 'Radio Checked Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="radio"]:checked' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_radio_button_lavel_spacing',
			[
				'label' => esc_html__( 'Label Spacing', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form .wpcf7-radio label' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_radio_margin',
			[
				'label' => esc_html__( 'Margin', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form .wpcf7-form-control.wpcf7-radio' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; display: block;',
				],
			]
		);
		$this->end_controls_section();

		// Checkbox Button Start Here

		$this->start_controls_section(
			'bwdcf_contact_form_checkbox_style',
			[
				'label' => __( 'Checkbox', 'bwdcf7-contact-form' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_checkbox_layout',
			[
				'label' => esc_html__( 'Layout', 'bwdcf7-contact-form' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => [
					'inline' => esc_html__( 'Inline', 'bwdcf7-contact-form' ),
					'block' => esc_html__( 'Vertical ', 'bwdcf7-contact-form' ),
				],
				'prefix_class' => 'bwdcf-checkbox-layout%s-'
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_checkbox_button_size',
			[
				'label' => esc_html__( 'Size', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="checkbox"]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_checkbox_button_check_size',
			[
				'label' => esc_html__( 'Inner Arrow Size', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="checkbox"]::after' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'bwdcf_contact_form_checkbox_arrow_color',
			[
				'label' => esc_html__( 'Arrow Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="checkbox"]::after' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bwdcf_contact_form_checkbox_border_color',
				'label' => esc_html__( 'Border', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="checkbox"]',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_checkbox_lavel_spacing',
			[
				'label' => esc_html__( 'Label Spacing', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form .wpcf7-checkbox label' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_checkbox_margin',
			[
				'label' => esc_html__( 'Margin', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form .wpcf7-form-control.wpcf7-checkbox' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; display: block;',
				],
			]
		);
		$this->end_controls_section();

		// File Upload Button Start Here

		$this->start_controls_section(
			'bwdcf_contact_form_file_upload_style',
			[
				'label' => __( 'File Upload', 'bwdcf7-contact-form' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'bwdcf_contact_form_file_upload_typography',
				'label' => esc_html__( 'Typography', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="file"]::file-selector-button, {{WRAPPER}} .bwdcf7-form form input[type="file"]::-webkit-file-upload-button',
			]
		);
		$this->add_control(
			'bwdcf_contact_form_file_upload_color',
			[
				'label' => esc_html__( 'Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="file"]::file-selector-button, 
					{{WRAPPER}} .bwdcf7-form form input[type="file"]::-webkit-file-upload-button' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'bwdcf_contact_form_file_upload_bg_color',
				'label' => esc_html__( 'Background', 'bwdcf7-contact-form' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="file"]::file-selector-button, 
					{{WRAPPER}} .bwdcf7-form form input[type="file"]::-webkit-file-upload-button',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bwdcf_contact_form_file_upload_border',
				'label' => esc_html__( 'Border', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="file"]::file-selector-button, 
							  {{WRAPPER}} .bwdcf7-form form input[type="file"]::-webkit-file-upload-button',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_file_upload_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="file"]::file-selector-button, 
					{{WRAPPER}} .bwdcf7-form form input[type="file"]::-webkit-file-upload-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_file_upload_padding',
			[
				'label' => esc_html__( 'Padding', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="file"]::file-selector-button, 
					{{WRAPPER}} .bwdcf7-form form input[type="file"]::-webkit-file-upload-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_file_upload_margin',
			[
				'label' => esc_html__( 'Margin', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="file"]::file-selector-button, 
					{{WRAPPER}} .bwdcf7-form form input[type="file"]::-webkit-file-upload-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'bwdcf_contact_form_submit_button_style',
			[
				'label' => esc_html__( 'Submit Button', 'bwdcf7-contact-form' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_button_alignment',
			[
				'label'    => esc_html__( 'Alignment', 'bwdcf7-contact-form' ),
				'type'     => Controls_Manager::CHOOSE,
				'options'  => [
					'left'   => [
						'title' => esc_html__( 'Left', 'bwdcf7-contact-form' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bwdcf7-contact-form' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'bwdcf7-contact-form' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'bwdcf7-contact-form' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'  => 'left',
				'prefix_class' => 'bwd%s-cf7-button-',
				'toggle'       => false,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'bwdcf_contact_form_button_typography',
				'label' => esc_html__( 'Typography', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="submit"]',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bwdcf_contact_form_button_border',
				'label' => esc_html__( 'Border', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="submit"]',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_button_border_padding',
			[
				'label' => esc_html__( 'Padding', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_button_style_margin',
			[
				'label' => esc_html__( 'Margin Top', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="submit"]' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'bwdcf_contact_form_button_style_use_width_height',
			[
				'label' => esc_html__( 'Use Height Width', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bwdcf7-contact-form' ),
				'label_off' => esc_html__( 'Hide', 'bwdcf7-contact-form' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_button_width',
			[
				'label' => esc_html__( 'Width', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="submit"]' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'bwdcf_contact_form_button_style_use_width_height' => 'yes'
				]
			]
		);
		$this->add_responsive_control(
			'bwdcf_contact_form_button_style_height',
			[
				'label' => esc_html__( 'Height', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="submit"]' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'bwdcf_contact_form_button_style_use_width_height' => 'yes'
				]
			]
		);
		$this->start_controls_tabs(
            'bwdcf_contact_form_button_normal_and_hover_tabs'
        );
        $this->start_controls_tab(
            'bwdcf_contact_form_button_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'bwdcf7-contact-form' ),
            ]
		);
		$this->add_control(
			'bwdcf_contact_form_button_color',
			[
				'label' => esc_html__( 'Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="submit"]' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'bwdcf_contact_form_button_background',
				'label' => esc_html__( 'Background', 'bwdcf7-contact-form' ),
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="submit"]',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bwdcf_contact_form_button_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="submit"]',
			]
		);
		$this->add_group_control(
           \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'bwdcf_contact_form_button_title_shadow',
                'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="submit"]' ,
            ]
		);
		$this->end_controls_tab();
        $this->start_controls_tab(
            'bwdcf_contact_form_button_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'bwdcf7-contact-form' ),
            ]
		);

		$this->add_control(
			'bwdcf_contact_form_button_color_hover',
			[
				'label' => esc_html__( 'Color', 'bwdcf7-contact-form' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bwdcf7-form form input[type="submit"]:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'bwdcf_contact_form_button_hover_background',
				'label' => esc_html__( 'Background', 'bwdcf7-contact-form' ),
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="submit"]:hover',
				'exclude' => [
					'image'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bwdcf_contact_form_button_box_shadow_hover',
				'label' => esc_html__( 'Box Shadow', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="submit"]:hover',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'bwdcf_contact_form_button_border_hover',
				'label' => esc_html__( 'Border', 'bwdcf7-contact-form' ),
				'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="submit"]:hover',
			]
		);

		$this->add_group_control(
           \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'bwdcf_contact_form_button_title_shadow_hover',
                'selector' => '{{WRAPPER}} .bwdcf7-form form input[type="submit"]:hover' ,
            ]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
    }

    protected function render( ) {
		if( ! bwdcf7_require_plugin_check() ):
			echo '<div class="bwdcf7-plugin-notice">'.esc_html__( 'Contact Form 7 plugin is missing in your site. Please install/activate the plugin.').'</div>';
		else:
        echo '<div class="bwdcf7-wrapper" >';
            $this->render_raw();
        echo '</div>';
		endif;
    }

    protected function render_raw( ) {
        $settings = $this->get_settings();
		$form_title = $settings['bwdcf_contact_form7_title_text'];
		$form_desc = $settings['bwdcf_contact_form7_description_text'];

		if($settings['bwdcf_contact_form7_title'] === 'yes'){echo '<h1 class="bwdcf-title">'.esc_html__($form_title).'</h1>';}
		if($settings['bwdcf_contact_form7_description'] === 'yes'){echo '<p class="bwdcf-desc">'.esc_html__($form_desc).'</p>';}

		if(!empty($settings['bwdcf_contact_form7'])):
		echo '<div class="bwdcf7-form">';
				echo do_shortcode('[contact-form-7 id="'.intval($settings['bwdcf_contact_form7']).'"]' );
		echo '</div>';
		else:
			echo '<div class="bwdcf7-plugin-notice">'.esc_html__( 'Please select a form.').'</div>';
		endif;
	}
}

