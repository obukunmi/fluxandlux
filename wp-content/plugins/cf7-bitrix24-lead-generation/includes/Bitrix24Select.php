<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Includes;

class Bitrix24Select
{
    private static $instance = false;

    private $statusTypes = [
        'lead' => [
            'STATUS_ID' => 'STATUS',
            'SOURCE_ID' => 'SOURCE',
        ],
        'deal' => [
            'TYPE_ID' => 'DEAL_TYPE',
            'STAGE_ID' => 'DEAL_STAGE',
        ],
        'contact' => [
            'TYPE_ID' => 'CONTACT_TYPE',
            'SOURCE_ID' => 'SOURCE',
        ],
        'company' => [
            'COMPANY_TYPE' => 'COMPANY_TYPE',
            'SOURCE_ID' => 'SOURCE',
            'INDUSTRY' => 'INDUSTRY',
            'EMPLOYEES' => 'EMPLOYEES',
        ],
    ];

    protected function __construct()
    {
        add_action('wpcf7_init', [$this, 'addFormTag']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addFormTag()
    {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_add_form_tag(
                [
                    'bitrix24_select',
                    'bitrix24_select*',
                ],
                [$this, 'formTagHandler'],
                [
                    'name-attr' => true,
                    'selectable-values' => true,
                ]
            );

            add_filter('wpcf7_validate_bitrix24_select', [$this, 'validationFilter'], 10, 2);
            add_filter('wpcf7_validate_bitrix24_select*', [$this, 'validationFilter'], 10, 2);
        }
    }

    public function formTagHandler($tag)
    {
        if (empty($tag->name)) {
            return '';
        }

        /** @psalm-suppress UndefinedFunction */
        $validationError = \wpcf7_get_validation_error($tag->name);

        /** @psalm-suppress UndefinedFunction */
        $class = \wpcf7_form_controls_class($tag->type);

        $class .= ' wpcf7-select';

        if ($validationError) {
            $class .= ' wpcf7-not-valid';
        }

        $attributes = [];

        $attributes['class'] = $tag->get_class_option($class);
        $attributes['id'] = $tag->get_id_option();
        $attributes['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);

        $entity = $tag->get_option('entity', '[-0-9a-zA-Z_]+', true);
        $field = $tag->get_option('field', '[-0-9a-zA-Z_]+', true);

        if ($tag->is_required()) {
            $attributes['aria-required'] = 'true';
        }

        $attributes['aria-invalid'] = $validationError ? 'true' : 'false';

        $includeBlank = $tag->has_option('include_blank');

        if ($tag->has_option('size')) {
            $size = $tag->get_option('size', 'int', true);

            if ($size) {
                $attributes['size'] = $size;
            } else {
                $attributes['size'] = 1;
            }
        }

        $multiple = $tag->has_option('multiple');

        if ($multiple) {
            $attributes['multiple'] = true;
        }

        $resolveItems = $this->resolveValues($entity, $field);
        $values = $resolveItems['values'];
        $labels = $resolveItems['labels'];

        if ($includeBlank || empty($values)) {
            $value = (string) reset($tag->values);

            if ($tag->has_option('placeholder')) {
                array_unshift($labels, $value);
                array_unshift($values, '');
            } else {
                array_unshift($labels, '---');
                array_unshift($values, '');
            }
        }

        $html = '';

        foreach ($values as $key => $value) {
            $itemAttributes = [
                'value' => $value,
                'selected' => '',
            ];

            /** @psalm-suppress UndefinedFunction */
            $itemAttributes = \wpcf7_format_atts($itemAttributes);

            $label = isset($labels[$key]) ? $labels[$key] : $value;

            $html .= sprintf(
                '<option %1$s>%2$s</option>',
                $itemAttributes,
                esc_html($label)
            );
        }

        if ($multiple) {
            $attributes['name'] = $tag->name . '[]';
        } else {
            $attributes['name'] = $tag->name;
        }

        /** @psalm-suppress UndefinedFunction */
        $attributes = \wpcf7_format_atts($attributes);

        return sprintf(
            '<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
            \sanitize_html_class($tag->name),
            $attributes,
            $html,
            $validationError
        );
    }

    public function validationFilter($result, $tag)
    {
        $name = $tag->name;

        if (isset($_POST[$name]) && is_array($_POST[$name])) {
            foreach ($_POST[$name] as $key => $value) {
                if ($value === '') {
                    unset($_POST[$name][$key]);
                }
            }
        }

        $empty = !isset($_POST[$name]) || empty($_POST[$name]) && $_POST[$name] !== '0';

        if ($tag->is_required() && $empty) {
            /** @psalm-suppress UndefinedFunction */
            $result->invalidate($tag, \wpcf7_get_message('invalid_required'));
        }

        return $result;
    }

    private function resolveValues($entity, $field)
    {
        $items = [
            'values' => [],
            'labels' => [],
        ];

        if (isset($this->statusTypes[$entity], $this->statusTypes[$entity][$field])) {
            $statusList = get_option(Bootstrap::STATUS_LIST_KEY);

            foreach ($statusList as $status) {
                if ($status['ENTITY_ID'] === $this->statusTypes[$entity][$field]) {
                    $items['values'][] = $status['STATUS_ID'];
                    $items['labels'][] = $status['NAME'];
                }
            }
        } elseif ($field == 'CURRENCY_ID') {
            $currency = get_option(Bootstrap::CURRENCY_LIST_KEY);

            $items['values'] = array_keys($currency);
            $items['labels'] = array_values($currency);
        } else {
            $fields = [];

            switch ($entity) {
                case 'lead':
                    $fields = get_option(Bootstrap::LEAD_FIELDS_KEY);
                    break;
                case 'deal':
                    $fields = get_option(Bootstrap::DEAL_FIELDS_KEY);
                    break;
                case 'contact':
                    $fields = get_option(Bootstrap::CONTACT_FIELDS_KEY);
                    break;
                case 'company':
                    $fields = get_option(Bootstrap::COMPANY_FIELDS_KEY);
                    break;
                default:
                    // Nothing
                    break;
            }

            if ($fields && isset($fields[$field]) && !empty($fields[$field]['items'])) {
                foreach ($fields[$field]['items'] as $item) {
                    $items['values'][] = $item['ID'];
                    $items['labels'][] = $item['VALUE'];
                }
            }
        }

        return $items;
    }
}
