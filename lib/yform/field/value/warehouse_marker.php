<?php

class rex_yform_value_warehouse_marker extends rex_yform_value_abstract
{
    public function enterObject(): void
    {

        $marker_value = $this->getValue();
        if(!empty($marker_value)) {
            $marker_value = (string) "Y";
        }

        $this->setValue($marker_value);

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('value.checkbox.tpl.php');
        }

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();

        if ($this->saveInDB()) {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }
    }

    public function getDescription(): string
    {
        return 'warehouse_marker|fieldname|label|notice';
    }

    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'warehouse_marker',
            'values' => [
                'name' => ['type' => 'name',   'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text',    'label' => rex_i18n::msg('yform_values_defaults_label')],
                'notice' => ['type' => 'text',    'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
            'description' => rex_i18n::msg('yform_values_warehouse_marker_description'),
            'db_type' => ['char(1)'],
        ];
    }

    public static function getListValue(array $params): string
    {
        if($params['value'] !== "") {
            return "⭐";
        }
        return "";
    }
}
