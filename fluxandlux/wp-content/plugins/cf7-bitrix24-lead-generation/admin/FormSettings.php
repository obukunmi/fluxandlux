<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin;

use Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings\CompanySettings;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings\ContactSettings;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings\DealSettings;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings\LeadSettings;
use Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings\TaskSettings;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Helper;

class FormSettings
{
    public function __construct()
    {
        if (!Helper::isActive()) {
            return;
        }

        add_filter('wpcf7_editor_panels', [$this, 'settingsPanels']);
        add_action('save_post_' . \WPCF7_ContactForm::post_type, [$this, 'saveSettings']);

        add_action('admin_enqueue_scripts', function () {
            if (!$this->isEditFormPage() && !$this->isNewFormPage()) {
                return;
            }

            wp_enqueue_style(
                'cf7-bitrix24-admin',
                Bootstrap::$common->assetsHelper->getPathAssetFile('/admin/css/app.css'),
                [],
                null
            );
            wp_enqueue_script(
                'cf7-bitrix24-admin',
                Bootstrap::$common->assetsHelper->getPathAssetFile('/admin/js/app.js'),
                [],
                null,
                true
            );
        });

        // add support files
        if (\current_user_can('wpcf7_edit_contact_forms')) {
            \add_action('wpcf7_post_edit_form_tag', function () {
                echo 'enctype="multipart/form-data"';
            });
        }

        if (
            \current_user_can('wpcf7_edit_contact_forms')
            && isset($_GET[Bootstrap::OPTIONS_KEY . '-export-form-settings'])
        ) {
            add_action('admin_init', [$this, 'exportFormSettings']);
        }
    }

    public function isNewFormPage()
    {
        return isset($_GET['page']) && $_GET['page'] === 'wpcf7-new';
    }

    public function isEditFormPage()
    {
        if (empty($_GET['post'])) {
            return false;
        }

        return \get_post_type($_GET['post']) === \WPCF7_ContactForm::post_type;
    }

    public function exportFormSettings()
    {
        if (empty($_GET[Bootstrap::OPTIONS_KEY . '-export-form-settings'])) {
            exit();
        }

        $formID = $_GET[Bootstrap::OPTIONS_KEY . '-export-form-settings'];

        if (\get_post_type($formID) !== 'wpcf7_contact_form') {
            exit();
        }

        $meta = \get_post_meta($formID, Bootstrap::META_KEY, true);

        if (empty($meta)) {
            $meta = [];
        }

        $meta = serialize(!empty($meta) ? $meta : []);

        header('Content-Type: plain/text');
        header(
            'Content-Disposition: attachment; filename="'
            . Bootstrap::PLUGIN_ID . '_' . Bootstrap::PLUGIN_VERSION
            . '_(' . $formID . ')_' . date('Y-m-d_H:i:s') . '.export' . '"'
        );
        header('Content-Length: ' . strlen($meta));

        echo $meta;

        exit();
    }

    public function settingsPanels($panels)
    {
        $panels['bitrix-panel'] = [
            'title' => esc_html__('Bitrix24', 'cf7-bitrix24-integration'),
            'callback' => [$this, 'panel'],
        ];

        return $panels;
    }

