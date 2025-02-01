<?php

/**
 * yform.
 *
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */
class rex_yform_value_select_sql_tree extends rex_yform_value_abstract
{

    public static $getListValues = [];
    private $query;

    public function enterObject()
    {
        $multiple = $this->getElement('multiple') == 1;

        // ----- query
        $this->query = $this->getElement('query');
        
        $res = $this->sqlTree(0, 0);
        $options = [];
        foreach ($res as $v) {
            $options[$v['id']] = $v['name'];
        }
        $option_names = $options;
        
        if ($multiple) {
            $size = (int) $this->getElement('size');
            if ($size < 2) {
                $size = count($options);
            }

            $values = $this->getValue();
            if (!is_array($values)) {
                $values = explode(',', $values ?? "");
            }

            $real_values = [];
            foreach ($values as $value) {
                if (array_key_exists($value, $options)) {
                    $real_values[] = $value;
                }
            }

            $this->setValue($real_values);
        } else {
            $size = 1;

            if ($this->getElement('empty_option') == 1) {
                $options = ['0' => (string) $this->getElement('empty_value')] + $options;
            }

            $default = null;
            if (array_key_exists((string) $this->getElement('default'), $options)) {
                $default = $this->getElement('default');
            }
            $value = (string) $this->getValue();

            if (!array_key_exists($value, $options)) {
                if ($default or $default === '0') {
                    $this->setValue([$default]);
                } else {
                    reset($options);
                    $this->setValue([key($options)]);
                }
            } else {
                $this->setValue([$value]);
            }
        }

        // ---------- rex_yform_set
        if (isset($this->params['rex_yform_set'][$this->getName()]) && !is_array($this->params['rex_yform_set'][$this->getName()])) {
            $value = $this->params['rex_yform_set'][$this->getName()];
            $values = [];
            if (array_key_exists($value, $options)) {
                $values[] = $value;
            }
            $this->setValue($values);
            $this->setElement('disabled', true);
        }
        // ----------

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('value.select.tpl.php', compact('options', 'multiple', 'size'));
        }

        $this->setValue(implode(',', $this->getValue()));

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
        $this->params['value_pool']['email'][$this->getName() . '_NAME'] = isset($options[$this->getValue()]) ? $options[$this->getValue()] : null;

