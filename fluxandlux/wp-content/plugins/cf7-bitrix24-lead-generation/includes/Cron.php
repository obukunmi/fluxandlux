<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Includes;

use Itgalaxy\PluginCommon\ActionSchedulerHelper;

class Cron
{
    private static $instance = false;

    private function __construct()
    {
        add_action('init', [$this, 'createCron']);
        \add_action(Bootstrap::SEND_CRON_TASK, [$this, 'sendCronAction']);

        // not bind if run not cron mode
        if (!defined('DOING_CRON') || !DOING_CRON) {
            return;
        }

        add_action(Bootstrap::CRON, [$this, 'cronAction']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function createCron()
    {
        if (!wp_next_scheduled(Bootstrap::CRON)) {
            wp_schedule_event(time(), 'weekly', Bootstrap::CRON);
        }
    }

    public function cronAction()
    {
        $response = Bootstrap::$common->requester->call('cron_code_check');

        if (is_wp_error($response)) {
            return;
        }

        if ($response->status === 'stop') {
            update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, '');
        }
    }

    public function sendCronAction($optionName)
    {
        Helper::log('[cron/form] process sent [start]');

        $data = ActionSchedulerHelper::getDataForSendFormAction($optionName);

        if (empty($data)) {
            Helper::log('[cron/form] no data [end]');

            return;
        }

        Bitrix24::send($data['fields'], $data['crmFields'], $data['formID'], $data['type'], $data['ignoreFields']);

        Helper::log('[cron/form] process sent [end]');
    }
}
