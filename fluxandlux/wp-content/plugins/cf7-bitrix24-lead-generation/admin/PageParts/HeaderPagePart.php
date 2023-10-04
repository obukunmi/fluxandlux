<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\PageParts;

use Itgalaxy\PluginCommon\AdminGenerator\Themes\OurStore;

class HeaderPagePart
{
    /**
     * @return void
     */
    public static function render()
    {
        OurStore::pageHeader(
            esc_html__('Integration settings', 'cf7-bitrix24-integration'),
            esc_html__(
                'Formation of leads in Bitrix24 from the hits that users leave on your site, using the Contact Form 7 plugin.',
                'cf7-bitrix24-integration'
            ),
            [
                [
                    'type' => 'button-link',
                    'classes' => ['btn-light'],
                    'text' => '<i class="icon-svg icon-svg-question"></i> ' . esc_html__('Documentation', 'cf7-bitrix24-integration'),
                    'attributes' => [
                        'title' => esc_html__('Documentation', 'cf7-bitrix24-integration'),
                        'href' => 'https://itgalaxy.company/software/wordpress-contact-form-7-bitrix24-crm-%d0%b8%d0%bd%d1%82%d0%b5%d0%b3%d1%80%d0%b0%d1%86%d0%b8%d1%8f/contact-form-7-bitrix24-crm-%d0%b8%d0%bd%d1%82%d0%b5%d0%b3%d1%80%d0%b0%d1%86%d0%b8%d1%8f-%d0%b8%d0%bd%d1%81%d1%82%d1%80%d1%83%d0%ba%d1%86%d0%b8%d0%b8/',
                        'target' => '_blank',
                    ],
                ],
                [
                    'type' => 'button-link',
                    'classes' => ['btn-light'],
                    'text' => '<i class="icon-svg icon-svg-support"></i> ' . esc_html__('Support', 'cf7-bitrix24-integration'),
                    'attributes' => [
                        'title' => esc_html__('Support', 'cf7-bitrix24-integration'),
                        'href' => 'https://plugins.itgalaxy.company/product/contact-form-7-bitrix24-crm-integration-contact-form-7-bitrix24-crm-integracziya/',
                        'target' => '_blank',
                    ],
                ],
            ]
        );

        self::showNotices();
    }

    /**
     * @return void
     */
    private static function showNotices()
    {
        if (isset($_GET['success'])) {
            OurStore::callout(
                esc_html__('Settings successfully updated.', 'cf7-bitrix24-integration'),
                'success'
            );
        } elseif (isset($_GET['error-required-fields'])) {
            OurStore::callout(
                esc_html__('ERROR', 'cf7-bitrix24-integration') . ': '
                . esc_html__('Web hook url is not valid.', 'cf7-bitrix24-integration'),
                'danger'
            );
        } elseif (isset($_GET['success-fields-reload'])) {
            OurStore::callout(
                esc_html__('Fields cache updated successfully.', 'cf7-bitrix24-integration'),
                'success'
            );
        }
    }
}