        if ($this->getElement('no_db') != 'no_db') {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }
    }

    public function getDescription() : string
    {
        return 'select_sql_tree|name|label| select id,name from table order by name | [defaultvalue] | [no_db] |1/0 Leeroption|Leeroptionstext|1/0 Multiple Feld|selectsize';
    }

    public function getDefinitions() : array
    {
        return [
            'type' => 'value',
            'name' => 'select_sql_tree',
            'values' => [
                'name' => ['type' => 'name', 'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'query' => [
                    'type' => 'text', 'label' => 'Query mit "select id, name from .." es müssen id und name übergeben werden, ggf. "SELECT id, name_1 name FROM rex_warehouse_categories WHERE parent_id = |parent_id|"',
                    'notice' => '|parent_id| ist ein Platzhalter, der intern durch die jeweilige Parent-id ersetzt wird. Das Feld "parent_id" muss vorhanden sein. Die erste Ebene muss den Wert 0 haben.'
                    ],
                'default' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_select_sql_default')],
                'no_db' => ['type' => 'no_db', 'label' => rex_i18n::msg('yform_values_defaults_table'), 'default' => 0],
                'empty_option' => ['type' => 'boolean', 'label' => rex_i18n::msg('yform_values_select_sql_empty_option')],
                'empty_value' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_select_sql_empty_value')],
                'multiple' => ['type' => 'boolean', 'label' => rex_i18n::msg('yform_values_select_sql_multiple')],
                'size' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_select_sql_size')],
                'attributes' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_attributes'), 'notice' => rex_i18n::msg('yform_values_defaults_attributes_notice')],
                'notice' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
            'description' => rex_i18n::msg('yform_values_select_sql_description'),
            'dbtype' => 'text',
        ];
    }

    public static function getListValue($params) : string
    {
        $return = [];

        $query = $params['params']['field']['query'];
        $query_params = [];
        $pos = mb_strrpos(mb_strtoupper($query), 'ORDER BY ');
        if ($pos !== false) {
            $query = mb_substr($query, 0, $pos);
        }

        $pos = mb_strrpos(mb_strtoupper($query), 'LIMIT ');
        if ($pos !== false) {
            $query = mb_substr($query, 0, $pos);
        }

        $pos = mb_strrpos(mb_strtoupper($query), 'WHERE ');
        if ($pos !== false) {
            $query = mb_substr($query, 0, $pos);
        }

        $multiple = (isset($params['params']['field']['multiple'])) ? (int) $params['params']['field']['multiple'] : 0;
        if ($multiple != 1) {
            $where = ' `id` = ?';
            $query_params[] = $params['value'];
        } else {
            $where = ' FIND_IN_SET(`id`, ?)';
            $query_params[] = $params['value'];
        }

        $pos = mb_strrpos(mb_strtoupper($query), 'WHERE ');
        if ($pos !== false) {
            $query = mb_substr($query, 0, $pos) . ' WHERE ' . $where . ' AND ' . mb_substr($query, $pos + strlen('WHERE '));
        } else {
            $query .= ' WHERE ' . $where;
        }

        $db = rex_sql::factory();
        $db_array = $db->getArray($query, $query_params);

        foreach ($db_array as $entry) {
            $return[] = $entry['name'];
        }

        if (count($return) == 0 && $params['value'] != '' && $params['value'] != '0') {
            $return[] = $params['value'];
        }

        return implode('<br />', $return);
    }

    public static function getSearchField($params)
    {
        //        dump($params);
        $options = [];
        $options['(empty)'] = '(empty)';
        $options['!(empty)'] = '!(empty)';

        $options_sql = rex_sql::factory();
        //        $options_sql->setDebug(true);
        $qry = explode('WHERE', $params['field']['query']);
        //        $options_sql->setQuery($params['field']['query']);
        $options_sql->setQuery($qry[0]);

        foreach ($options_sql->getArray() as $t) {
            $options[$t['id']] = $t['name'];
        }

        $params['searchForm']->setValueField(
            'select',
            [
            'name' => $params['field']->getName(),
            'label' => $params['field']->getLabel(),
            'options' => $options,
            'multiple' => 1,
            'size' => 5,
            'notice' => rex_i18n::msg('yform_search_defaults_select_notice'),
                ]
        );
    }

    public static function getSearchFilter($params)
    {
        $sql = rex_sql::factory();
        $field = $params['field']->getName();
        $values = (array) $params['value'];

        $multiple = $params['field']->getElement('multiple') == 1;

        $where = [];
        foreach ($values as $value) {
            switch ($value) {
                case '(empty)':
                    $where[] = $sql->escapeIdentifier($field) . ' = ""';
                    break;
                case '!(empty)':
                    $where[] = $sql->escapeIdentifier($field) . ' != ""';
                    break;
                default:
                    if ($multiple) {
                        $where[] = ' ( FIND_IN_SET( ' . $sql->escape($value) . ', ' . $sql->escapeIdentifier($field) . ') )';
                    } else {
                        $where[] = ' ( ' . $sql->escape($value) . ' = ' . $sql->escapeIdentifier($field) . ' )';
                    }
                    break;
            }
        }

        if (count($where) > 0) {
            return ' ( ' . implode(' or ', $where) . ' )';
        }
    }

    public function sqlTree($parent_id = 0, $level = 0, $depth = 0)
    {

        $return = array();
        $level++;

        $sql = rex_sql::factory();
        //        $sql->setDebug(1);
        $sql->setQuery(str_replace('|parent_id|', $parent_id, $this->query));

        $res = $sql->getArray();
        
        if ($res != null) {

            foreach ($res as $t) {
                $v = str_repeat('- ', $level - 1) . $t['name'];
                $k = $t['id'];
                
                $return[] = array_merge($t, array('id' => $k, 'name' => $v, 'name_raw' => $t['name']));
                /*

                if (isset($t['image'])) {
                    $return[] = array('id' => $k, 'name' => $v, 'name_raw' => $t['name'], 'image'=>$t['image']);
                } else {
                    $return[] = array('id' => $k, 'name' => $v, 'name_raw' => $t['name']);
                }
                 *
                 */
                if ($depth == 0 || $level < $depth) {
                    $return = array_merge($return, $this->sqlTree($k, $level, $depth));
                }
            }
        }
        
        return $return;
    }
    
    public function set_query($query)
    {
        $this->query = $query;
    }
    

}