    public function panel(\WPCF7_ContactForm $post)
    {
        $meta = get_post_meta($post->id(), Bootstrap::META_KEY, true);

        $enabled = isset($meta['ENABLED']) ? $meta['ENABLED'] : false; ?>
        <?php if (!empty($post->id())) { ?>
            <a href="<?php echo esc_url(admin_url() . '?' . Bootstrap::OPTIONS_KEY . '-export-form-settings=' . $post->id()); ?>"
               class="button button-primary"
               target="_blank">
                <?php \esc_html_e('Export settings', 'cf7-bitrix24-integration'); ?>
            </a>
            |
        <?php } ?>
        <button type="submit" class="button" name="<?php echo \esc_attr(Bootstrap::OPTIONS_KEY . '-import-form-button'); ?>">
            <?php esc_attr_e('Import settings (from file)', 'cf7-bitrix24-integration'); ?>
        </button>
        <input type="file" name="<?php echo \esc_attr(Bootstrap::OPTIONS_KEY . '-import-form-settings'); ?>">
        <hr>
        <hr>
        <input type="hidden" name="cf7Bitrix[ENABLED]" value="0">
        <input
            type="checkbox"
            name="cf7Bitrix[ENABLED]" value="1"
            <?php checked($enabled, true); ?>
            title="<?php esc_attr_e('Enable send the lead', 'cf7-bitrix24-integration'); ?>">
        <strong><?php esc_html_e('Enable send the lead', 'cf7-bitrix24-integration'); ?></strong>
        <br><br>
        <?php esc_html_e('In the following fields, you can use these mail-tags:', 'contact-form-7'); ?>
        <div class="contact-form-editor-box-mail">
            <?php $post->suggest_mail_tags(); ?>
        </div>
        <br><br>
        <?php if (apply_filters('itglx_cf7_bx24_save_utm', true)) { ?>
            Utm-fields:<br>
            <span class="mailtag code">[utm_source]</span>
            <span class="mailtag code">[utm_medium]</span>
            <span class="mailtag code">[utm_campaign]</span>
            <span class="mailtag code">[utm_term]</span>
            <span class="mailtag code">[utm_content]</span>
            <span class="mailtag code">and etc.</span>
            <br><br>
        <?php } ?>
        Roistat-fields:<br>
        <span class="mailtag code">[roistat_visit]</span>
        <br><br>
        GA fields:<br>
        <span class="mailtag code">[gaClientID]</span>
        <br><br>
        Yandex fields:<br>
        <span class="mailtag code">[yandexClientID]</span>
        <hr>
        <p>
            <?php $currentType = isset($meta['TYPE']) ? $meta['TYPE'] : 'lead'; ?>
            <strong>
                <?php
                esc_html_e('Choose the type of lead that will be generated in CRM:', 'cf7-bitrix24-integration'); ?>
            </strong>
            <br>
            <label>
                <input type="radio"
                    value="lead"
                    name="cf7Bitrix[TYPE]"
                    title="<?php esc_attr_e('Lead', 'cf7-bitrix24-integration'); ?>"
                    <?php checked($currentType, 'lead'); ?>> <?php esc_html_e('Lead', 'cf7-bitrix24-integration'); ?>
                <br><small>
                    <?php
                    esc_html_e('Used tabs: Lead fields - main, Task fields - additional.', 'cf7-bitrix24-integration'); ?>
                </small>
            </label>
            <br>
            <label>
                <input type="radio"
                    value="deal"
                    name="cf7Bitrix[TYPE]"
                    title="<?php esc_html_e('Deal', 'cf7-bitrix24-integration'); ?>"
                    <?php checked($currentType, 'deal'); ?>>
                <?php esc_html_e('Deal (Auto create/connect existing contact and company. Search contact and company by phone and email.)', 'cf7-bitrix24-integration'); ?>
                <br>
                <small>
                    <?php esc_html_e('Used tabs: Deal fields - main, Contact fields, Company fields, Task fields - additional.', 'cf7-bitrix24-integration'); ?>
                </small>
            </label>
            <br>
            <label>
                <input type="radio"
                    value="task"
                    name="cf7Bitrix[TYPE]"
                    title="<?php esc_html_e('Task', 'cf7-bitrix24-integration'); ?>"
                    <?php checked($currentType, 'task'); ?>>
                <?php esc_html_e('Task (Auto create/connect existing contact and company. Search contact and company by phone and email.)', 'cf7-bitrix24-integration'); ?>
                <br>
                <small>
                    <?php esc_html_e('Used tabs: Task fields - main, Contact fields, Company fields - additional.', 'cf7-bitrix24-integration'); ?>
                </small>
            </label>
            <br>
            <label>
                <input type="radio"
                    value="contact"
                    name="cf7Bitrix[TYPE]"
                    title="<?php esc_html_e('Contact', 'cf7-bitrix24-integration'); ?>"
                    <?php checked($currentType, 'contact'); ?>> <?php esc_html_e('Contact', 'cf7-bitrix24-integration'); ?>
                <br><small><?php esc_html_e('Used tab: Contact fields.', 'cf7-bitrix24-integration'); ?></small>
            </label>
            <br>
            <label>
                <input type="radio"
                    value="company"
                    name="cf7Bitrix[TYPE]"
                    title="<?php esc_html_e('Company', 'cf7-bitrix24-integration'); ?>"
                    <?php checked($currentType, 'company'); ?>>
                <?php esc_html_e('Company', 'cf7-bitrix24-integration'); ?>
                <br><small><?php esc_html_e('Used tab: Company fields.', 'cf7-bitrix24-integration'); ?></small>
            </label>
        </p>
        <hr>
        <div id="cf7-bx-tabs">
            <ul>
                <li>
                    <a href="#lead-fields">
                        <?php esc_html_e('Lead fields', 'cf7-bitrix24-integration'); ?>
                    </a>
                </li>
                <li>
                    <a href="#deal-fields">
                        <?php esc_html_e('Deal fields', 'cf7-bitrix24-integration'); ?>
                    </a>
                </li>
                <li>
                    <a href="#task-fields">
                        <?php esc_html_e('Task fields', 'cf7-bitrix24-integration'); ?>
                    </a>
                </li>
                <li>
                    <a href="#contact-fields">
                        <?php esc_html_e('Contact fields', 'cf7-bitrix24-integration'); ?>
                    </a>
                </li>
                <li>
                    <a href="#company-fields">
                        <?php esc_html_e('Company fields', 'cf7-bitrix24-integration'); ?>
                    </a>
                </li>
                <li>
                    <a href="#livefeed-fields">
                        <?php esc_html_e('Live feed entry message', 'cf7-bitrix24-integration'); ?>
                    </a>
                </li>
            </ul>
            <div id="lead-fields">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th style="width: 150px; padding: 5px;">
                                <?php esc_html_e('Create task', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td style="padding: 5px;">
                                <?php $value = isset($meta['lead_create_task']) ? $meta['lead_create_task'] : ''; ?>
                                <input type="hidden" name="cf7Bitrix[lead_create_task]" value="false">
                                <input id="__lead_create_task"
                                    type="checkbox"
                                    title="<?php esc_html_e('Create task', 'cf7-bitrix24-integration'); ?>"
                                    <?php checked($value, 'true'); ?>
                                    name="cf7Bitrix[lead_create_task]"
                                    value="true">
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 400px; padding: 5px;">
                                <?php esc_html_e('Update an existing lead (search by phone)', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td style="padding: 5px;">
                                <?php $value = isset($meta['lead_update_exists']) ? $meta['lead_update_exists'] : ''; ?>
                                <input type="hidden" name="cf7Bitrix[lead_update_exists]" value="false">
                                <input id="__lead_update_exists"
                                    type="checkbox"
                                    title="<?php esc_html_e('Update an existing lead (search by phone)', 'cf7-bitrix24-integration'); ?>"
                                    <?php checked($value, 'true'); ?>
                                    name="cf7Bitrix[lead_update_exists]"
                                    value="true">
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 300px; padding: 5px;">
                                <?php esc_html_e('Update an existing lead (search by mail)', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td style="padding: 5px;">
                                <?php
                                $value = isset($meta['lead_update_exists_mail'])
                                    ? $meta['lead_update_exists_mail']
                                    : ''; ?>
                                <input type="hidden" name="cf7Bitrix[lead_update_exists_mail]" value="false">
                                <input id="__lead_update_exists"
                                    type="checkbox"
                                    title="<?php esc_html_e('Update an existing lead (search by mail)', 'cf7-bitrix24-integration'); ?>"
                                    <?php checked($value, 'true'); ?>
                                    name="cf7Bitrix[lead_update_exists_mail]"
                                    value="true">
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 300px; padding: 5px;">
                                <?php esc_html_e('Link to existing contact (search by mail/phone)', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td style="padding: 5px;">
                                <?php
                                $value = isset($meta['lead_connect_exists_contact'])
                                    ? $meta['lead_connect_exists_contact']
                                    : ''; ?>
                                <input type="hidden" name="cf7Bitrix[lead_connect_exists_contact]" value="false">
                                <input id="__lead_connect_exists_contact"
                                    type="checkbox"
                                    title="<?php esc_html_e('Link to existing contact (search by mail/phone)', 'cf7-bitrix24-integration'); ?>"
                                    <?php checked($value, 'true'); ?>
                                    name="cf7Bitrix[lead_connect_exists_contact]"
                                    value="true">
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 150px; padding: 5px;">
                                <?php esc_html_e('Send uploaded files', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td style="padding: 5px;">
                                <?php
                                $value = isset($meta['send_files_lead'])
                                    ? $meta['send_files_lead']
                                    : ''; ?>
                                <input type="hidden" name="cf7Bitrix[send_files_lead]" value="false">
                                <input id="__send_files_lead"
                                    type="checkbox"
                                    title="<?php esc_html_e('Send uploaded files', 'cf7-bitrix24-integration'); ?>"
                                    <?php checked($value, 'true'); ?>
                                    name="cf7Bitrix[send_files_lead]"
                                    value="true">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <?php LeadSettings::render($meta); ?>
            </div>
            <div id="deal-fields">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th style="width: 150px;">
                                <?php esc_html_e('Create task', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td>
                                <?php $value = isset($meta['deal_create_task']) ? $meta['deal_create_task'] : ''; ?>
                                <input type="hidden" name="cf7Bitrix[deal_create_task]" value="false">
                                <input id="__deal_create_task"
                                    type="checkbox"
                                    title="<?php esc_html_e('Create task', 'cf7-bitrix24-integration'); ?>"
                                    <?php checked($value, 'true'); ?>
                                    name="cf7Bitrix[deal_create_task]"
                                    value="true">
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 150px;">
                                <?php esc_html_e('Send uploaded files', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td>
                                <?php
                                $value = isset($meta['send_files_deal'])
                                    ? $meta['send_files_deal']
                                    : ''; ?>
                                <input type="hidden" name="cf7Bitrix[send_files_deal]" value="false">
                                <input id="__send_files_deal"
                                    type="checkbox"
                                    title="<?php esc_html_e('Send uploaded files', 'cf7-bitrix24-integration'); ?>"
                                    <?php checked($value, 'true'); ?>
                                    name="cf7Bitrix[send_files_deal]"
                                    value="true">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <?php DealSettings::render($meta); ?>
            </div>
            <div id="task-fields">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th style="width: 350px;">
                                <?php esc_html_e('Automatically assign a responsible if there is an updated lead or createdlead / deal.', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td>
                                <?php
                                $value = isset($meta['task_auto_set_responsible'])
                                    ? $meta['task_auto_set_responsible']
                                    : ''; ?>
                                <input type="hidden" name="cf7Bitrix[task_auto_set_responsible]" value="false">
                                <input id="__task_auto_set_responsible"
                                    type="checkbox"
                                    <?php checked($value, 'true'); ?>
                                    name="cf7Bitrix[task_auto_set_responsible]"
                                    value="true">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <?php TaskSettings::render($meta); ?>
                <hr>
                <table class="form-table">
                    <tr>
                        <th>
                            <?php esc_html_e('Create checklist', 'cf7-bitrix24-integration'); ?>
                        </th>
                        <td>
                            <textarea
                                id="__task_check_list"
                                class="large-text code"
                                title="<?php echo esc_attr_e('Checklist items', 'cf7-bitrix24-integration'); ?>"
                                placeholder="Element 1; Element 2; Element 3"
                                name="cf7Bitrix[task_check_list]"
                                rows="4"><?php echo esc_html(isset($meta['task_check_list']) ? $meta['task_check_list'] : ''); ?></textarea>
                            <small>
                                <?php esc_html_e('List of elements separated by a semicolon ";"', 'cf7-bitrix24-integration'); ?>
                            </small>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="contact-fields">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th style="width: 300px;">
                                <?php esc_html_e('Always create a new contact and create live feed entry message in all exists (search by phone and mail)', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td>
                                <?php
                                $value = isset($meta['always_create_new_contact'])
                                    ? $meta['always_create_new_contact']
                                    : ''; ?>
                                <input type="hidden" name="cf7Bitrix[always_create_new_contact]" value="false">
                                <label>
                                    <input type="checkbox"
                                        name="cf7Bitrix[always_create_new_contact]"
                                        value="true"
                                        <?php checked($value, 'true'); ?>>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 300px;">
                                <?php esc_html_e('Update an existing contact (search by phone and mail)', 'cf7-bitrix24-integration'); ?>
                            </th>
                            <td>
                                <?php
                                $value = isset($meta['contact_update_exists'])
                                    ? $meta['contact_update_exists']
                                    : ''; ?>
                                <input type="hidden" name="cf7Bitrix[contact_update_exists]" value="false">
                                <label>
                                    <input type="checkbox"
                                        name="cf7Bitrix[contact_update_exists]"
                                        value="true"
                                        <?php checked($value, 'true'); ?>>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <?php ContactSettings::render($meta); ?>
            </div>
            <div id="company-fields">
                <?php CompanySettings::render($meta); ?>
            </div>
            <div id="livefeed-fields">
                <table class="form-table">
                    <tr>
                        <th>
                            <?php esc_html_e('Message content', 'cf7-bitrix24-integration'); ?>
                        </th>
                        <td>
                            <textarea
                                id="__livefeedmessage"
                                class="large-text code"
                                title="<?php echo esc_attr_e('Message content', 'cf7-bitrix24-integration'); ?>"
                                name="cf7Bitrix[livefeedmessage]"
                                rows="4"><?php echo esc_html(isset($meta['livefeedmessage']) ? $meta['livefeedmessage'] : ''); ?></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }

    public function saveSettings($postID)
    {
        if (!\current_user_can('wpcf7_edit_contact_forms')) {
            return;
        }

        $importFileName = Bootstrap::OPTIONS_KEY . '-import-form-settings';

        // import settings from file
        if (
            isset($_POST[Bootstrap::OPTIONS_KEY . '-import-form-button'])
            && !empty($_FILES[$importFileName])
            && !empty($_FILES[$importFileName]['tmp_name'])
        ) {
            $settingsContent = file_get_contents($_FILES[$importFileName]['tmp_name']);

            /**
             * @see https://developer.wordpress.org/reference/functions/is_serialized/
             */
            if (\is_serialized($settingsContent) === true) {
                \update_post_meta($postID, Bootstrap::META_KEY, \maybe_unserialize($settingsContent));
            }
        } elseif (isset($_POST['cf7Bitrix'])) {
            update_post_meta($postID, Bootstrap::META_KEY, wp_unslash($_POST['cf7Bitrix']));
        }
    }
}
