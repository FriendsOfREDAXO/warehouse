<?php

use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Warehouse;

class rex_yform_value_warehouse_payment_options extends rex_yform_value_abstract
{
    public function enterObject(): void
    {
        $payment_options = Payment::getPaymentOptions();
        
        // Transform simple array into structured array with backend-defined labels, notices, and images
        $options = [];
        foreach ($payment_options as $key => $label) {
            $options[$key] = [
                'label' => Warehouse::getLabel('paymentoptions_' . $key) ?: rex_i18n::msg($label),
                'description' => Warehouse::getLabel('paymentoptions_' . $key . '_notice') ?: '',
                'logo' => Warehouse::getConfig('label_paymentoptions_' . $key . '_image') ?: '',
            ];
        }

        if (!array_key_exists($this->getValue(), $options)) {
            $this->setValue('');
        }

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('value.warehouse_payment_options.tpl.php', compact('options'));
        }

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
        $this->params['value_pool']['email'][$this->getName() . '_LABEL'] = self::getLabelForValue($payment_options, (string) $this->getValue());

        if ($this->saveInDb()) {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }
    }

    public function getDescription(): string
    {
        return 'radio|name|label|[notice]|[no_db]';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'warehouse_payment_options',
            'values' => [
                'name' => ['type' => 'name',   'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text',    'label' => rex_i18n::msg('yform_values_defaults_label')],
                'notice' => ['type' => 'text',    'label' => rex_i18n::msg('yform_values_defaults_notice')],
                'no_db' => ['type' => 'no_db',   'label' => rex_i18n::msg('yform_values_defaults_table'), 'default' => 0],
            ],
            'deprecated' => false,
            'description' => rex_i18n::msg('yform_values_radio_description'),
            'db_type' => ['int', 'text'],
        ];
    }

    /**
     * @param array<string, mixed> $params
     */
    public static function getListValue(array $params): string
    {
        $return = [];

        $new_select = new self();
        $values = $new_select->getArrayFromString($params['params']['field']['options']);

        foreach (explode(',', $params['value']) as $k) {
            if (isset($values[$k])) {
                $return[] = rex_i18n::translate($values[$k]);
            }
        }

        return implode('<br />', $return);
    }

    /**
     * @param array<string, mixed> $params
     */
    public static function getSearchField(array $params): void
    {
        $options = [];
        $options['(empty)'] = '(empty)';
        $options['!(empty)'] = '!(empty)';

        $new_select = new self();
        $options += $new_select->getArrayFromString($params['field']['options']);

        $params['searchForm']->setValueField(
            'select',
            [
                'name' => $params['field']->getName(),
                'label' => $params['field']->getLabel(),
                'options' => $options,
                'multiple' => 1,
                'size' => 5,
            ],
        );
    }

    /**
     * @param array<string, mixed> $params
     */
    public static function getSearchFilter(array $params): ?string
    {
        $sql = rex_sql::factory();

        $field = $params['field']->getName();
        $values = (array) $params['value'];

        $where = [];
        foreach ($values as $value) {
            switch ($value) {
                case '(empty)':
                    $where[] = ' ' . $sql->escapeIdentifier($field) . ' = ""';
                    break;
                case '!(empty)':
                    $where[] = ' ' . $sql->escapeIdentifier($field) . ' != ""';
                    break;
                default:
                    $where[] = ' ( FIND_IN_SET( ' . $sql->escape($value) . ', ' . $sql->escapeIdentifier($field) . ') )';
                    break;
            }
        }

        if (count($where) > 0) {
            return ' ( ' . implode(' or ', $where) . ' )';
        }
        return null;
    }

    /**
     * @param array<string, string> $options
     */
    public function getLabelForValue(array $options = [], ?string $selected = null): string
    {
        if (is_null($selected)) {
            $selected = $this->getValue();
        }

        if (isset($options[$selected])) {
            return rex_i18n::translate($options[$selected]);
        }

        return '';
    }
}
