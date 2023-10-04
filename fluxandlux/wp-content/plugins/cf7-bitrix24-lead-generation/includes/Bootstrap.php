<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Includes;

use Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\IntegrationSettingsPage;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\LogHelper;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\PluginActionLinksFilter;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\SupportDuplicateForm;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\CF7 as CF7Includes;
use Itgalaxy\PluginCommon\AnalyticsHelper;
use Itgalaxy\PluginCommon\CacheHelper;
use Itgalaxy\PluginCommon\DependencyPluginChecker;
use Itgalaxy\PluginCommon\MainHelperLoader;

class Bootstrap
{
    const PLUGIN_ID = '20288214';
    const PLUGIN_VERSION = '2.40.0-552';

    const OPTIONS_KEY = 'cf7-bitrix-lead-generation-settings';
    const PURCHASE_CODE_OPTIONS_KEY = 'cf7-bitrix-purchase-code';

    const META_KEY = '_cf7-bitrix-lead-generation';

    const LEAD_FIELDS_KEY = '_cf7-bitrix-lead-fields';

    const DEAL_FIELDS_KEY = '_cf7-bitrix-deal-fields';
    const DEAL_CATEGORY_LIST_KEY = '_cf7-bitrix-deal-category-list';

    const TASK_FIELDS_KEY = '_cf7-bitrix-task-fields';
    const CONTACT_FIELDS_KEY = '_cf7-bitrix-contact-fields';
    const COMPANY_FIELDS_KEY = '_cf7-bitrix-company-fields';
    const STATUS_LIST_KEY = '_cf7-bitrix-status-list';
    const CURRENCY_LIST_KEY = '_cf7-bitrix-currency-list';

    const UTM_COOKIES = 'cf7-bitrix-utm-cookie';
    const CRON = 'cf7-bitrix-cron';
    const SEND_CRON_TASK = 'itglx/cf7/bx24/send-cron-task';

    const DEPENDENCY_PLUGIN_LIST = ['contact-form-7/wp-contact-form-7.php'];

    /**
     * @var string Absolute path (with a trailing slash) to the plugin directory.
     */
    public static $pluginDir;

    /**
     * @var string URL to the plugin directory (with a trailing slash).
     */
    public static $pluginUrl;

    /**
     * @var string Absolute path to the file for log content.
     */
    public static $pluginLogFile;

    /**
     * @var MainHelperLoader
     */
    public static $common;

    public static $plugin = '';

    private static $instance = false;

    protected function __construct($file)
    {
        if (!defined('CF7_BITRIX24_PLUGIN_LOG_FILE')) {
            define('CF7_BITRIX24_PLUGIN_LOG_FILE', wp_upload_dir()['basedir'] . '/logs/cf7bx24_' . md5(get_option('siteurl')) . '.log');
        }

        self::$plugin = $file;
        self::$pluginDir = \plugin_dir_path(self::$plugin);
        self::$pluginUrl = \plugin_dir_url(self::$plugin);
        self::$pluginLogFile = CF7_BITRIX24_PLUGIN_LOG_FILE;
        self::$common = new MainHelperLoader($this);

        \register_activation_hook(self::$plugin, [self::class, 'pluginActivation']);
        \register_deactivation_hook(self::$plugin, [self::class, 'pluginDeactivation']);

        if (!DependencyPluginChecker::isActivated(self::DEPENDENCY_PLUGIN_LIST)) {
            DependencyPluginChecker::showRequirementPluginsNotice(
                esc_html__('Contact Form 7 - Bitrix24 CRM', 'cf7-bitrix24-integration'),
                self::DEPENDENCY_PLUGIN_LIST
            );

            return;
        }

        new Updater($this);

        Cron::getInstance();
        CF7Includes::getInstance();
        Bitrix24Select::getInstance();

        if (is_admin()) {
            add_action('plugins_loaded', function () {
                new FormSettings();
                new IntegrationSettingsPage();

                // add links to plugin list
                PluginActionLinksFilter::getInstance();
                LogHelper::getInstance();
                SupportDuplicateForm::getInstance();
            });
        }

        if (apply_filters('itglx_cf7_bx24_save_utm', true)) {
            add_action('init', [$this, 'utmCookies']);
            add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);

            add_action('wp_ajax_cf7Bitrix24AjaxSetUtm', [$this, 'utmCookies']);
            add_action('wp_ajax_nopriv_cf7Bitrix24AjaxSetUtm', [$this, 'utmCookies']);
        }
    }

    private function __clone()
    {
        // Nothing
    }

    public static function getInstance($file)
    {
        if (!self::$instance) {
            self::$instance = new self($file);
        }

        return self::$instance;
    }

    public function utmCookies()
    {
        $utmParams = AnalyticsHelper::getUtmListFromUrl();

        if (!empty($utmParams)) {
            setcookie(
                self::UTM_COOKIES,
                wp_json_encode($utmParams),
                time() + apply_filters('itglx_cf7_bx24_count_seconds_save_utm_cookies', 86400),
                '/'
            );
        }
    }

    public function enqueueScripts()
    {
        if (!CacheHelper::siteUsesCache()) {
            return;
        }

        wp_enqueue_script('cf7-bitrix24-theme', self::$common->assetsHelper->getPathAssetFile('/theme/js/app.js'), [], null, true);
    }

    public static function pluginActivation()
    {
        self::$common->requester->call('plugin_activate');

        DependencyPluginChecker::activateHelper(
            self::$plugin,
            self::DEPENDENCY_PLUGIN_LIST,
            esc_html__('Contact Form 7 - Bitrix24 CRM', 'cf7-bitrix24-integration')
        );

        self::$common->logger->prepare();
    }

    public static function pluginDeactivation()
    {
        self::$common->requester->call('plugin_deactivate');
        \wp_clear_scheduled_hook(self::CRON);
    }

    public static function pluginUninstall()
    {
        self::$common->requester->call('plugin_uninstall');
    }
}
