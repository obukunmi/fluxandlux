<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\PageParts;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Helper;
use Itgalaxy\PluginCommon\AdminGenerator\Elements\Root;

class ReloadFieldsPagePart
{
    /**
     * @return void
     */
    public static function render()
    {
        if (!Helper::isActive()) {
            return;
        }

        echo ' <form action="'
            . esc_url(admin_url() . 'admin.php?page=' . Bootstrap::OPTIONS_KEY)
            . '" method="post">';

        Root::render(
            [
                'type' => 'div',
                'classes' => ['mb-3'],
                'childes' => [
                    [
                        'type' => 'button',
                        'classes' => ['btn-primary'],
                        'text' => esc_html__('Reload fields data from CRM', 'cf7-bitrix24-integration'),
                        'attributes' => [
                            'type' => 'submit',
                            'name' => 'cf7Bitrix24ReloadFieldsCache',
                        ],
                    ],
                ],
            ]
        );

        echo '</form>';
    }
}
