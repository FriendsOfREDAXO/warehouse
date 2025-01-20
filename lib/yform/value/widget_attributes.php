<?php

/**
 * yform.
 *
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 *
 * nach yform/values kopieren - kann direkt verwendet werden um Datensätze zu verbinden
 *
 *  ===================================================================================
 *  ======================== ! ! ! ! WICHTIGER HINWEIS ! ! ! ! ========================
 *  ===================================================================================
 *  Dieses Widget funktioniert nur im Zusammenhang mit einem weiteren Feld, welches die
 *  Auswahl zur Verfügung stellt!
 *
 *
 */

class rex_yform_value_widget_attributes extends rex_yform_value_abstract
{
    public function enterObject()
    {
        static $counter = 0;
        ++$counter;

        $values = $this->fetchValues();

//        dump($values);

        $values = $this->rearange_widget_values($values);

        if (!$values) {
            $values = $this->load_values();
        }

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = 'field_output';
            $name = $this->getFieldName();

            $attributes_for_article = $this->get_attributes_for_article();

            $formElements = [];

            // Ausgabe erstellen
            foreach ($attributes_for_article as $i=>$attr) {
                $n = [];
                $n['label'] = '<label for="widget_attributes_'.$i.'">' . $attr['name'];
                $n['label'] .=  $attr['unit'] ? " [".$attr['unit']."]" : '';
                $n['label'] .= '</label>';
                $n['note'] = $attr['notice'];

                // SELECT Feld - derzeit nur multiple - kein single
                if ($attr['type'] == 'SELECT') {
                    $sel = new rex_select();
                    $sel->setId('widget_attributes_'.$i);
                    $sel->setName($name."[$i][]");
//                    $sel->setSize(1);
                    $sel->setAttribute('class', 'form-control selectpicker');
                    $sel->setMultiple();
                    $sel->setSelected($values[$i]);
                    foreach (explode('|',$attr['values']) as $v) {
                        $_v = explode('=',$v);
                        $sel->addOption($_v[0], isset($_v[1]) ? $_v[1] : $_v[0]);
                    }
                    $n['field'] = $sel->get();
                } elseif ($attr['type'] == 'WIDGET') {
                // WIDGET
                // - Attribute value (z.B. gr47)
                // - Attribute label (z.B. Größe 47)
                // - Attribute Price (Mehr-/Minderkosten vom Hauptartikel)

                    $n['field'] = '';
                    $n['field'] .= '<input type="hidden" name="'.$name."[$i][widgettype]".'" value="WIDGET">';
                    $n['field'] .= '<table class="sortable table table-bordered">';
                    $n['field'] .= '<thead>';
                    $n['field'] .= '<tr><th>Nr.</th><th>Label</th><th>'.($attr['pricemode'] == 'absolute' ? 'Preis' : 'Mehr-/Minderpreis').'</th><th>Lieferbar</th><th></th></tr>';
                    $n['field'] .= '</thead><tbody>';

//                    dump($values);

                    if (isset($values[$i]) && is_array($values[$i])) {
                        foreach ($values[$i] as $v) {
                            $n['field'] .= '<tr><td>'
                                    . '<input type="text" id="widget_attributes_'.$i.'" name="'.$name."[$i][value][]".'" value="' . $v['value'] . '" />'
                                    . '</td><td>'
                                    . '<input name="'.$name."[$i][label][]".'" value="' . $v['label'] . '" /></td><td><input name="'.$name."[$i][price][]".'" value="' . $v['price'] . '" /></td><td>'
                                . '<select name="'.$name."[$i][available][]".'">'
                                    . '<option value="1" ' . ($v['available'] == "1" ? ' selected="selected" ' : '') .' >lieferbar</option>'
                                    . '<option value="0" ' . ($v['available'] == "0" ? ' selected="selected" ' : '') .' >nicht lieferbar</option>'
                                . '</select>'
                                . '</td><td><a class="row_add rex-icon rex-icon-add"></a> <a class="rex-icon rex-icon-delete row_del"></a></td></tr>';
                        }
                    }

                    if (!isset($values[$i]) || !$values[$i]) {
                        $n['field'] .= '<tr><td>'
                                . '<input type="text" id="widget_attributes_'.$i.'" name="'.$name."[$i][value][]".'" />'
                                . '</td><td>'
                                . '<input name="'.$name."[$i][label][]".'" /></td><td><input name="'.$name."[$i][price][]".'" /></td><td>'
                            . '<select name="'.$name."[$i][available][]".'">'
                                . '<option value="1">lieferbar</option>'
                                . '<option value="0">nicht lieferbar</option>'
                                . '</select>'
                            . '</td><td><a class="row_add rex-icon rex-icon-add"></a> <a class="rex-icon rex-icon-delete row_del"></a></td></tr>';
                    }

                    $n['field'] .= '</tbody></table>';


//                    $n['field'] = '<textarea class="form-control" type="text" id="widget_attributes_'.$i.'" name="'.$name."[$i]".'">'.$values[$i].'</textarea>';


                // TEXT Input Feld
                } else {
                    $n['field'] = '<input class="form-control" type="text" id="widget_attributes_'.$i.'" name="'.$name."[$i]".'" value="' . $values[$i] . '" />';
                }
                $formElements[] = $n;
            }
            $fragment = new rex_fragment();
            $fragment->setVar('elements', $formElements, false);



            $this->params['form_output'][$this->getId()] = $fragment->parse('core/form/form.php');


        }

//        $this->params['value_pool']['email'][$this->getElement(1)] = $this->getValue();
//        $this->params['value_pool']['sql'][$this->getElement(1)] = json_encode($this->getValue());

