<?php
/**
 * Contact Form 7 - Bitrix24 CRM - Integration.
 *
 * @author itgalaxycompany
 *
 * @wordpress-plugin
 * Plugin Name: Contact Form 7 - Bitrix24 CRM - Integration
 * Plugin URI: https://plugins.itgalaxy.company/product/contact-form-7-bitrix24-crm-integration-contact-form-7-bitrix24-crm-integracziya/
 * Description: Allows you to integrate your forms and Bitrix24 CRM
 * Version: 2.40.0
 * Author: itgalaxycompany
 * Author URI: https://itgalaxy.company
 * License: GPLv3
 * Text Domain: cf7-bitrix24-integration
 * Domain Path: /languages/
 */

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
update_site_option('cf7-bitrix-purchase-code', '***************');
if (!defined('ABSPATH')) {
    exit();
}

/**
 * Registration and load of translations.
 *
 * @see https://developer.wordpress.org/reference/functions/load_plugin_textdomain/
 */
\add_action('init', function () {
    \load_plugin_textdomain('cf7-bitrix24-integration', false, dirname(\plugin_basename(__FILE__)) . '/languages');
});

/**
 * Action Scheduler requires a special loading procedure.
 */
require plugin_dir_path(__FILE__) . 'vendor/woocommerce/action-scheduler/action-scheduler.php';

/**
 * Use composer autoloader.
 */
require plugin_dir_path(__FILE__) . 'vendor/autoload.php';

/**
 * Register plugin uninstall hook.
 *
 * @see https://developer.wordpress.org/reference/functions/register_uninstall_hook/
 */
\register_uninstall_hook(__FILE__, [Bootstrap::class, 'pluginUninstall']);

/**
 * Load plugin.
 */
Bootstrap::getInstance(__FILE__);
