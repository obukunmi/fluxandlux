<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\CrmFields;

class CompanySettings
{
    public static function render($meta)
    {
        $renderFields = new RenderFields('company');
        $renderFields->startTable();

        $currentValues = isset($meta['company']) ? $meta['company'] : [];
        $companyFields = get_option(Bootstrap::COMPANY_FIELDS_KEY, []);

        $crmFields = new CrmFields();
        $crmFields = $crmFields->fields;

        foreach ($companyFields as $key => $field) {
            // Not show fields
            if (in_array($key, CrmFields::$breakFields['company'])) {
                continue;
            }

            // Not show read only fields
            if ($field['isReadOnly'] === true) {
                continue;
            }

            $title = isset($crmFields[$key])
                ? $crmFields[$key]
                : (
                    isset($field['formLabel'])
                        ? $field['formLabel']
                        : $key
                );

            $renderFields->startFieldRow($title, $key, $field['isRequired']);

            $currentValue = isset($currentValues[$key]) ? $currentValues[$key] : '';
            $currentValuePopulate = isset($currentValues[$key . '-populate']) ? $currentValues[$key . '-populate'] : '';

            if ($field['type'] === 'enumeration' && !empty($field['items'])) {
                $selectItems = [];

                foreach ($field['items'] as $item) {
                    $selectItems[$item['ID']] = $item['VALUE'];
                }

                $renderFields->selectField(
                    $selectItems,
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif ($field['type'] === 'char' || $field['type'] === 'boolean') {
                $renderFields->inputCheckboxField(
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif ($key === 'COMPANY_TYPE') {
                $renderFields->statusField(
                    'COMPANY_TYPE',
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif ($key === 'SOURCE_ID') {
                $renderFields->statusField(
                    'SOURCE',
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif ($key === 'INDUSTRY') {
                $renderFields->statusField(
                    'INDUSTRY',
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif ($key === 'EMPLOYEES') {
                $renderFields->statusField(
                    'EMPLOYEES',
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif ($key === 'CURRENCY_ID') {
                $renderFields->selectField(
                    get_option(Bootstrap::CURRENCY_LIST_KEY),
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif (in_array($key, ['COMMENTS', 'BANKING_DETAILS'])) {
                $renderFields->textareaField(
                    $key,
                    $title,
                    $currentValue
                );
            } else {
                $renderFields->inputTextField(
                    $key,
                    $title,
                    $currentValue
                );
            }

            $renderFields->endFieldRow();
        }

        $renderFields->endTable();
    }
}
