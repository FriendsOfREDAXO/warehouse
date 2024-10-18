<?php

/**
 * yform.
 *
 * 
 * 
 * <td class="rex-table-action"><a href="index.php?page=yform/manager/data_edit&amp;table_name=rex_warehouse_article_variants&amp;rex_yform_manager_popup=0&amp;data_id=1&amp;func=edit&amp;start=&amp;sort=&amp;sorttype=&amp;list=519d1177"><i class="rex-icon rex-icon-edit"></i> editieren</a></td>
 * 
 * <a href="index.php?page=mediapool/media" class="rex-popup" onclick="openMediaPool(); return false;"><i class="rex-icon rex-icon-media"></i> Medienpool</a>
 * 
 * <a href="javascript:void(0);" class="btn btn-popup" onclick="yform_manager_openDatalist(1, 'name_0', 'index.php?page=yform/manager/data_edit&amp;table_name=rex_warehouse_attributes','1');return false;" title="Datensatz auswählen"><i class="rex-icon rex-icon-add"></i></a>
 * 
 * 
 */
class rex_yform_value_sub_table extends rex_yform_value_abstract
{
    public function enterObject()
    {
        $items = $this->fetch_subrecords();
        $out = '';
        if ($this->elements['label']) {
            $out .= "<h3>{$this->elements['label']}</h3>";
        }
        $out .= '<table class="table table-bordered"><tr>';
        $rows = [];
        $params = $_GET;
        $params['page'] = 'yform/manager/data_edit';
        $params['table_name'] = $this->elements['table'];
        $params['func'] = 'edit';
        $params['rex_yform_manager_popup'] = 1;
        foreach ($items as $item) {
            $params['data_id'] = $item['id'];
            $link = 'index.php?'.rex_string::buildQuery($params, '&');
            $cols = implode('</td><td>',$item);
            $cols .= "</td><td><a href=\"javascript:void(0);\" onclick=\"yform_manager_openDatalist(1, 'name_1', '$link','1');return false;\"><i class=\"rex-icon rex-icon-edit\"></i> bearbeiten</a>";
            $cols = '<td>'.$cols.'</td>';
            $rows[] = $cols;            
        }
        $out .= implode('</tr><tr>',$rows);
        $out .= '</tr></table>';        
        $this->params['form_output'][$this->getId()] = $out;
    }
    
    
    private function fetch_subrecords () {
        $sql = rex_sql::factory()
//                ->setDebug()
                ;        
        $qry = 'SELECT id,'.$this->elements['columns'].' FROM '.$this->elements['table'];
        $qry .= ' WHERE '.$this->elements['parent_id'].' = :parent_id';
        if ($this->elements['subprio']) {
            $qry .= ' ORDER BY '.$this->elements['subprio'];
        }
        $sql->setQuery($qry,['parent_id'=>$this->params['main_id']]);
        $res = $sql->getArray();
        return $res;
    }

    public function getDefinitions() : array
    {
        return [
            'type'        => 'value',
            'name'        => 'sub_table',
            'values'      => [
                'name'    => ['type' => 'name', 'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label'   => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'table'   => ['type' => 'text', 'label' => 'Tabelle'],
                'parent_id' => ['type' => 'text', 'label' => 'Referenzfeld mit der Parent Id'],
                'columns' => ['type' => 'text', 'label' => 'Felder, Kommasepariert'],
                'subprio'    => ['type' => 'text', 'label' => 'Prio Feld'],
                'notice'  => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
            'description' => rex_i18n::msg('yform_values_be_table_description'),
            'formbuilder' => false,
            'dbtype'      => 'none',
        ];
    }
}
