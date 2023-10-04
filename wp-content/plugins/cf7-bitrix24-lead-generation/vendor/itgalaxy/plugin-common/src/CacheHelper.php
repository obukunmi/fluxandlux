<?php

namespace Itgalaxy\PluginCommon;

class CacheHelper
{
    /**
     * The method allows you to check if caching is applied on the site.
     *
     * @return bool
     */
    public static function siteUsesCache()
    {
        /** @psalm-suppress RedundantCondition */
        if (!defined('WP_CACHE') || !WP_CACHE) {
            return false;
        }

        if (!function_exists('is_plugin_active')) {
            /**
             * Require for `is_plugin_active` function.
             *
             * @psalm-suppress MissingFile
             */
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (
            !\is_plugin_active('wp-fastest-cache/wpFastestCache.php')
            && !\is_plugin_active('sg-cachepress/sg-cachepress.php')
        ) {
            return false;
        }

        return true;
    }
}
