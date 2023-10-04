<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin;

use Itgalaxy\Cf7\Bitrix24\Integration\Admin\AjaxActions\LicenseAjaxAction;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\AjaxActions\LogAjaxAction;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\PageParts\AuthorizePagePart;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\PageParts\HeaderPagePart;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\PageParts\ReloadFieldsPagePart;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bitrix24;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\PluginCommon\AdminGenerator\Elements\Root;
use Itgalaxy\PluginCommon\AdminGenerator\Themes\OurStore;

class IntegrationSettingsPage
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addSubmenu'], 1000); // 1000 - fix priority for Admin Menu Editor
        add_action('init', [$this, 'submitListener']);
        add_action('admin_notices', [$this, 'notice']);

        if (isset($_GET['page']) && $_GET['page'] === Bootstrap::OPTIONS_KEY) {
            add_action('admin_enqueue_scripts', function () {
                OurStore::enqueueStyle();
                OurStore::enqueueScript();
            });
        }

        new LicenseAjaxAction();
        new LogAjaxAction();
    }

    /**
     * @return void
     */
    public function notice()
    {
        if (\get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY)) {
            return;
        }

        echo sprintf(
            '<div class="notice notice-error" data-ui-component="itglx-license-notice"><p><strong>%1$s</strong>: %2$s <a href="%3$s">%4$s</a></p></div>',
            esc_html__('Contact Form 7 - Bitrix24 CRM - Integration', 'cf7-bitrix24-integration'),
            esc_html__(
                'Please verify the purchase code on the plugin settings page - ',
                'cf7-bitrix24-integration'
            ),
            esc_url(admin_url() . 'admin.php?page=' . Bootstrap::OPTIONS_KEY . '#itglx-license-verify'),
            esc_html__('open', 'cf7-bitrix24-integration')
        );
    }

    /**
     * @return void
     */
    public function addSubmenu()
    {
        add_submenu_page(
            'wpcf7',
            esc_html__('Bitrix24', 'cf7-bitrix24-integration'),
            esc_html__('Bitrix24', 'cf7-bitrix24-integration'),
            'wpcf7_manage_integration',
            Bootstrap::OPTIONS_KEY,
            [$this, 'settingsPage']
        );
    }

    /**
     * @return void
     */
    public function submitListener()
    {
        if (!current_user_can('wpcf7_manage_integration')) {
            return;
        }

        if (isset($_POST['cf7Bitrix24ReloadFieldsCache'])) {
            Bitrix24::updateInformation();

            wp_safe_redirect(admin_url() . 'admin.php?page=' . Bootstrap::OPTIONS_KEY . '&success-fields-reload');

            exit();
        }

        if (!isset($_POST['cf7Bitrix24Submit'])) {
            return;
        }

        $webhook = isset($_POST['webhook']) ? trim(wp_unslash($_POST['webhook'])) : '';
        $webhook = trailingslashit($webhook);

        if (filter_var($webhook, FILTER_VALIDATE_URL) === false) {
            wp_safe_redirect(admin_url() . 'admin.php?page=' . Bootstrap::OPTIONS_KEY . '&error-required-fields');

            exit();
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        update_option(
            Bootstrap::OPTIONS_KEY,
            [
                'webhook' => $webhook,
                'enabled_logging' => isset($settings['enabled_logging']) ? $settings['enabled_logging'] : '',
                'send_type' => isset($_POST['send_type']) ? trim(wp_unslash($_POST['send_type'])) : '',
            ]
        );

        Bitrix24::checkConnection();
        Bitrix24::updateInformation();

        wp_safe_redirect(admin_url() . 'admin.php?page=' . Bootstrap::OPTIONS_KEY . '&success');

        exit();
    }

    /**
     * @return void
     */
    public function settingsPage()
    {
        OurStore::rootWrapperStart();

        // generate base js variables
        Root::render(['type' => 'options']);

        HeaderPagePart::render();
        AuthorizePagePart::render();
        ReloadFieldsPagePart::render();

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        OurStore::logsBlock(
            [
                'enabled' => !empty($settings['enabled_logging']),
                'enableAction' => LogAjaxAction::$enableLogActionName,
                'downloadUrl' => admin_url() . '?' . Bootstrap::OPTIONS_KEY . '-logs-get=' . uniqid(),
                'clearAction' => LogAjaxAction::$clearLogActionName,
                'filePath' => Bootstrap::$pluginLogFile,
            ]
        );

        OurStore::licenseBlock(
            \get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, ''),
            LicenseAjaxAction::$name
        );

        OurStore::rootWrapperEnd();
    }
}
