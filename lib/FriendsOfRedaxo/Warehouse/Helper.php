<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_sql;
use rex_backup;
use rex_path;
use rex_logger;

class Helper {
    
    private $query;
    private $tree = [];
    private $maxlev = 0;
    
    public function sql_full_tree($parent_id = 0, $level = 0, $depth = 0) {

        $return = array();
        $level++;

        $sql = rex_sql::factory();
        $sql->setQuery(str_replace('|parent_id|', $parent_id, $this->query));

        $res = $sql->getArray();

        if ($res != NULL) {

            foreach ($res as $t) {
                $v = str_repeat('- ', $level - 1) . $t['name'];
                $k = $t['id'];
                $lev = $t;
                $lev['name'] = $v;
                $lev['name_raw'] = $t['name'];
                if ($this->maxlev > 0 && $level < $this->maxlev && $next_lev = $this->sql_full_tree($k, $level, $depth)) {
                    $lev['level'] = $next_lev;                    
                }
                $return[] = $lev;
            }
        }
        return $return;
    }
    
    public function set_query($query) {
        $this->query = $query;
    }
    
    public function set_maxlev($maxlev) {
        $this->maxlev = $maxlev;
    }
    
    public static function restore_installation () {
        rex_backup::importDb(rex_path::base('redaxo/data/addons/backup/base_installation.sql'));
        rex_logger::factory()->log(1,'Installation wieder hergestellt.');        
    }

    /**
     * Beispiel:
     *  $yf->setValidateField('customfunction',['ki_ort','warehouse_helper::validate_sub_values','anderer_kontoinhaber','Bitte füllen Sie alle markierten Felder aus.']);
     *  das Feld ki_ort soll nur geprüft werden, wenn im Feld "anderer_kontoinhaber" was drin steht
     * 
     * Beispiel 2:
     *  $yf->setValidateField('customfunction',['iban','warehouse_helper::validate_sub_values',['zahlungsweise_lastschrift','SEPA-Lastschrift'],'Bitte füllen Sie alle markierten Felder aus.']);
     *  Das Feld iban soll nur auf empty geprüft werden, wenn das Feld zahlungsweise_lastschfit = SEPA-Lastschrift enthält
     * 
     * @param type $label
     * @param type $value
     * @param type $params kann als String (nur Feldname) oder als Array (Feldname und Wert bei dem die Prüfung des label Feldes stattfindet) übergeben werden.
     * @param type $yf
     * @return boolean
     */
    
    public static function validate_sub_values ($label, $value, $params, $yf) {
        $compare = '';
        if (is_array($params)) {
            list($field,$compare) = $params;
        } else {
            $field = $params;
        }
        foreach ($yf->getObjects() as $o) {
            if ($o->getName() == $field) {
                $base_val = $o->getValue();
            }
            if ($o->getName() == $label) {
                $val = $o->getValue();
            }
        }
        if ($compare) {
            if ($compare != $base_val) {
                return false;
            }
        }
        if (!$base_val) {
            return false;
        }
        if (!$val) {
            return true;
        }
        return false;
        
    }
    
    public static function uk_format_text ($text) {
        $search = ['<ul>'];
        $replace = ['<ul class="uk-list uk-list-bullet">'];
        return str_replace($search,$replace,$text);
        
    }
    
    
}
