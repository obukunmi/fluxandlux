<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\AjaxActions;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\PluginCommon\AdminGenerator\Themes\OurStore;

class LicenseAjaxAction
{
    /**
     * @var string
     */
    public static $name = 'itglx/cf7/bx24/license';

    public function __construct()
    {
        add_action('wp_ajax_' . self::$name, [$this, 'action']);
    }

    /**
     * @return void
     */
    public function action()
    {
        if (!current_user_can('wpcf7_manage_integration')) {
            exit();
        }

        if (isset($_POST['code'])) {
            $response = Bootstrap::$common->requester->code(
                isset($_POST['type']) && $_POST['type'] === 'verify' ? 'code_activate' : 'code_deactivate',
                trim(wp_unslash($_POST['code']))
            );

            if ($response['state'] == 'successCheck') {
                OurStore::callout(esc_html($response['message']), 'success');
            } elseif ($response['message']) {
                OurStore::callout(esc_html($response['message']), 'danger');
            }
        }

        OurStore::licenseBlock(
            \get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, ''),
            self::$name
        );

        exit();
    }
}
