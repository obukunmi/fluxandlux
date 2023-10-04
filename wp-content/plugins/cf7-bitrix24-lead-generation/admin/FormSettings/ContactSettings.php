<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\CrmFields;

class ContactSettings
{
    public static function render($meta)
    {
        $renderFields = new RenderFields('contact');
        $renderFields->startTable();

        $currentValues = isset($meta['contact']) ? $meta['contact'] : [];
        $contactFields = get_option(Bootstrap::CONTACT_FIELDS_KEY, []);

        $crmFields = new CrmFields();
        $crmFields = $crmFields->fields;

        foreach ($contactFields as $key => $field) {
            // Not show fields
            if (in_array($key, CrmFields::$breakFields['contact'])) {
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
                    : (
                        isset($field['title'])
                            ? $field['title']
                            : $key
                    )
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
                    $currentValue
                );
            } elseif ($field['type'] === 'char' || $field['type'] === 'boolean') {
                $renderFields->inputCheckboxField(
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif ($key === 'TYPE_ID') {
                $renderFields->statusField(
                    'CONTACT_TYPE',
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
            } elseif ($key === 'HONORIFIC') {
                $renderFields->statusField(
                    'HONORIFIC',
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
            } elseif (in_array($key, ['COMMENTS', 'SOURCE_DESCRIPTION', 'STATUS_DESCRIPTION'])) {
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
