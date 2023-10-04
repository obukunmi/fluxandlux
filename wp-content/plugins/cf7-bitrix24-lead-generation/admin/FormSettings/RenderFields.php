<?php

namespace Itgalaxy\Cf7\Bitrix24\Integration\Admin\FormSettings;

use Itgalaxy\Cf7\Bitrix24\Integration\Includes\Bootstrap;

class RenderFields
{
    public $fieldNameStart = 'cf7Bitrix[';

    public function __construct($type = '')
    {
        $this->fieldNameStart .= $type . '][';
    }

    public function startTable()
    {
        echo '<table class="form-table">';
    }

    public function endTable()
    {
        echo '</table>';
    }

    public function startFieldRow($title, $key, $isRequired)
    {
        ?>
        <tr>
        <th>
            <?php echo esc_html($title . ' (' . $key . ')'); ?>
            <?php echo $isRequired ? '<span style="color:red;"> * </span>' : ''; ?>
        </th>
        <td>
        <?php
    }

    public function endFieldRow()
    {
        ?></td></tr><?php
    }

    public function selectField($list, $name, $title, $currentValue, $currentValuePopulate = '')
    {
        ?>
        <table width="100%">
            <tr>
                <td style="width: 50%;">
                    <label><?php esc_html_e('Default value', 'cf7-bitrix24-integration'); ?></label>
                    <br>
                    <select id="__<?php echo esc_attr($name); ?>"
                        title="<?php echo esc_attr($title); ?>"
                        name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]">
                        <option value=""><?php esc_html_e('Not chosen', 'cf7-bitrix24-integration'); ?></option>
                        <?php
                        foreach ((array) $list as $value => $label) {
                            echo '<option value="'
                                . esc_attr($value)
                                . '"'
                                . ($currentValue == $value ? ' selected' : '')
                                . '>'
                                . esc_html($value . ' - ' . $label)
                                . '</option>';
                        } ?>
                    </select>
                </td>
                <td>
                    <label><?php esc_html_e('Form value', 'cf7-bitrix24-integration'); ?></label>
                    <a href="<?php echo esc_url(Bootstrap::$pluginUrl . 'documentation/index.html#additional'); ?>"
                        target="_blank"
                        style="text-decoration: none;">
                        <span class="dashicons dashicons-editor-help"></span>
                    </a>
                    <br>
                    <input id="__<?php echo esc_attr($name); ?>-populate"
                        type="text"
                        class="large-text code"
                        placeholder="Use `bitrix24_select` field"
                        title="<?php echo esc_attr($title); ?>"
                        name="<?php echo esc_attr($this->fieldNameStart . $name); ?>-populate]"
                        value="<?php echo esc_attr($currentValuePopulate); ?>">
                </td>
            </tr>
        </table>
        <?php
    }

    public function statusField($type, $name, $title, $currentValue, $currentValuePopulate = '')
    {
        $list = $this->getStatusListByType($type); ?>
        <table width="100%">
            <tr>
                <td style="width: 50%;">
                    <label><?php esc_html_e('Default value', 'cf7-bitrix24-integration'); ?></label>
                    <br>
                    <select id="__<?php echo esc_attr($name); ?>"
                        title="<?php echo esc_attr($title); ?>"
                        name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]">
                        <option value=""><?php esc_html_e('Not chosen', 'cf7-bitrix24-integration'); ?></option>
                        <?php
                        foreach ((array) $list as $value => $label) {
                            echo '<option value="'
                                . esc_attr($value)
                                . '"'
                                . ($currentValue == $value ? ' selected' : '')
                                . '>'
                                . esc_html($value . ' - ' . $label)
                                . '</option>';
                        } ?>
                    </select>
                </td>
                <td>
                    <label><?php esc_html_e('Form value', 'cf7-bitrix24-integration'); ?></label>
                    <a href="<?php echo esc_url(Bootstrap::$pluginUrl . 'documentation/index.html#additional'); ?>"
                        target="_blank"
                        style="text-decoration: none;">
                        <span class="dashicons dashicons-editor-help"></span>
                    </a>
                    <br>
                        <input id="__<?php echo esc_attr($name); ?>-populate"
                            type="text"
                            class="large-text code"
                            placeholder="Use `bitrix24_select` field"
                            title="<?php echo esc_attr($title); ?>"
                            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>-populate]"
                            value="<?php echo esc_attr($currentValuePopulate); ?>">
                </td>
            </tr>
        </table>
        <?php
    }

    public function inputTextField($name, $title, $currentValue, $description = '')
    {
        ?>
        <input id="__<?php echo esc_attr($name); ?>"
            type="text"
            class="large-text code"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            value="<?php echo esc_attr($currentValue); ?>">
        <?php

        echo $description ? '<p class="description">' . esc_html($description) . '</p>' : '';
    }

    public function inputCheckboxField($name, $title, $currentValue, $currentValuePopulate = '')
    {
        ?>
        <table width="100%">
            <tr>
                <td style="width: 50%;">
                    <label><?php esc_html_e('Default value', 'cf7-bitrix24-integration'); ?></label>
                    <br>
                    <input type="hidden"
                        name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
                        value="N">
                    <input id="__<?php echo esc_attr($name); ?>"
                        type="checkbox"
                        title="<?php echo esc_attr($title); ?>"
                        name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
                        value="Y"
                        <?php echo $currentValue === 'Y' ? 'checked' : ''; ?>>
                </td>
                <td>
                    <label><?php esc_html_e('Form value', 'cf7-bitrix24-integration'); ?></label>
                    <br>
                    <small>
                        <?php esc_html_e('Support values', 'cf7-bitrix24-integration'); ?>: yes/no, on/off, 1/0, true/false
                    </small>
                    <input id="__<?php echo esc_attr($name); ?>-populate"
                        type="text"
                        class="large-text code"
                        title="<?php echo esc_attr($title); ?>"
                        name="<?php echo esc_attr($this->fieldNameStart . $name); ?>-populate]"
                        value="<?php echo esc_attr($currentValuePopulate); ?>">
                </td>
            </tr>
        </table>
        <?php
    }

    public function textareaField($name, $title, $currentValue, $placeholder = '', $description = '')
    {
        ?>
        <textarea
            id="__<?php echo esc_attr($name); ?>"
            class="large-text code"
            placeholder="<?php echo $placeholder ? esc_attr($placeholder) : ''; ?>"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            rows="4"><?php echo esc_attr($currentValue); ?></textarea>
        <?php

        echo $description ? '<p class="description">' . esc_html($description) . '</p>' : '';
    }

    private function getStatusListByType($type)
    {
        $statusList = get_option(Bootstrap::STATUS_LIST_KEY);

        $returnList = [];

        if ($type == 'DEAL_STAGE') {
            // Default pipeline
            foreach ($statusList as $status) {
                if ($status['ENTITY_ID'] === $type) {
                    $returnList[$status['STATUS_ID']] = esc_html__('Default pipeline', 'cf7-bitrix24-integration')
                        . ' - '
                        . $status['NAME'];
                }
            }

            $pipelines = get_option(Bootstrap::DEAL_CATEGORY_LIST_KEY, []);

            if ($pipelines) {
                foreach ($pipelines as $pipeline) {
                    foreach ($statusList as $status) {
                        if ($status['ENTITY_ID'] === $type . '_' . $pipeline['ID']) {
                            $returnList[$status['STATUS_ID']] = $pipeline['NAME'] . ' - ' . $status['NAME'];
                        }
                    }
                }
            }
        } else {
            foreach ($statusList as $status) {
                if ($status['ENTITY_ID'] === $type) {
                    $returnList[$status['STATUS_ID']] = $status['NAME'];
                }
            }
        }

        return $returnList;
    }
}
