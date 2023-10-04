<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Includes;

use Itgalaxy\PluginCommon\ActionSchedulerHelper;
use Itgalaxy\PluginCommon\AnalyticsHelper;

class CF7
{
    private static $instance = false;

    private $submission;

    protected function __construct()
    {
        if (Helper::isActive()) {
            add_action('wpcf7_mail_sent', [$this, 'onFormSubmit'], 10, 1);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /** @psalm-suppress UndefinedClass */
    public function onFormSubmit(\WPCF7_ContactForm $contactForm)
    {
        Helper::log('[start] triggered on `wpcf7_mail_sent`');

        if (!class_exists('\\WPCF7_Submission')) {
            Helper::log('[end] not exists `WPCF7_Submission` - ' . $contactForm->id());

            return;
        }

        $this->submission = \WPCF7_Submission::get_instance();

        if (!$this->submission) {
            return;
        }

        $postedData = $this->submission->get_posted_data();

        if (!$postedData) {
            Helper::log('[end] empty posted data`');

            return;
        }

        /**
         * Filters ignore sending data.
         *
         * @since 2.37.0
         *
         * @param bool               $ignore      Default: false
         * @param array              $postedData
         * @param \WPCF7_ContactForm $contactForm
         */
        if (\apply_filters('itglx/cf7/bx24/ignore-sending', false, $postedData, $contactForm)) {
            Helper::log('[end] ignore sent through `itglx/cf7/bx24/ignore-sending`, form ID - ' . $contactForm->id());

            return;
        }

        // Compatible `Contact Form 7 Multi-Step Forms`
        if (function_exists('\\cf7msm_get')) {
            if (!empty($_POST['step'])) {
                $step = wp_unslash($_POST['step']);

                if (preg_match('/(\d+)-(\d+)/', $step, $matches)) {
                    $curr_step = $matches[1];
                    $last_step = $matches[2];

                    if ($curr_step != $last_step) {
                        Helper::log('[end] not last step in multistep');

                        return;
                    }
                }
            }

            $prevData = \cf7msm_get('cf7msm_posted_data', '');

            if (!is_array($prevData)) {
                $prevData = [];
            }

            $postedData = array_merge($prevData, $postedData);
        }

        $meta = get_post_meta($contactForm->id(), Bootstrap::META_KEY, true);

        // If empty form setting or not enabled send lead
        if (empty($meta) || !$meta['ENABLED']) {
            Helper::log('[end] not enabled for - ' . $contactForm->id());

            return;
        }

        $currentType = isset($meta['TYPE']) ? $meta['TYPE'] : 'lead';

        // If empty settings by type
        if (empty($meta[$currentType])) {
            Helper::log('[end] empty meta in form by type - ' . $contactForm->id() . ' - ' . $currentType);

            return;
        }

        $postedData = $this->prepareSpecialMailTags($postedData);

        if (apply_filters('itglx_cf7_bx24_save_utm', true)) {
            $postedData = $this->parseUtmCookie($postedData);
        }

        $postedData['roistat_visit'] = AnalyticsHelper::getCookieRoistatVisit();
        $postedData['gaClientID'] = AnalyticsHelper::getGetGaClientIdFromCookie();
        $postedData['yandexClientID'] = AnalyticsHelper::getCookieYandexClientId();

        $keys = array_map(function ($key) {
            return '[' . $key . ']';
        }, array_keys($postedData));
        $values = array_values($postedData);
        array_walk($values, function (&$value) {
            if (is_array($value)) {
                $value = implode('; ', $value);
            }
        });

        $data = [];
        $data['keys'] = $keys;
        $data['values'] = $values;

        $sendFields = [];
        $crmFields = [];

        if (isset($meta['task_auto_set_responsible']) && $meta['task_auto_set_responsible'] == 'true') {
            $sendFields['task_auto_set_responsible'] = true;
        }

        if (isset($meta['contact_update_exists']) && $meta['contact_update_exists'] == 'true') {
            $sendFields['contact_update_exists'] = true;
        }

        if (isset($meta['always_create_new_contact']) && $meta['always_create_new_contact'] == 'true') {
            $sendFields['always_create_new_contact'] = true;
        }

        if (!empty($meta['livefeedmessage'])) {
            $sendFields['livefeedmessage'] = $this->replaceTagsToValue($data, $meta['livefeedmessage']);
            $sendFields['livefeedmessage'] = str_replace(
                ['<br>', '<br/>', '<br />'],
                '\n',
                $sendFields['livefeedmessage']
            );

            $sendFields['livefeedmessage'] = \strip_tags($sendFields['livefeedmessage']);
        }

        if (!empty($meta['task_check_list'])) {
            $sendFields['task_check_list'] = $this->replaceTagsToValue($data, $meta['task_check_list']);
            $sendFields['task_check_list'] = explode(';', $sendFields['task_check_list']);
        }

        $data['files_for_contact_photo'] = $this->submission->uploaded_files();

        switch ($currentType) {
            case 'lead':
                if (isset($meta['send_files_lead']) && $meta['send_files_lead'] === 'true') {
                    $sendFields['uploads'] = $this->prepareUploads($this->submission->uploaded_files());
                }

                $crmFields['lead'] = (array) get_option(Bootstrap::LEAD_FIELDS_KEY);
                $sendFields['lead'] = $this->prepareSendFields($crmFields['lead'], $meta[$currentType], $data);

                if (isset($meta['lead_create_task']) && $meta['lead_create_task'] == 'true') {
                    $crmFields['task'] = (array) get_option(Bootstrap::TASK_FIELDS_KEY);
                    $sendFields['task'] = $this->prepareSendFields($crmFields['task'], $meta['task'], $data);
                }

                if (isset($meta['lead_update_exists']) && $meta['lead_update_exists'] == 'true') {
                    $sendFields['lead_update_exists'] = true;
                }

                if (isset($meta['lead_update_exists_mail']) && $meta['lead_update_exists_mail'] == 'true') {
                    $sendFields['lead_update_exists_mail'] = true;
                }

                if (isset($meta['lead_connect_exists_contact']) && $meta['lead_connect_exists_contact'] == 'true') {
                    $sendFields['lead_connect_exists_contact'] = true;
                }

                break;
            case 'deal':
                if (isset($meta['send_files_deal']) && $meta['send_files_deal'] === 'true') {
                    $sendFields['uploads'] = $this->prepareUploads($this->submission->uploaded_files());
                }

                $crmFields['deal'] = (array) get_option(Bootstrap::DEAL_FIELDS_KEY);
                $crmFields['contact'] = (array) get_option(Bootstrap::CONTACT_FIELDS_KEY);
                $crmFields['company'] = (array) get_option(Bootstrap::COMPANY_FIELDS_KEY);
                $sendFields['deal'] = $this->prepareSendFields($crmFields['deal'], $meta[$currentType], $data);
                $sendFields['contact'] = $this->prepareSendFields($crmFields['contact'], $meta['contact'], $data);
                $sendFields['company'] = $this->prepareSendFields($crmFields['company'], $meta['company'], $data);

                if (isset($meta['deal_create_task']) && $meta['deal_create_task'] == 'true') {
                    $crmFields['task'] = (array) get_option(Bootstrap::TASK_FIELDS_KEY);
                    $sendFields['task'] = $this->prepareSendFields($crmFields['task'], $meta['task'], $data);
                }
                break;
            case 'task':
                $crmFields['task'] = (array) get_option(Bootstrap::TASK_FIELDS_KEY);
                $crmFields['contact'] = (array) get_option(Bootstrap::CONTACT_FIELDS_KEY);
                $crmFields['company'] = (array) get_option(Bootstrap::COMPANY_FIELDS_KEY);
                $sendFields['task'] = $this->prepareSendFields($crmFields['task'], $meta[$currentType], $data);
                $sendFields['contact'] = $this->prepareSendFields($crmFields['contact'], $meta['contact'], $data);
                $sendFields['company'] = $this->prepareSendFields($crmFields['company'], $meta['company'], $data);
                break;
            case 'contact':
                $crmFields['contact'] = (array) get_option(Bootstrap::CONTACT_FIELDS_KEY);
                $sendFields['contact'] = $this->prepareSendFields($crmFields['contact'], $meta[$currentType], $data);

                break;
            case 'company':
                $crmFields['company'] = (array) get_option(Bootstrap::COMPANY_FIELDS_KEY);
                $sendFields['company'] = $this->prepareSendFields($crmFields['company'], $meta[$currentType], $data);
                break;
            default:
                // Nothing
                break;
        }

        // Not use API if empty data
        if (empty($sendFields[$currentType])) {
            Helper::log('send empty form event by type- ' . $contactForm->id() . ' - ' . $currentType, []);
            Helper::log('[end] send form event data - ' . $contactForm->id() . ' - ' . $currentType, $sendFields);

            return;
        }

        // resolve ignore fields
        $currentFieldsIgnore = isset($meta['ignore_fields']) ? $meta['ignore_fields'] : [];

        if (!empty($currentFieldsIgnore) && !empty($currentFieldsIgnore[$currentType])) {
            $currentFieldsIgnore = explode(',', $currentFieldsIgnore[$currentType]);
            $currentFieldsIgnore = array_map('trim', $currentFieldsIgnore);
        } else {
            $currentFieldsIgnore = [];
        }

        Helper::log('send form event - ' . $contactForm->id() . ' - ' . $currentType, $sendFields);

        $settings = \get_option(Bootstrap::OPTIONS_KEY, []);

        if (!empty($settings['send_type']) && $settings['send_type'] === 'wp_cron') {
            Helper::log('register delayed send form event - ' . $contactForm->id() . ' - ' . $currentType);

            // register one time task
            ActionSchedulerHelper::registerSendFormAction(
                '_itglx_cf7_bx24_send_',
                Bootstrap::SEND_CRON_TASK,
                [
                    'fields' => $sendFields,
                    'crmFields' => $crmFields,
                    'formID' => $contactForm->id(),
                    'type' => $currentType,
                    'ignoreFields' => $currentFieldsIgnore,
                ]
            );
        } else {
            Bitrix24::send($sendFields, $crmFields, $contactForm->id(), $currentType, $currentFieldsIgnore);
        }

        Helper::log('[end] triggered on `wpcf7_mail_sent`');
    }

    public function prepareSendFields($fields, $meta, $postedData)
    {
        $prepareFields = [];

        foreach ($fields as $key => $field) {
            $populateValue = isset($meta[$key . '-populate']) ? $meta[$key . '-populate'] : '';
            $value = isset($meta[$key]) ? $meta[$key] : '';

            if (($field['type'] === 'char' || $field['type'] === 'boolean') && $value === 'N') {
                $value = false;
            }

            if ($populateValue) {
                $populateValue = $this->replaceTagsToValue($postedData, $populateValue);
            }

            if ($populateValue) {
                $prepareFields[$key] = $populateValue;
            } elseif ($value) {
                $prepareFields[$key] = $this->replaceTagsToValue($postedData, $value);
            }

            if (isset($prepareFields[$key])) {
                // Prepare and populate value to list field
                if (
                    $field['type'] === 'enumeration'
                    && !empty($field['items'])
                    && !empty($prepareFields[$key])
                ) {
                    $explodedField = explode('; ', $prepareFields[$key]);
                    $resolveValues = [];

                    $ids = \array_column($field['items'], 'ID');
                    $values = \array_column($field['items'], 'VALUE');

                    foreach ($explodedField as $explodeValue) {
                        if (array_search($explodeValue, $ids) !== false) {
                            $resolveValues[] = $explodeValue;
                        } elseif (array_search($explodeValue, $values) !== false) {
                            $resolveValues[] = $ids[array_search($explodeValue, $values)];
                        }
                    }

                    $prepareFields[$key] = $field['isMultiple']
                        ? $resolveValues
                        : current($resolveValues);
                } elseif ($field['type'] === 'char' || $field['type'] === 'boolean') {
                    $prepareFields[$key] = $prepareFields[$key] === 'Y' ? 'true' : $prepareFields[$key];

                    if (filter_var($prepareFields[$key], FILTER_VALIDATE_BOOLEAN)) {
                        $prepareFields[$key] = $field['type'] === 'char' ? 'Y' : 1;
                    } else {
                        $prepareFields[$key] = $field['type'] === 'char' ? 'N' : 0;
                    }
                }

                if ($key === 'HONORIFIC' && $populateValue) {
                    $prepareFields[$key] = $this->resolveStatusListValue('HONORIFIC', $prepareFields[$key]);
                }

                if (in_array($field['type'], ['string', 'url'], true) && $field['isMultiple']) {
                    $prepareFields[$key] = [$prepareFields[$key]];
                }

                if (!empty($prepareFields[$key]) && in_array($key, ['ACCOMPLICES', 'AUDITORS'], true)) {
                    $prepareFields[$key] = explode(',', $prepareFields[$key]);
                }

                // resolve file field
                if ($field['type'] === 'file' && $prepareFields[$key] && !empty($postedData['files_for_contact_photo'])) {
                    $resolvedFile = false;
                    $fileList = [];

                    foreach ($postedData['files_for_contact_photo'] as $file) {
                        if (is_array($file)) {
                            foreach ($file as $subFile) {
                                $fileList[] = $subFile;
                            }
                        } else {
                            $fileList[] = $file;
                        }
                    }

                    $fileList = array_unique($fileList);

                    foreach ($fileList as $file) {
                        if (strpos($file, $prepareFields[$key]) !== false) {
                            $resolvedFile = [
                                'fileData' => [
                                    basename($file),
                                    base64_encode(file_get_contents($file)),
                                ],
                            ];
                        }
                    }

                    if ($resolvedFile) {
                        $prepareFields[$key] = $resolvedFile;
                    }
                }

                if ($key === 'DEADLINE') {
                    $prepareFields[$key] = date_i18n('c', strtotime($prepareFields[$key]));
                }
            }
        }

        return $prepareFields;
    }

    public function replaceTagsToValue($postedData, $value)
    {
        $value = trim(
            str_replace($postedData['keys'], $postedData['values'], $value)
        );

        if (function_exists('wpcf7_mail_replace_tags')) {
            $value = \wpcf7_mail_replace_tags($value);
        }

        return $value;
    }

    public function prepareSpecialMailTags($postedData)
    {
        $specialMailTags = [
            '_date',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content',
            'utm_term',
            'geoip_detect2_user_info', // Support GeoIP Detection plugin
            'tracking-info',  // Support Contact Form 7 Lead info with country plugin
        ];

        foreach ($specialMailTags as $smt) {
            if (isset($postedData[$smt])) {
                continue;
            }

            // Support Contact Form 7 Lead info with country plugin
            if ($smt === 'tracking-info') {
                if (function_exists('\\wpshore_wpcf7_before_send_mail')) {
                    $result = \wpshore_wpcf7_before_send_mail(['body' => '[tracking-info]'], 1, new \stdClass());

                    $postedData[$smt] = $result['body'];
                }

                // another method since version 1.5.0
                if (function_exists('\\apa_wpcf7_before_send_mail')) {
                    $result = \apa_wpcf7_before_send_mail(['body' => '[tracking-info]'], 1, new \stdClass());

                    $postedData[$smt] = $result['body'];
                }
            } elseif ($smt === '_date') {
                $postedData[$smt] = date_i18n('Y-m-d');
            } else {
                /** @psalm-suppress UndefinedClass */
                $mailTag = new \WPCF7_MailTag(sprintf('[%s]', $smt), $smt, '');
                $postedData[$smt] = \apply_filters('wpcf7_special_mail_tags', '', $smt, false, $mailTag);
            }
        }

        return $postedData;
    }

    public function parseUtmCookie($postedData)
    {
        $basedUtm = [
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_term',
            'utm_content',
        ];

        foreach ($basedUtm as $utm) {
            if (!isset($postedData[$utm])) {
                $postedData[$utm] = '';
            }
        }

        if (!empty($_COOKIE[Bootstrap::UTM_COOKIES])) {
            $utmParams = json_decode(wp_unslash($_COOKIE[Bootstrap::UTM_COOKIES]), true);

            foreach ($utmParams as $key => $value) {
                $postedData[$key] = rawurldecode(wp_unslash($value));
            }
        }

        return $postedData;
    }

    public function prepareUploads($files, $single = false)
    {
        $uploadsDir = \wp_upload_dir();
        $uploadedFiles = [];

        if (!file_exists($uploadsDir['basedir'] . '/cf7-bitrix24-integration')) {
            mkdir($uploadsDir['basedir'] . '/cf7-bitrix24-integration', 0777);
        }

        $fileList = [];

        foreach ($files as $file) {
            if (is_array($file)) {
                foreach ($file as $subFile) {
                    $fileList[] = $subFile;
                }
            } else {
                $fileList[] = $file;
            }
        }

        // compatibility with https://ru.wordpress.org/plugins/drag-and-drop-multiple-file-upload-contact-form-7/
        if (function_exists('dnd_get_upload_dir')) {
            // Scan and get all form tags from cf7 generator
            $formsTags = $this->submission->get_contact_form();
            $dndUploadsDir = dnd_get_upload_dir();
            $originalPostedData = $this->submission->get_posted_data();

            if ($forms = $formsTags->scan_form_tags()) {
                foreach ($forms as $field) {
                    if ($field->basetype !== 'mfile') {
                        continue;
                    }

                    if (empty($originalPostedData[$field->name])) {
                        continue;
                    }

                    foreach ($originalPostedData[$field->name] as $file) {
                        if (is_array($dndUploadsDir)) {
                            $fileList[] = str_replace($dndUploadsDir['upload_url'], $dndUploadsDir['upload_dir'], $file);
                        } else {
                            $fileList[] = $file;
                        }
                    }
                }
            }
        }

        $fileList = array_unique($fileList);

        foreach ($fileList as $file) {
            if (!file_exists($file)) {
                continue;
            }

            $filePathInfo = pathinfo($file);
            $newFileName = uniqid();

            $newFilePath = $uploadsDir['basedir']
                . '/cf7-bitrix24-integration/'
                . $newFileName;

            copy($file, $newFilePath);

            if ($single) {
                return [
                    'name' => $filePathInfo['basename'],
                    'path' => $newFilePath,
                ];
            }

            $uploadedFiles[] = [
                'name' => $filePathInfo['basename'],
                'path' => $newFilePath,
            ];
        }

        return $uploadedFiles;
    }

    private function resolveStatusListValue($type, $value)
    {
        $statusList = get_option(Bootstrap::STATUS_LIST_KEY);
        $checkList = [];

        foreach ($statusList as $status) {
            if ($status['ENTITY_ID'] === $type) {
                $checkList[$status['STATUS_ID']] = $status['NAME'];
            }
        }

        $ids = array_keys($checkList);
        $values = array_values($checkList);

        if (array_search($value, $ids) !== false) {
            return $value;
        }
        if (array_search($value, $values) !== false) {
            return $ids[array_search($value, $values)];
        }

        return '';
    }
}
