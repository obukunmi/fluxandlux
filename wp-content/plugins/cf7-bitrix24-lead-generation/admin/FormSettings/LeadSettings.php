<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\CrmFields;

class LeadSettings
{
    public static function render($meta)
    {
        $renderFields = new RenderFields('lead');
        $renderFields->startTable();

        // ignore fields setting start
        $renderFieldsIgnore = new RenderFields('ignore_fields');

        $currentValues = isset($meta['lead']) ? $meta['lead'] : [];
        $currentValuesIgnore = isset($meta['ignore_fields']) ? $meta['ignore_fields'] : [];

        echo '<tr><th>'
            . esc_html__('Ignore fields on update exists entry', 'cf7-bitrix24-integration')
            . '</th><td>';

        $currentValue = isset($currentValuesIgnore['lead']) ? $currentValuesIgnore['lead'] : '';

        $renderFieldsIgnore->textareaField(
            'lead',
            esc_html__('Ignore fields on update exists entry', 'cf7-bitrix24-integration'),
            $currentValue,
            esc_html__('Example:', 'cf7-bitrix24-integration') . ' TITLE,NAME,SOURCE_ID',
            esc_html__(
                'If you want some of the fields to be skipped and not changed when updating an existing lead, specify them with a comma.',
                'cf7-bitrix24-integration'
            )
        );

        echo '<hr>';

        $renderFields->endFieldRow();
        // ignore fields setting end

        $leadFields = get_option(Bootstrap::LEAD_FIELDS_KEY, []);

        $crmFields = new CrmFields();
        $crmFields = $crmFields->fields;

        foreach ($leadFields as $key => $field) {
            // Not show fields
            if (in_array($key, CrmFields::$breakFields['lead'])) {
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
            } elseif ($key === 'STATUS_ID') {
                $renderFields->statusField(
                    'STATUS',
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
