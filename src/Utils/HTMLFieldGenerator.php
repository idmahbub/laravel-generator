<?php

namespace InfyOm\Generator\Utils;

use InfyOm\Generator\Common\GeneratorField;

class HTMLFieldGenerator
{
    public static function generateHTML(GeneratorField $field, $templateType, $localized = false)
    {
        $fieldTemplate = '';

        $localized = ($localized) ? '_locale' : '';
        switch ($field->htmlType) {
            case 'text':
            case 'textarea':
            case 'date':
            case 'file':
            case 'email':
            case 'password':
            case 'number':
                $fieldTemplate = get_template('scaffold.fields.'.$field->htmlType.$localized, $templateType);
                break;
            case 'select':
            case 'enum':
                $fieldTemplate = get_template('scaffold.fields.select'.$localized, $templateType);
                $radioLabels = GeneratorFieldsInputUtil::prepareKeyValueArrFromLabelValueStr($field->htmlValues);

                $fieldTemplate = str_replace(
                    '$INPUT_ARR$',
                    GeneratorFieldsInputUtil::prepareKeyValueArrayStr($radioLabels),
                    $fieldTemplate
                );
                break;
            case 'checkbox':
                $fieldTemplate = get_template('scaffold.fields.checkbox'.$localized, $templateType);
                if (count($field->htmlValues) > 0) {
                    $checkboxValue = $field->htmlValues[0];
                } else {
                    $checkboxValue = 1;
                }
                $fieldTemplate = str_replace('$CHECKBOX_VALUE$', $checkboxValue, $fieldTemplate);
                break;
            case 'radio':
                $fieldTemplate = get_template('scaffold.fields.radio_group'.$localized, $templateType);
                $radioTemplate = get_template('scaffold.fields.radio'.$localized, $templateType);

                $radioLabels = GeneratorFieldsInputUtil::prepareKeyValueArrFromLabelValueStr($field->htmlValues);

                $radioButtons = [];
                foreach ($radioLabels as $label => $value) {
                    $radioButtonTemplate = str_replace('$LABEL$', $label, $radioTemplate);
                    $radioButtonTemplate = str_replace('$VALUE$', $value, $radioButtonTemplate);
                    $radioButtons[] = $radioButtonTemplate;
                }
                $fieldTemplate = str_replace('$RADIO_BUTTONS$', implode("\n", $radioButtons), $fieldTemplate);
                break;
            case 'toggle-switch':
                $fieldTemplate = get_template('scaffold.fields.toggle-switch'.$localized, $templateType);
                break;
        }

        return $fieldTemplate;
    }
    public static function generateCustomFieldHTML(GeneratorField $field, $templateType)
    {
        $fieldTemplate = '';

        switch ($field->htmlType) {
            case 'text':
            case 'textarea':
            case 'date':
            case 'file':
            case 'email':
            case 'password':
                $fieldTemplate = get_template('scaffold.custom_fields.' . $field->htmlType, $templateType);
                break;
            case 'number':
                $fieldTemplate = get_template('scaffold.custom_fields.' . $field->htmlType, $templateType);
                break;
            case 'select':
            case 'enum':
                if ($field->dbInput === 'hidden,mtm') {
                    $fieldTemplate = get_template('scaffold.custom_fields.selects', $templateType);
                } else {
                    $fieldTemplate = get_template('scaffold.custom_fields.select', $templateType);
                }
                if (starts_with($field->htmlValues[0], '$')) {
                    $fieldTemplate = str_replace(
                        '$INPUT_ARR$',
                        $field->htmlValues[0],
                        $fieldTemplate
                    );
                    $fieldTemplate = str_replace(
                        '$INPUT_ARR_SELECTED$',
                        str_plural($field->htmlValues[0]) . 'Selected',
                        $fieldTemplate
                    );
                } else {
                    $radioLabels = GeneratorFieldsInputUtil::prepareKeyValueArrFromLabelValueStr($field->htmlValues);

                    $fieldTemplate = str_replace(
                        '$INPUT_ARR$',
                        GeneratorFieldsInputUtil::prepareKeyValueArrayStr($radioLabels),
                        $fieldTemplate
                    );
                }
                break;
            case 'checkbox':
                $fieldTemplate = get_template('scaffold.custom_fields.checkbox', $templateType);
                if (count($field->htmlValues) > 0) {
                    $checkboxValue = $field->htmlValues[0];
                } else {
                    $checkboxValue = 1;
                }
                $fieldTemplate = str_replace('$CHECKBOX_VALUE$', $checkboxValue, $fieldTemplate);
                break;
            case 'radio':
                $fieldTemplate = get_template('scaffold.custom_fields.radio_group', $templateType);
                $radioTemplate = get_template('scaffold.custom_fields.radio', $templateType);

                $radioLabels = GeneratorFieldsInputUtil::prepareKeyValueArrFromLabelValueStr($field->htmlValues);

                $radioButtons = [];
                foreach ($radioLabels as $label => $value) {
                    $radioButtonTemplate = str_replace('$LABEL$', $label, $radioTemplate);
                    $radioButtonTemplate = str_replace('$VALUE$', $value, $radioButtonTemplate);
                    $radioButtonTemplate = str_replace('$FIELD_VALUE$', in_array($value,$field->validations)? '1': 'null', $radioButtonTemplate);
                    $radioButtons[] = $radioButtonTemplate;
                }
                $fieldTemplate = str_replace('$RADIO_BUTTONS$', implode("\n", $radioButtons), $fieldTemplate);
                break;
            case 'boolean':
                $fieldTemplate = get_template('scaffold.custom_fields.boolean', $templateType);
                break;
        }

        return $fieldTemplate;
    }
}
