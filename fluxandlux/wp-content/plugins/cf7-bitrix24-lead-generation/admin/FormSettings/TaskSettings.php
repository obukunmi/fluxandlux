<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Cf7\Bitrix24\Integration\Includes\CrmFields;

class TaskSettings
{
    public static function render($meta)
    {
        $renderFields = new RenderFields('task');
        $renderFields->startTable();

        $currentValues = isset($meta['task']) ? $meta['task'] : [];
        $taskFields = get_option(Bootstrap::TASK_FIELDS_KEY, []);

        $crmFields = new CrmFields();
        $crmFields = $crmFields->fields;

        foreach ($taskFields as $key => $field) {
            // Not show fields
            if (in_array($key, CrmFields::$breakFields['task'])) {
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
            } elseif (in_array($key, ['DESCRIPTION'])) {
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