        if ($this->params['send'] && $this->fetchValues()) {
            $this->save_values();
        }



    }

    /**
     * Liest die Werte aus $this->getValue und wendet htmlspecialchars an
     *
     * @return array
     */
    private function fetchValues() {
        $value = $this->getValue();
        if (is_array($value)) {
            $values = [];
            foreach ($value as $k=>$v) {
                if (is_string($v)) {
                    $values[$k] = htmlspecialchars($v);
                }
                if (is_array($v)) {
                    foreach ($v as $_k=>$_v) {
                        if (is_array($_v)) {
                            foreach ($_v as $__k=>$__v) {
                                $v[$_k][$__k] = htmlspecialchars($__v);
                            }
                        } else {
                            $v[$_k] = htmlspecialchars($_v);
                        }
                    }
                    $values[$k] = $v;
                }
            }
            return $values;
        } else {
            return [];
        }
    }

    /**
     * Liest aus dem aktuellen Artikel die zugeordnete Attributgruppe und liest aus der Attributgruppe die Attribute
     *
     * @return array
     */
    private function get_attributes_for_article () {

        // passendes Feld mit dem Wert der Attributgruppe suchen
        foreach ($this->params['values'] as $v) {
            if ($v->name == 'attributegroup_id') {
                $attributegroup_id = $v->value;
                break;
            }
        }

        $qry = 'SELECT attributes FROM '.$this->getElement('table_attributegroups').' WHERE id = :id';
        $r1 = rex_sql::factory()->getArray($qry,['id'=>$attributegroup_id]);

        if (!$r1) return [];

        $qry = 'SELECT *, name_1 name FROM `'.$this->getElement('table_attributes').'` WHERE id = :id';

        $sql = rex_sql::factory();
        $r2 = [];

        foreach (explode(',',$r1[0]['attributes']) as $v) {
            $sql->setQuery($qry,['id'=>$v]);
            $r2[] = $sql->getArray()[0];
        }

        return $r2;
    }


    /**
     * Speichert die Werte in der Tabelle
     */
    private function save_values() {
        $main_id = $this->params['main_id'];
        $values = $this->fetchValues();
        $attributes_for_article = $this->get_attributes_for_article();
        $attribute_value_table = $this->elements['table_attributevalues'];


        // alle zum Artikel gehörenden löschen
        $sql = rex_sql::factory()->setTable($attribute_value_table);

        $sql->setWhere('article_id = :article_id',['article_id'=>$main_id])->delete();


        /*
        // Setzt auto_increment id zurück
        $sql = rex_sql::factory();
        $qry = "SET  @num := 0;
                UPDATE $attribute_value_table SET id = @num := (@num+1);
                ALTER TABLE $attribute_value_table AUTO_INCREMENT =1;";
        $sql->setQuery($qry);
        */

        foreach ($attributes_for_article as $k=>$attr) {
            $sql->setTable($this->elements['table_attributevalues']);
            $value = '';
            if (isset($values[$k]['widgettype']) && $values[$k]['widgettype'] == 'WIDGET') {
                $widget_values = $this->get_widget_values($values[$k]);
                foreach ($widget_values as $prio=>$_val) {
                    $sql->setTable($this->elements['table_attributevalues']);
                    $insert = [
                        'article_id'=>$main_id,
                        'attribute_id'=>$attr['id'],
                        'prio'=>$prio
                    ];
                    $insert = array_merge($insert,$_val);
                    $sql->setValues($insert);
                    $sql->insert();
                }
            } else {
                if (isset($values[$k])) {
                    $value = $values[$k];
                }
                if (is_array($value)) {
                    $value = implode(',',$value);
                }
                $insert = [
                    'article_id'=>$main_id,
                    'attribute_id'=>$attr['id'],
                    'value'=>trim($value)
                ];
                $sql->setValues($insert);
                $sql->insert();
            }
        }
//        exit;
    }


    private function get_widget_values ($values) {
        $vals = [];
        $k = 0;
        foreach ($values as $k=>$v) {
            if ($k == 'widgettype') continue;
            foreach ($v as $_k=>$_v) {
                $vals[$_k][$k] = $_v;
            }
        }
        return $vals;
    }


    /**
     * Lädt die gespeicherten Werte
     */
    private function load_values() {
//        return [];

        $main_id = $this->params['main_id'];
        $attributes_for_article = $this->get_attributes_for_article();
        $attribute_value_table = $this->elements['table_attributevalues'];
        $qry = "SELECT * FROM $attribute_value_table WHERE article_id = :article_id";
        $res = rex_sql::factory()
//                ->setDebug()
                ->getArray($qry,['article_id'=>$main_id]);
        $values = [];
        foreach ($attributes_for_article as $k=>$attr) {
            if ($attr['type'] == 'SELECT') {
                $values[$k] = explode(',',$res[$k]['value']);
            } elseif ($attr['type'] == 'WIDGET') {
                foreach ($res as $v) {
                    if ($v['attribute_id'] == $attr['id']) {
                        $values[$k][] = $v;
                    }
                }
            } else {
                $values[$k] = $res[$k]['value'];
            }
        }
        $this->setValue($values);
        return $values;
    }

    private function rearange_widget_values($values) {
        $vals = [];
        foreach ($values as $k=>$v) {
            if (isset($v['widgettype']) && $v['widgettype'] == 'WIDGET') {
                foreach ($v as $_k=>$_v) {
                    if (is_array($_v)) {
                        foreach ($_v as $__k=>$__v) {
                            $vals[$k][$__k][$_k] = $__v;
                        }
                    } else {
//                        $vals[$k][$_k] = $_v;
                    }
                }
            } else {
                $vals[$k] = $v;
            }
        }
        return $vals;
    }


    public function getDefinitions() : array
    {
        return [
            'type' => 'value',
            'name' => 'widget_attributes',
            'values' => [
                'name' => ['type' => 'name',   'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text',    'label' => rex_i18n::msg('yform_values_defaults_label')],
                'table_attributegroups' => ['type' => 'text',   'label' => rex_i18n::msg('yform_widget_attributes_tablegroups')],
                'table_attributes' => ['type' => 'text',   'label' => rex_i18n::msg('yform_widget_attributes_tableattributes')],
                'table_attributevalues' => ['type' => 'text',   'label' => rex_i18n::msg('yform_widget_attributes_tableattributevalues'),'notice'=>'Eine Notiz - funzt die?'],
                'field_name' => ['type' => 'text',    'label' => rex_i18n::msg('yform_widget_record_field_name')],
            ],
            'description' => rex_i18n::msg('yform_values_widget_record_description'),
            'formbuilder' => false,
            'dbtype' => 'none',
        ];
    }

    /**
     *
     * @param type $params
     */
    public static function getListValues($table, $field_name, $value) : array {
        $qry = "SELECT id, $field_name name FROM `$table` WHERE id = :id";

        $sql = rex_sql::factory();
        $res = [];
        foreach (explode(',',$value) as $val) {
            $sql->setQuery($qry,['id'=>$val]);
            $res[] = $sql->getArray()[0];
        }
        return $res;
    }

    public static function getListValue($params)
    {
        $table = $params['params']['field']['table'];
        $field_name = $params['params']['field']['field_name'];
        $value = $params['subject'];
        $qry = "SELECT id, $field_name name FROM `$table` WHERE id = :id";

        $sql = rex_sql::factory();
        $res = [];
        $out = [];
        foreach (explode(',',$value) as $val) {
            $sql->setQuery($qry,['id'=>$val]);
            $res = $sql->getArray()[0];
            $out[] = $res['name'].' [id='.$res['id'].']';

        }

        return implode('<br>',$out);
    }


    public static function getSearchField($params)
    {
        $params['searchForm']->setValueField('text', ['name' => $params['field']->getName(), 'label' => $params['field']->getLabel()]);
    }

    public static function getSearchFilter($params)
    {
        $sql = rex_sql::factory();
        $value = $params['value'];
        $field = $params['field']->getName();

        if ($value == '(empty)') {
            return ' (' . $sql->escapeIdentifier($field) . ' = "" or ' . $sql->escapeIdentifier($field) . ' IS NULL) ';
        }
        if ($value == '!(empty)') {
            return ' (' . $sql->escapeIdentifier($field) . ' <> "" and ' . $sql->escapeIdentifier($field) . ' IS NOT NULL) ';
        }

        $pos = strpos($value, '*');
        if ($pos !== false) {
            $value = str_replace('%', '\%', $value);
            $value = str_replace('*', '%', $value);
            return $sql->escapeIdentifier($field) . ' LIKE ' . $sql->escape($value);
        }
        return $sql->escapeIdentifier($field) . ' = ' . $sql->escape($value);
    }
}
