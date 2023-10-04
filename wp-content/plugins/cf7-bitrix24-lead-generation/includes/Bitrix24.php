<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Includes;

class Bitrix24
{
    public static $scope = [
        'crm',
        'task',
        'tasks_extended',
    ];

    public static function send($sendFields, $crmFields, $formID, $currentType = 'lead', $currentFieldsIgnore = [])
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);
        $startLink = explode('rest', $settings['webhook']);

        $preparedFields = self::prepareFields($crmFields[$currentType], $sendFields[$currentType]);

        if (empty($preparedFields)) {
            return [];
        }

        $result = [];

        switch ($currentType) {
            case 'lead':
                $existLead = '';
                $leadID = '';
                $preparedFields = apply_filters('itglx_cf7_bx24_lead_fields_before_send', $preparedFields);

                if (isset($sendFields['lead_update_exists'])) {
                    $leadID = self::findItemByField($sendFields, 'lead', 'PHONE');
                }

                if (!$leadID && isset($sendFields['lead_update_exists_mail'])) {
                    $leadID = self::findItemByField($sendFields, 'lead', 'EMAIL');
                }

                if ($leadID) {
                    $existLead = self::sendApiRequest(
                        'crm.lead.get',
                        [
                            'id' => $leadID,
                        ]
                    );
                }

                if (isset($sendFields['lead_connect_exists_contact'])) {
                    $contactID = self::findItemByField(
                        [
                            'contact' => [
                                'PHONE' => isset($sendFields['lead']['PHONE']) ? $sendFields['lead']['PHONE'] : '',
                            ],
                        ],
                        'contact',
                        'PHONE'
                    );

                    if (!$contactID) {
                        $contactID = self::findItemByField(
                            [
                                'contact' => [
                                    'EMAIL' => isset($sendFields['lead']['EMAIL']) ? $sendFields['lead']['EMAIL'] : '',
                                ],
                            ],
                            'contact',
                            'EMAIL'
                        );
                    }

                    if ($contactID) {
                        $preparedFields['CONTACT_ID'] = $contactID;
                    }
                }

                if ($existLead) {
                    $preparedFields = self::ignoreFields($preparedFields, $currentFieldsIgnore);

                    // fix duplicate phone
                    if (!empty($existLead['PHONE'])
                        && !empty($preparedFields['PHONE'])
                        && self::existEmailPhone($existLead['PHONE'], $preparedFields['PHONE'][0]['VALUE'])
                    ) {
                        unset($preparedFields['PHONE']);
                    }

                    // fix duplicate email
                    if (!empty($existLead['EMAIL'])
                        && !empty($preparedFields['EMAIL'])
                        && self::existEmailPhone($existLead['EMAIL'], $preparedFields['EMAIL'][0]['VALUE'])
                    ) {
                        unset($preparedFields['EMAIL']);
                    }

                    $result = self::sendApiRequest(
                        'crm.lead.update',
                        [
                            'id' => $existLead['ID'],
                            'fields' => self::mergeFields($existLead, $preparedFields, $crmFields['lead']),
                        ]
                    );

                    if (!empty($sendFields['uploads'])) {
                        self::sendUploadFiles($sendFields['uploads'], $existLead['ID'], 1);
                    }

                    if (!empty($sendFields['task'])) {
                        if (
                            isset($sendFields['task_auto_set_responsible'])
                            && !empty($existLead['ASSIGNED_BY_ID'])
                        ) {
                            $sendFields['task']['RESPONSIBLE_ID'] = $existLead['ASSIGNED_BY_ID'];
                        }

                        $preparedFieldsTask = self::prepareFields($crmFields['task'], $sendFields['task']);
                        $preparedFieldsTask['UF_CRM_TASK'] = ['L_' . $existLead['ID']];

                        $result = self::sendApiRequest('tasks.task.add', ['fields' => $preparedFieldsTask]);

                        self::createTaskCheckList($result, $sendFields);
                    }

                    self::sendLiveMessage($existLead['ID'], 1, $sendFields);
                } else {
                    if (!empty($preparedFields['ASSIGNED_BY_ID'])) {
                        $preparedFields['ASSIGNED_BY_ID'] = self::resolveNextResponsible(
                            $preparedFields['ASSIGNED_BY_ID'],
                            'lead',
                            $formID
                        );
                    }

                    if (!Helper::isVerify()) {
                        if (!empty($preparedFields['COMMENTS'])) {
                            $preparedFields['COMMENTS'] = Helper::nonVerifyText()
                                    . '<br>'
                                    . $preparedFields['COMMENTS'];
                        } else {
                            $preparedFields['COMMENTS'] = Helper::nonVerifyText();
                        }
                    }

                    $result = self::sendApiRequest('crm.lead.add', ['fields' => $preparedFields]);

                    if (isset($result[0]) && is_numeric($result[0]) && $startLink[0]) {
                        self::sendLiveMessage($result[0], 1, $sendFields);

                        if (!empty($sendFields['uploads'])) {
                            self::sendUploadFiles($sendFields['uploads'], $result[0], 1);
                        }

                        $userNotify = esc_html__('New lead', 'cf7-bitrix24-integration')
                            . ' [b]#'
                            . $result[0]
                            . '[/b] [url='
                            . $startLink[0] . 'crm/lead/show/' . $result[0] . '/'
                            . ']'
                            . $preparedFields['TITLE']
                            . '[/url] '
                            . esc_html__('from the site', 'cf7-bitrix24-integration')
                            . ' '
                            . get_home_url();

                        if (!empty($sendFields['task'])) {
                            if (
                                isset($sendFields['task_auto_set_responsible'])
                                && !empty($preparedFields['ASSIGNED_BY_ID'])
                            ) {
                                $sendFields['task']['RESPONSIBLE_ID'] = $preparedFields['ASSIGNED_BY_ID'];
                            }

                            $preparedFieldsTask = self::prepareFields($crmFields['task'], $sendFields['task']);

                            $preparedFieldsTask['UF_CRM_TASK'] = ['L_' . $result[0]];

                            $result = self::sendApiRequest('tasks.task.add', ['fields' => $preparedFieldsTask]);

                            self::createTaskCheckList($result, $sendFields);
                        }

                        self::sendApiRequest(
                            'im.notify',
                            [
                                'to' => !empty($preparedFields['ASSIGNED_BY_ID']) ? $preparedFields['ASSIGNED_BY_ID'] : 1,
                                'message' => $userNotify,
                                'type' => 'SYSTEM',
                            ]
                        );
                    }
                }

                break;
            case 'deal':
                // Find or create company
                if (!empty($sendFields['company'])) {
                    $companyID = self::findItemByField($sendFields, 'company', 'PHONE');

                    if (!$companyID) {
                        $companyID = self::findItemByField($sendFields, 'company', 'EMAIL');
                    }

                    if (!$companyID) {
                        $preparedFieldsCompany = self::prepareFields($crmFields['company'], $sendFields['company']);

                        if ($preparedFieldsCompany && !empty($preparedFieldsCompany['TITLE'])) {
                            $result = self::sendApiRequest('crm.company.add', ['fields' => $preparedFieldsCompany]);

                            if ($result) {
                                $companyID = $result[0];
                            }
                        }
                    }

                    // Set company for deal
                    if ($companyID) {
                        $preparedFields['COMPANY_ID'] = $companyID;
                    }
                }

                // Find or create contact
                if (!empty($sendFields['contact'])) {
                    $preparedFieldsContact = self::prepareFields($crmFields['contact'], $sendFields['contact']);

                    if ($preparedFieldsContact) {
                        if (isset($companyID) && $companyID) {
                            $preparedFieldsContact['COMPANY_ID'] = $companyID;
                        }

                        $contactID = self::contactProcessing($sendFields, $preparedFieldsContact, $crmFields);

                        // Set contact for deal
                        if ($contactID) {
                            $preparedFields['CONTACT_ID'] = $contactID;
                        }
                    }
                }

                // Pipeline support
                $isPipelineSatus = explode(':', $preparedFields['STAGE_ID']);

                if (count($isPipelineSatus) === 2) {
                    $preparedFields['CATEGORY_ID'] = str_replace('C', '', $isPipelineSatus[0]);
                }
                // Pipeline support

                if (!empty($preparedFields['ASSIGNED_BY_ID'])) {
                    $preparedFields['ASSIGNED_BY_ID'] = self::resolveNextResponsible(
                        $preparedFields['ASSIGNED_BY_ID'],
                        'deal',
                        $formID
                    );
                }

                if (!Helper::isVerify()) {
                    if (!empty($preparedFields['COMMENTS'])) {
                        $preparedFields['COMMENTS'] = Helper::nonVerifyText()
                            . '<br>'
                            . $preparedFields['COMMENTS'];
                    } else {
                        $preparedFields['COMMENTS'] = Helper::nonVerifyText();
                    }
                }

                $result = self::sendApiRequest('crm.deal.add', ['fields' => $preparedFields]);

                if (isset($result[0]) && is_numeric($result[0]) && $startLink[0]) {
                    self::sendLiveMessage($result[0], 2, $sendFields);

                    if (!empty($sendFields['uploads'])) {
                        self::sendUploadFiles($sendFields['uploads'], $result[0], 2);
                    }

                    $userNotify = esc_html__('New deal', 'cf7-bitrix24-integration')
                        . ' [b]#'
                        . $result[0]
                        . '[/b] [url='
                        . $startLink[0] . 'crm/deal/show/' . $result[0] . '/'
                        . ']'
                        . $preparedFields['TITLE']
                        . '[/url] '
                        . esc_html__('from the site', 'cf7-bitrix24-integration')
                        . ' '
                        . get_home_url();

                    if (!empty($sendFields['task'])) {
                        if (
                            isset($sendFields['task_auto_set_responsible'])
                            && !empty($preparedFields['ASSIGNED_BY_ID'])
                        ) {
                            $sendFields['task']['RESPONSIBLE_ID'] = $preparedFields['ASSIGNED_BY_ID'];
                        }

                        $preparedFieldsTask = self::prepareFields($crmFields['task'], $sendFields['task']);
                        $preparedFieldsTask['UF_CRM_TASK'] = ['D_' . $result[0]];

                        $result = self::sendApiRequest('tasks.task.add', ['fields' => $preparedFieldsTask]);

                        self::createTaskCheckList($result, $sendFields);
                    }

                    self::sendApiRequest(
                        'im.notify',
                        [
                            'to' => !empty($preparedFields['ASSIGNED_BY_ID']) ? $preparedFields['ASSIGNED_BY_ID'] : 1,
                            'message' => $userNotify,
                            'type' => 'SYSTEM',
                        ]
                    );
                }
                break;
            case 'task':
                $preparedFields['UF_CRM_TASK'] = [];

                // Find or create company
                if (!empty($sendFields['company'])) {
                    $companyID = self::findItemByField($sendFields, 'company', 'PHONE');

                    if (!$companyID) {
                        $companyID = self::findItemByField($sendFields, 'company', 'EMAIL');
                    }

                    if (!$companyID) {
                        $preparedFieldsCompany = self::prepareFields($crmFields['company'], $sendFields['company']);

                        if ($preparedFieldsCompany && !empty($preparedFieldsCompany['TITLE'])) {
                            $result = self::sendApiRequest('crm.company.add', ['fields' => $preparedFieldsCompany]);

                            if ($result) {
                                $companyID = $result[0];
                            }
                        }
                    }

                    // Set company for task
                    if ($companyID) {
                        $preparedFields['UF_CRM_TASK'][] = 'CO_' . $companyID;
                    }
                }

                // Find or create contact
                if (!empty($sendFields['contact'])) {
                    $preparedFieldsContact = self::prepareFields($crmFields['contact'], $sendFields['contact']);

                    if ($preparedFieldsContact) {
                        if (isset($companyID) && $companyID) {
                            $preparedFieldsContact['COMPANY_ID'] = $companyID;
                        }

                        $contactID = self::contactProcessing($sendFields, $preparedFieldsContact, $crmFields);

                        // Set contact for task
                        if ($contactID) {
                            if (!empty($sendFields['uploads'])) {
                                self::sendUploadFiles($sendFields['uploads'], $contactID, 3);
                            }

                            $preparedFields['UF_CRM_TASK'][] = 'C_' . $contactID;
                        }
                    }
                }

                if (!Helper::isVerify()) {
                    if (!empty($preparedFields['DESCRIPTION'])) {
                        $preparedFields['DESCRIPTION'] = Helper::nonVerifyText()
                            . '<br>'
                            . $preparedFields['DESCRIPTION'];
                    } else {
                        $preparedFields['DESCRIPTION'] = Helper::nonVerifyText();
                    }
                }

                $result = self::sendApiRequest('tasks.task.add', ['fields' => $preparedFields]);

                self::createTaskCheckList($result, $sendFields);

                if (isset($result[0]) && is_numeric($result[0]) && $startLink[0]) {
                    $userNotify = esc_html__('New task', 'cf7-bitrix24-integration')
                        . ' [b]#'
                        . $result[0]
                        . '[/b] [url='
                        . $startLink[0] . 'company/personal/user/'
                        . (!empty($preparedFields['RESPONSIBLE_ID']) ? $preparedFields['RESPONSIBLE_ID'] : 1)
                        . '/tasks/task/view/'
                        . $result[0]
                        . '/'
                        . ']'
                        . $preparedFields['TITLE']
                        . '[/url] '
                        . esc_html__('from the site', 'cf7-bitrix24-integration')
                        . ' '
                        . get_home_url();

                    self::sendApiRequest(
                        'im.notify',
                        [
                            'to' => !empty($preparedFields['RESPONSIBLE_ID']) ? $preparedFields['RESPONSIBLE_ID'] : 1,
                            'message' => $userNotify,
                            'type' => 'SYSTEM',
                        ]
                    );
                }

                break;
            case 'contact':
                $contactID = self::contactProcessing($sendFields, $preparedFields, $crmFields);

                if ($contactID) {
                    self::sendLiveMessage($contactID, 3, $sendFields);
                }
                break;
            case 'company':
                if (!Helper::isVerify()) {
                    if (!empty($preparedFields['COMMENTS'])) {
                        $preparedFields['COMMENTS'] = Helper::nonVerifyText()
                            . '<br>'
                            . $preparedFields['COMMENTS'];
                    } else {
                        $preparedFields['COMMENTS'] = Helper::nonVerifyText();
                    }
                }

                $result = self::sendApiRequest('crm.company.add', ['fields' => $preparedFields]);

                self::sendLiveMessage($result[0], 4, $sendFields);
                break;
            default:
                // Nothing
                break;
        }

        return $result;
    }

    /**
     * @param array $createdTask
     * @param array $sendFields
     *
     * @see https://training.bitrix24.com/rest_help/tasks/task/checklistitem/add.php
     */
    public static function createTaskCheckList($createdTask, $sendFields)
    {
        if (empty($sendFields['task_check_list'])) {
            return;
        }

        if (empty($createdTask['task']) || empty($createdTask['task']['id'])) {
            return;
        }

        foreach ($sendFields['task_check_list'] as $item) {
            $item = trim($item);

            if (empty($item)) {
                continue;
            }

            self::sendApiRequest(
                'task.checklistitem.add',
                [
                    'TASKID' => $createdTask['task']['id'],
                    'FIELDS' => [
                        'TITLE' => $item,
                    ],
                ]
            );
        }
    }

    public static function checkConnection()
    {
        $apiResponse = self::sendApiRequest('scope');
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        if ($apiResponse && $apiResponse != self::$scope) {
            $errorScope = false;

            foreach (self::$scope as $scope) {
                if (!in_array($scope, $apiResponse)) {
                    $errorScope = true;
                }
            }

            if ($errorScope) {
                // Clean failed information
                \update_option(
                    Bootstrap::OPTIONS_KEY,
                    [
                        'enabled_logging' => isset($settings['enabled_logging']) ? $settings['enabled_logging'] : '',
                        'send_type' => isset($settings['send_type']) ? $settings['send_type'] : '',
                    ]
                );

                wp_die(
                    sprintf(
                        esc_html__('%1$s: %2$s', 'cf7-bitrix24-integration'),
                        esc_html__('ERROR', 'cf7-bitrix24-integration'),
                        esc_html__('Insufficient permissions. Check CRM settings.', 'cf7-bitrix24-integration')
                    ),
                    esc_html__(
                        'An error occurred while verifying the connection to the Bitrix24 CRM.',
                        'cf7-bitrix24-integration'
                    ),
                    [
                        'back_link' => true,
                    ]
                );
                // Escape ok
            }
        }

        if (empty($apiResponse)) {
            // Clean failed information
            \update_option(
                Bootstrap::OPTIONS_KEY,
                [
                    'enabled_logging' => isset($settings['enabled_logging']) ? $settings['enabled_logging'] : '',
                    'send_type' => isset($settings['send_type']) ? $settings['send_type'] : '',
                ]
            );

            wp_die(
                sprintf(
                    esc_html__('%1$s: %2$s', 'cf7-bitrix24-integration'),
                    esc_html__('ERROR', 'cf7-bitrix24-integration'),
                    esc_html__('Response CRM is not valid. Please check web hook link.', 'cf7-bitrix24-integration')
                ),
                esc_html__(
                    'An error occurred while verifying the connection to the Bitrix24 CRM.',
                    'cf7-bitrix24-integration'
                ),
                [
                    'back_link' => true,
                ]
            );
            // Escape ok
        }
    }

    public static function updateInformation()
    {
        self::updateFieldsList('crm.lead.fields', Bootstrap::LEAD_FIELDS_KEY);

        self::updateFieldsList('crm.deal.fields', Bootstrap::DEAL_FIELDS_KEY);
        self::updateFieldsList('crm.dealcategory.list', Bootstrap::DEAL_CATEGORY_LIST_KEY);

        self::updateFieldsList('crm.contact.fields', Bootstrap::CONTACT_FIELDS_KEY);
        self::updateFieldsList('crm.company.fields', Bootstrap::COMPANY_FIELDS_KEY);

        $crmFields = new CrmFields();
        update_option(Bootstrap::TASK_FIELDS_KEY, $crmFields->taskFields);

        self::updateCurrencyList();
        self::updateFieldsList('crm.status.list', Bootstrap::STATUS_LIST_KEY);
    }

    public static function updateFieldsList($method, $optionKey)
    {
        $apiResponse = self::sendApiRequest($method);

        if ($apiResponse) {
            update_option($optionKey, $apiResponse);
        }
    }

    public static function updateCurrencyList()
    {
        $apiResponse = self::sendApiRequest('crm.currency.list');

        if ($apiResponse) {
            $currencyList = [];

            foreach ($apiResponse as $currency) {
                $currencyList[$currency['CURRENCY']] = $currency['FULL_NAME'];
            }

            update_option(Bootstrap::CURRENCY_LIST_KEY, $currencyList);
        }
    }

    public static function existEmailPhone($values, $currentValue)
    {
        foreach ($values as $field) {
            if ($field['VALUE'] === $currentValue) {
                return true;
            }
        }

        return false;
    }

    public static function findItemByField($sendFields, $type, $field)
    {
        if (!empty($sendFields[$type][$field])) {
            $findParams = [
                'type' => $field,
                'entity_type' => $type,
                'values' => [$sendFields[$type][$field]],
            ];

            // https://dev.1c-bitrix.ru/rest_help/crm/auxiliary/duplicates/crm_duplicate_findbycomm.php
            $findItem = self::sendApiRequest('crm.duplicate.findbycomm', $findParams);

            if (!empty($findItem)) {
                $ids = current($findItem);

                return $ids[0];
            }
        }

        return false;
    }

    public static function findAllItemsByField($sendFields, $type, $field)
    {
        if (!empty($sendFields[$type][$field])) {
            $findParams = [
                'FILTER' => [
                    $field => $sendFields[$type][$field],
                ],
                'SELECT' => [
                    'ID',
                ],
            ];

            $findItems = self::sendApiRequest('crm.' . $type . '.list', $findParams);

            if ($findItems) {
                return $findItems;
            }
        }

        return false;
    }

    public static function ignoreFields($sendFields, $currentFieldsIgnore = [])
    {
        $returnFields = $sendFields;

        foreach ($sendFields as $key => $_) {
            if (in_array($key, $currentFieldsIgnore, true)) {
                unset($returnFields[$key]);
            }
        }

        return $returnFields;
    }

    public static function prepareFields($crmFields, $sendFields)
    {
        foreach ($crmFields as $key => $_) {
            if (
                in_array($key, ['PHONE', 'EMAIL', 'WEB'], true)
                && !empty($sendFields[$key])
            ) {
                $sendFields[$key] = [
                    [
                        'VALUE' => $sendFields[$key],
                        'VALUE_TYPE' => 'WORK',
                    ],
                ];
            }
        }

        if (!empty($sendFields['COMMENTS'])) {
            $sendFields['COMMENTS'] = str_replace(
                "\n",
                '<br>',
                strip_tags($sendFields['COMMENTS'])
            );
        }

        return $sendFields;
    }

    public static function prepareFieldsToUpdate($crmFields, $sendFields)
    {
        foreach ($crmFields as $key => $_) {
            if ($sendFields[$key] === '') {
                unset($sendFields[$key]);
            }

            if (
                in_array($key, ['PHONE', 'EMAIL', 'WEB'], true)
                && !empty($sendFields[$key])
            ) {
                $sendFields[$key] = [
                    [
                        'VALUE' => $sendFields[$key],
                        'VALUE_TYPE' => 'WORK',
                    ],
                ];
            }
        }

        if (!empty($sendFields['COMMENTS'])) {
            $sendFields['COMMENTS'] = str_replace(
                "\n",
                '<br>',
                strip_tags($sendFields['COMMENTS'])
            );
        }

        return $sendFields;
    }

    public static function mergeFields($existLead, $preparedFields, $crmFields)
    {
        foreach ($crmFields as $key => $field) {
            if (strpos($key, 'UF_CRM_') === false) {
                continue;
            }

            if (empty($field['isMultiple']) || !in_array($field['type'], ['string', 'url', 'enumeration'])) {
                continue;
            }

            if (empty($preparedFields[$key]) || empty($existLead[$key]) || !is_array($preparedFields[$key])) {
                continue;
            }

            $preparedFields[$key] = array_merge($existLead[$key], $preparedFields[$key]);
            $preparedFields[$key] = array_unique($preparedFields[$key], SORT_STRING);
        }

        return $preparedFields;
    }

    public static function resolveNextResponsible($list, $type, $formID)
    {
        $list = explode(',', $list);
        $list = array_map('trim', $list);

        if (count($list) === 1) {
            return $list[0];
        }

        $last = get_post_meta($formID, '_last_' . $type . '_responsible', true);
        $lastKey = array_search($last, $list);

        if (empty($last) || $lastKey === false || ($lastKey + 1) >= count($list)) {
            update_post_meta($formID, '_last_' . $type . '_responsible', $list[0]);

            return $list[0];
        }

        update_post_meta($formID, '_last_' . $type . '_responsible', $list[$lastKey + 1]);

        return $list[$lastKey + 1];
    }

    public static function sendLiveMessage($entityID, $entityType, $message)
    {
        if (!empty($message['livefeedmessage'])) {
            self::sendApiRequest(
                'crm.livefeedmessage.add',
                [
                    'fields' => [
                        'POST_TITLE' => 'Сообщение',
                        'MESSAGE' => $message['livefeedmessage'],
                        'ENTITYTYPEID' => $entityType, // 1 - lead, 2 - deal, 3 - contact, 4 - company
                        'ENTITYID' => $entityID,
                    ],
                ]
            );
        }
    }

    public static function sendApiRequest($method, $fields = [])
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        Helper::log('POST - ' . $method, self::clearFileDataBeforeLog($fields));

        try {
            $response = wp_remote_post(
                $settings['webhook'] . $method,
                [
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
                    'body' => $fields,
                ]
            );

            if (is_wp_error($response)) {
                throw new \Exception($response->get_error_message(), (int) $response->get_error_code());
            }

            $body = $response['body'];

            if (!empty($body)) {
                $result = json_decode(str_replace('\'', '"', $body), true);

                Helper::log('bitrix decode response', $result);

                if (isset($result['result'])) {
                    return (array) $result['result'];
                }

                if (!empty($result['error'])) {
                    throw new \Exception(
                        isset($result['error_message'])
                            ? esc_html($result['error_message'])
                            : esc_html($result['error_description']),
                        (int) $result['error']
                    );
                }
            }

            Helper::log('bitrix empty response', $response, 'warning');
        } catch (\Exception $error) {
            Helper::log($error->getCode() . ': ' . $error->getMessage(), [], 'error');
        }

        return [];
    }

    private static function contactProcessing($sendFields, $preparedFields, $crmFields)
    {
        $existContact = '';
        $contactID = '';
        $contacts = [];

        if (!isset($sendFields['always_create_new_contact'])) {
            $contactID = self::findItemByField($sendFields, 'contact', 'PHONE');

            if (!$contactID) {
                $contactID = self::findItemByField($sendFields, 'contact', 'EMAIL');
            }

            if ($contactID && isset($sendFields['contact_update_exists'])) {
                $existContact = self::sendApiRequest(
                    'crm.contact.get',
                    [
                        'id' => $contactID,
                    ]
                );
            }
        } else {
            $contacts = self::findAllItemsByField($sendFields, 'contact', 'PHONE');

            if (!$contacts) {
                $contacts = self::findAllItemsByField($sendFields, 'contact', 'EMAIL');
            }

            if ($contacts) {
                foreach ($contacts as $contact) {
                    self::sendLiveMessage($contact['ID'], 3, $sendFields);
                }
            }
        }

        if ($existContact) {
            if (!empty($preparedFields['COMPANY_ID']) && empty($existContact['COMPANY_ID'])) {
                $sendFields['contact']['COMPANY_ID'] = $preparedFields['COMPANY_ID'];
            }

            $preparedFields = self::prepareFieldsToUpdate($crmFields['contact'], $sendFields['contact']);

            // fix duplicate phone
            if (!empty($existContact['PHONE'])
                && !empty($preparedFields['PHONE'])
                && self::existEmailPhone($existContact['PHONE'], $preparedFields['PHONE'][0]['VALUE'])
            ) {
                unset($preparedFields['PHONE']);
            }

            // fix duplicate email
            if (!empty($existContact['EMAIL'])
                && !empty($preparedFields['EMAIL'])
                && self::existEmailPhone($existContact['EMAIL'], $preparedFields['EMAIL'][0]['VALUE'])
            ) {
                unset($preparedFields['EMAIL']);
            }

            self::sendApiRequest(
                'crm.contact.update',
                [
                    'id' => $existContact['ID'],
                    'fields' => $preparedFields,
                ]
            );

            return $existContact['ID'];
        }

        if (!$contactID && $preparedFields) {
            if (!Helper::isVerify()) {
                if (!empty($preparedFields['COMMENTS'])) {
                    $preparedFields['COMMENTS'] = Helper::nonVerifyText()
                        . "\n"
                        . $preparedFields['COMMENTS'];
                } else {
                    $preparedFields['COMMENTS'] = Helper::nonVerifyText();
                }
            }

            if ($contacts) {
                $preparedFields = apply_filters('cf7_bx_fields_for_duplicate', $preparedFields);
            }

            $result = self::sendApiRequest('crm.contact.add', ['fields' => $preparedFields]);

            if ($result) {
                return $result[0];
            }
        }

        return $contactID ? $contactID : false;
    }

    private static function sendUploadFiles($fileList, $entityID, $entityType)
    {
        $files = [];

        // do not send a message to the crm feed if the fileset is empty
        if (empty($fileList)) {
            return;
        }

        foreach ($fileList as $file) {
            $files[] = [
                $file['name'],
                base64_encode(file_get_contents($file['path'])),
            ];
        }

        self::sendApiRequest(
            'crm.livefeedmessage.add',
            [
                'fields' => [
                    'MESSAGE' => esc_html__('Uploaded files', 'cf7-bitrix24-integration'),
                    'ENTITYTYPEID' => $entityType, // LEAD
                    'ENTITYID' => $entityID,
                    'FILES' => $files,
                ],
            ]
        );

        foreach ($fileList as $file) {
            unlink($file['path']);
        }
    }

    private static function clearFileDataBeforeLog($data)
    {
        if (isset($data['fields']) && !empty($data['fields']['FILES'])) {
            $data['fields']['FILES'] = 'file data';
        }

        if (isset($data['fields'])) {
            foreach ($data['fields'] as $key => $field) {
                if (!is_array($field)) {
                    continue;
                }

                if (isset($field['fileData'])) {
                    $field['fileData'][1] = 'content';

                    $data['fields'][$key] = $field;

                    continue;
                }

                foreach ($field as $subkey => $value) {
                    if (!is_array($value) || !isset($value['fileData'])) {
                        continue;
                    }

                    $value['fileData'][1] = 'content';

                    $data['fields'][$key][$subkey] = $value;
                }
            }
        }

        return $data;
    }
}
