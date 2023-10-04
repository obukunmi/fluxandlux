<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Includes;

class CrmFields
{
    public $fields;

    public $taskFields;

    public static $breakFields = [
        'lead' => [
            'STATUS_SEMANTIC_ID',
            'ADDRESS_COUNTRY_CODE',
            'ADDRESS_LOC_ADDR_ID',
            'ORIGINATOR_ID',
            'ORIGIN_ID',
            'COMPANY_ID',
            'CONTACT_ID',
            'CONTACT_IDS',
        ],
        'deal' => [
            'CATEGORY_ID',
            'STAGE_SEMANTIC_ID',
            'IS_NEW',
            'TAX_VALUE',
            'COMPANY_ID',
            'CONTACT_ID',
            'CONTACT_IDS',
            'BEGINDATE',
            'CLOSEDATE',
            'CLOSED',
            'ADDITIONAL_INFO',
            'LOCATION_ID',
            'ORIGINATOR_ID',
            'ORIGIN_ID',
            'IS_RECURRING',
            'IS_MANUAL_OPPORTUNITY',
            'IS_RETURN_CUSTOMER',
            'IS_REPEATED_APPROACH',
            'ADDRESS_LOC_ADDR_ID',
            'REG_ADDRESS_LOC_ADDR_ID',
        ],
        'contact' => [
            'ADDRESS_COUNTRY_CODE',
            'ADDRESS_LOC_ADDR_ID',
            'REG_ADDRESS_LOC_ADDR_ID',
            'COMPANY_ID',
            'COMPANY_IDS',
            'ORIGINATOR_ID',
            'ORIGIN_ID',
            'ORIGIN_VERSION',
            'FACE_ID',
        ],
        'company' => [
            'ADDRESS_COUNTRY_CODE',
            'ADDRESS_LEGAL',
            'REG_ADDRESS',
            'REG_ADDRESS_2',
            'REG_ADDRESS_CITY',
            'REG_ADDRESS_POSTAL_CODE',
            'REG_ADDRESS_REGION',
            'REG_ADDRESS_PROVINCE',
            'REG_ADDRESS_COUNTRY',
            'REG_ADDRESS_COUNTRY_CODE',
            'ADDRESS_LOC_ADDR_ID',
            'REG_ADDRESS_LOC_ADDR_ID',
            'LOGO',
            'IS_MY_COMPANY',
            'CONTACT_ID',
            'ORIGINATOR_ID',
            'ORIGIN_ID',
            'ORIGIN_VERSION',
        ],
        'task' => [],
    ];

