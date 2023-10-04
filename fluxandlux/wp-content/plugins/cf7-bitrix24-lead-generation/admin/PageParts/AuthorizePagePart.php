<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\PageParts;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Helper;
use Itgalaxy\PluginCommon\ActionSchedulerHelper;
use Itgalaxy\PluginCommon\AdminGenerator\Themes\OurStore;

class AuthorizePagePart
{
    /**
     * @return void
     */
    public static function render()
    {
        echo ' <form action="'
            . esc_url(admin_url() . 'admin.php?page=' . Bootstrap::OPTIONS_KEY)
            . '" method="post">';

        $trList = [];

        foreach (self::getFieldList() as $field) {
            $trList[] = [
                'type' => 'tr',
                'childes' => [
                    [
                        'type' => 'th',
                        'childes' => $field['th'],
                    ],
                    [
                        'type' => 'td',
                        'childes' => $field['td'],
                    ],
                ],
            ];
        }

        OurStore::section(
            [
                'header' => [
                    'title' => esc_html__('Authorization', 'cf7-bitrix24-integration'),
                ],
                'childes' => [
                    [
                        'type' => 'table',
                        'childes' => $trList,
                    ],
                    [
                        'type' => 'button',
                        'classes' => ['btn-primary'],
                        'text' => esc_html__('Save Settings', 'cf7-bitrix24-integration'),
                        'attributes' => [
                            'type' => 'submit',
                            'name' => 'cf7Bitrix24Submit',
                        ],
                    ],
                ],
            ]
        );

        echo '</form>';
    }

    /**
     * @return array
     */
    private static function getFieldList()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        return [
            [
                'th' => [
                    [
                        'type' => 'content',
                        'content' => esc_html__('Inbound web hook', 'cf7-bitrix24-integration'),
                    ],
                ],
                'td' => [
                    [
                        'type' => 'input',
                        'input_attributes' => [
                            'value' => isset($settings['webhook']) ? esc_attr($settings['webhook']) : '',
                            'name' => 'webhook',
                            'id' => 'webhook',
                            'placeholder' => 'https://your.bitrix24.ru/rest/*/**********/',
                            'aria-required' => 'true',
                        ],
                        'description' => [
                            'text' => esc_html__('The following permissions are required: CRM, Tasks, Tasks extended, Chat and Notifications.', 'cf7-bitrix24-integration'),
                        ],
                    ],
                ],
            ],
            [
                'th' => [
                    [
                        'type' => 'content',
                        'content' => esc_html__('Send type', 'cf7-bitrix24-integration'),
                    ],
                ],
                'td' => [
                    [
                        'type' => 'select',
                        'select_attributes' => [
                            'name' => 'send_type',
                            'id' => 'send_type',
                            'aria-required' => 'true',
                        ],
                        'options' => [
                            [
                                'attributes' => [
                                    'value' => 'immediately',
                                    'selected' => empty($settings['send_type']) || $settings['send_type'] === 'immediately',
                                ],
                                'label' => esc_html__('Immediately upon submitting the form', 'cf7-bitrix24-integration'),
                            ],
                            [
                                'attributes' => [
                                    'value' => 'wp_cron',
                                    'selected' => isset($settings['send_type']) && $settings['send_type'] === 'wp_cron',
                                ],
                                'label' => esc_html__('Action Scheduler', 'cf7-bitrix24-integration'),
                            ],
                        ],
                        'description' => [
                            'text' => Helper::isActive() && !empty($settings['send_type']) && $settings['send_type'] == 'wp_cron'
                                ? esc_html__('The number of registered form submit events pending', 'cf7-bitrix24-integration')
                                . ': <strong>' . ActionSchedulerHelper::getCountPendingActions(Bootstrap::SEND_CRON_TASK) . '</strong>'
                                : '',
                        ],
                    ],
                ],
            ],
        ];
    }
}
