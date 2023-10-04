<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Includes;

class Helper
{
    public static function log($message, $data = [], $type = 'info')
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);
        $enableLogging = isset($settings['enabled_logging']) && (int) $settings['enabled_logging'] === 1;

        if (!$enableLogging) {
            return;
        }

        try {
            Bootstrap::$common->logger->log('cf7bx24', $message, (array) $data, $type);
        } catch (\Exception $exception) {
            // Nothing
        }
    }

    public static function isVerify()
    {
        $value = get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY);

        if (!empty($value)) {
            return true;
        }

        return false;
    }

    public static function nonVerifyText()
    {
        return esc_html__(
            'Please verify the purchase code on the plugin settings page - ',
            'cf7-bitrix24-integration'
        )
            . '<a href="'
            . admin_url()
            . 'admin.php?page=' . Bootstrap::OPTIONS_KEY . '#itglx-license-verify">'
            . admin_url()
            . 'admin.php?page=' . Bootstrap::OPTIONS_KEY . '</a>';
    }

    /**
     * @return bool
     */
    public static function isActive()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        return !empty($settings['webhook']);
    }
}
