<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;

class LogHelper
{
    private static $instance = false;

    protected function __construct()
    {
        if (!\current_user_can('wpcf7_manage_integration')) {
            return;
        }

        if (isset($_GET[Bootstrap::OPTIONS_KEY . '-logs-get'])) {
            add_action('admin_init', [$this, 'logsGet']);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function logsGet()
    {
        Bootstrap::$common->logger->logsGet();

        exit();
    }
}
