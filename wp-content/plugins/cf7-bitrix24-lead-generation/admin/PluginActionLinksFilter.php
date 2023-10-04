<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;

class PluginActionLinksFilter
{
    private static $instance = false;

    protected function __construct()
    {
        add_filter('plugin_action_links', [$this, 'pluginActionLinks'], 10, 2);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function pluginActionLinks($actions, $pluginFile)
    {
        if (strpos($pluginFile, 'cf7-bitrix24-lead-generation.php') === false) {
            return $actions;
        }

        $settingsLink = '<a href="'
            . admin_url()
            . 'admin.php?page=' . Bootstrap::OPTIONS_KEY . '">'
            . esc_html__('Settings', 'cf7-bitrix24-integration')
            . '</a>';

        $documentationLink = '<a href="'
            . esc_url(
                __(
                    'https://itgalaxy.company/software/wordpress-contact-form-7-bitrix24-crm-%d0%b8%d0%bd%d1%82%d0%b5%d0%b3%d1%80%d0%b0%d1%86%d0%b8%d1%8f/contact-form-7-bitrix24-crm-%d0%b8%d0%bd%d1%82%d0%b5%d0%b3%d1%80%d0%b0%d1%86%d0%b8%d1%8f-%d0%b8%d0%bd%d1%81%d1%82%d1%80%d1%83%d0%ba%d1%86%d0%b8%d0%b8/',
                    'cf7-bitrix24-integration'
                )
            )
            . '" target="_blank">'
            . esc_html__('Documentation', 'cf7-bitrix24-integration')
            . '</a>';

        array_push(
            $actions,
            $settingsLink,
            $documentationLink
        );

        return $actions;
    }
}