    public function __construct()
    {
        $this->fields = [
            'ADDRESS' => esc_html__('Street, building', 'cf7-bitrix24-integration'),
            'ADDRESS_2' => esc_html__('Suite / Apartment', 'cf7-bitrix24-integration'),
            'ADDRESS_CITY' => esc_html__('City', 'cf7-bitrix24-integration'),
            'ADDRESS_POSTAL_CODE' => esc_html__('Zip', 'cf7-bitrix24-integration'),
            'ADDRESS_REGION' => esc_html__('Region', 'cf7-bitrix24-integration'),
            'ADDRESS_PROVINCE' => esc_html__('State / Province', 'cf7-bitrix24-integration'),
            'ADDRESS_COUNTRY' => esc_html__('Country', 'cf7-bitrix24-integration'),
            'BIRTHDATE' => esc_html__('Birth date', 'cf7-bitrix24-integration'),
            'BANKING_DETAILS' => esc_html__('Payment details', 'cf7-bitrix24-integration'),
            'INDUSTRY' => esc_html__('Industry', 'cf7-bitrix24-integration'),
            'EMPLOYEES' => esc_html__('Employees', 'cf7-bitrix24-integration'),
            'REVENUE' => esc_html__('Annual income', 'cf7-bitrix24-integration'),
            'COMPANY_TITLE' => esc_html__('Company Name', 'cf7-bitrix24-integration'),
            'COMPANY_TYPE' => esc_html__('Company type', 'cf7-bitrix24-integration'),
            'OPENED' => esc_html__('Visible to everyone', 'cf7-bitrix24-integration'),
            'TITLE' => esc_html__('Title', 'cf7-bitrix24-integration'),
            'TYPE_ID' => esc_html__('Type', 'cf7-bitrix24-integration'),
            'STAGE_ID' => esc_html__('Stage / Pipeline', 'cf7-bitrix24-integration'),
            'PROBABILITY' => esc_html__('Probability, %', 'cf7-bitrix24-integration'),
            'NAME' => esc_html__('First name', 'cf7-bitrix24-integration'),
            'HONORIFIC' => esc_html__('Honorific', 'cf7-bitrix24-integration'),
            'LAST_NAME' => esc_html__('Last name', 'cf7-bitrix24-integration'),
            'SECOND_NAME' => esc_html__('Middle name', 'cf7-bitrix24-integration'),
            'POST' => esc_html__('Position', 'cf7-bitrix24-integration'),
            'RESPONSIBLE_ID' => esc_html__('Responsible user ID', 'cf7-bitrix24-integration'),
            'ACCOMPLICES' => esc_html__('Accomplice user ID', 'cf7-bitrix24-integration'),
            'AUDITORS' => esc_html__('Auditor user ID', 'cf7-bitrix24-integration'),
            'COMMENTS' => esc_html__('Comments', 'cf7-bitrix24-integration'),
            'DESCRIPTION' => esc_html__('Description', 'cf7-bitrix24-integration'),
            'SOURCE_DESCRIPTION' => esc_html__('Source Description', 'cf7-bitrix24-integration'),
            'STATUS_DESCRIPTION' => esc_html__('Status description', 'cf7-bitrix24-integration'),
            'OPPORTUNITY' => esc_html__('Opportunity', 'cf7-bitrix24-integration'),
            'CURRENCY_ID' => esc_html__('Currency', 'cf7-bitrix24-integration'),
            'PRODUCT_ID' => esc_html__('Product ID from CRM', 'cf7-bitrix24-integration'),
            'SOURCE_ID' => esc_html__('Source', 'cf7-bitrix24-integration'),
            'STATUS_ID' => esc_html__('Status', 'cf7-bitrix24-integration'),
            'PHONE' => esc_html__('Phone', 'cf7-bitrix24-integration'),
            'PHOTO' => esc_html__('Photo', 'cf7-bitrix24-integration'),
            'EMAIL' => esc_html__('E-mail', 'cf7-bitrix24-integration'),
            'WEB' => esc_html__('Site', 'cf7-bitrix24-integration'),
            'TAGS' => esc_html__('Tags', 'cf7-bitrix24-integration'),
            'DEADLINE' => esc_html__('Deadline (use the date field type)', 'cf7-bitrix24-integration'),
            'GROUP_ID' => esc_html__('ID workgroup', 'cf7-bitrix24-integration'),
            'ALLOW_CHANGE_DEADLINE' => esc_html__('Responsible person can change deadline', 'cf7-bitrix24-integration'),
            'TASK_CONTROL' => esc_html__('Approve task when completed', 'cf7-bitrix24-integration'),
            'ALLOW_TIME_TRACKING' => esc_html__('Task planned time', 'cf7-bitrix24-integration'),
            'ASSIGNED_BY_ID' => esc_html__('Responsible', 'cf7-bitrix24-integration'),
            'CREATED_BY' => esc_html__('Created by', 'cf7-bitrix24-integration'),
            'PRIORITY' => esc_html__('Priority', 'cf7-bitrix24-integration'),
        ];

        $this->taskFields = [
            'TITLE' => [
                'type' => 'string',
                'isRequired' => true,
                'isReadOnly' => false,
            ],
            'DESCRIPTION' => [
                'type' => 'string',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
            'RESPONSIBLE_ID' => [
                'type' => 'user',
                'isRequired' => true,
                'isReadOnly' => false,
            ],
            'CREATED_BY' => [
                'type' => 'user',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
            'ACCOMPLICES' => [
                'type' => 'user',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
            'AUDITORS' => [
                'type' => 'user',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
            'TAGS' => [
                'type' => 'string',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
            'DEADLINE' => [
                'type' => 'date',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
            'GROUP_ID' => [
                'type' => 'string',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
            'PRIORITY' => [
                'type' => 'enumeration',
                'isMultiple' => false,
                'isRequired' => false,
                'isReadOnly' => false,
                'items' => [
                    [
                        'ID' => 1,
                        'VALUE' => esc_html__('Default', 'cf7-bitrix24-integration'),
                    ],
                    [
                        'ID' => 2,
                        'VALUE' => esc_html__('High Priority', 'cf7-bitrix24-integration'),
                    ],
                ],
            ],
            'ALLOW_CHANGE_DEADLINE' => [
                'type' => 'char',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
            'TASK_CONTROL' => [
                'type' => 'char',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
            'ALLOW_TIME_TRACKING' => [
                'type' => 'char',
                'isRequired' => false,
                'isReadOnly' => false,
            ],
        ];
    }

    private function __clone()
    {
    }
}
