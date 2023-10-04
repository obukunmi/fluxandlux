<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;

class SupportDuplicateForm
{
    private static $instance = false;

    protected function __construct()
    {
        if (!\current_user_can('wpcf7_edit_contact_forms')) {
            return;
        }

        // prepare
        \add_filter('wpcf7_copy', [$this, 'copy'], 10, 2);

        // save value to new form
        \add_action('wpcf7_after_create', [$this, 'afterCreate']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @psalm-suppress UndefinedDocblockClass
     *
     * @param \WPCF7_ContactForm $new
     * @param \WPCF7_ContactForm $current
     *
     * @return \WPCF7_ContactForm
     */
    public function copy($new, $current)
    {
        $metaContent = \get_post_meta($current->id(), Bootstrap::META_KEY, true);

        if (empty($metaContent) || !is_array($metaContent)) {
            return $new;
        }

        /** @psalm-suppress UndefinedClass */
        $new->bitrix24Fields = $metaContent;

        return $new;
    }

    /**
     * @psalm-suppress UndefinedDocblockClass
     *
     * @param \WPCF7_ContactForm $current
     *
     * @return void
     */
    public function afterCreate($current)
    {
        if (!isset($current->bitrix24Fields)) {
            return;
        }

        \update_post_meta($current->id(), Bootstrap::META_KEY, $current->bitrix24Fields);
    }
}
