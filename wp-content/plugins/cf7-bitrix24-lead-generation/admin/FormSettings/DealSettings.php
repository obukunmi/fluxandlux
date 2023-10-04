<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\CrmFields;

class DealSettings
{
    public static function render($meta)
    {
        $renderFields = new RenderFields('deal');
        $renderFields->startTable();

        $currentValues = isset($meta['deal']) ? $meta['deal'] : [];
        $dealFields = get_option(Bootstrap::DEAL_FIELDS_KEY, []);

        $crmFields = new CrmFields();
        $crmFields = $crmFields->fields;

        foreach ($dealFields as $key => $field) {
            // Not show fields
            if (in_array($key, CrmFields::$breakFields['deal'])) {
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
            } elseif ($key === 'TYPE_ID') {
                $renderFields->statusField(
                    'DEAL_TYPE',
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif ($key === 'STAGE_ID') {
                $renderFields->statusField(
                    'DEAL_STAGE',
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
                $renderFields->textareaField($key, $title, $currentValue);
            } else {
                $description = '';

                if (in_array($key, ['ASSIGNED_BY_ID', 'RESPONSIBLE_ID'])) {
                    $description = esc_html__(
                        'you can specify several, separated by commas, then the requests will be distributed sequentially',
                        'cf7-bitrix24-integration'
                    );
                }

                $renderFields->inputTextField($key, $title, $currentValue, $description);
            }

            $renderFields->endFieldRow();
        }

        $renderFields->endTable();
    }
}
