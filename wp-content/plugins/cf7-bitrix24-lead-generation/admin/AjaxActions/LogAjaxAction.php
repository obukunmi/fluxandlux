<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\AjaxActions;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;

class LogAjaxAction
{
    /**
     * @var string
     */
    public static $enableLogActionName = 'itglx/cf7/bx24/enable-log';

    /**
     * @var string
     */
    public static $clearLogActionName = 'itglx/cf7/bx24/clear-log';

    public function __construct()
    {
        add_action('wp_ajax_' . self::$clearLogActionName, [$this, 'clear']);
        add_action('wp_ajax_' . self::$enableLogActionName, [$this, 'enable']);
    }

    /**
     * @return void
     */
    public function clear()
    {
        if (!current_user_can('wpcf7_manage_integration')) {
            exit();
        }

        Bootstrap::$common->logger->ajaxLogsClear();
    }

    /**
     * @return void
     */
    public function enable()
    {
        if (!current_user_can('wpcf7_manage_integration')) {
            exit();
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        if (!is_array($settings)) {
            $settings = [];
        }

        $settings['enabled_logging'] = isset($_POST['value']) ? (int) $_POST['value'] : '';

        update_option(Bootstrap::OPTIONS_KEY, $settings);

        \wp_send_json_success(
            [
                'message' => $settings['enabled_logging']
                    ? \esc_html__('Logging enabled successfully', 'cf7-bitrix24-integration')
                    : \esc_html__('Logging disabled successfully', 'cf7-bitrix24-integration'),
            ]
        );
    }
}
