<?php

namespace codegen\form;


use core\forms\BaseForm;
use core\db\DatabaseHandler;

class DAOTableSelectorForm extends BaseForm {
    
    
    public function __construct(\codegen\CodegenModuleSettings $cms) {
        parent::__construct();
        
        $dbh = object_container_get(DatabaseHandler::class);
        
        $resourceNames = $dbh->getResourceNames();
        
        $checkedWidgets = array();
        $uncheckedWidgets = array();
        
        foreach($resourceNames as $rn) {
            $result = queryListAsArray($rn, 'show tables');
            $tables = array();
            foreach($result as $r) {
                $tables[] = $r[0];
            }
            
            $selected_daotables = $cms->getVar('daotables', array());
            
            // add first selected tables
            foreach($tables as $t) {
                if ($this->daotable_in_array($t, $selected_daotables)) {
                    $chk = new \core\forms\CheckboxField('tbl_'.$t, '', $rn.' - '.$t);
                    $chk->setField('resource_name', $rn);
                    $chk->setField('table_name', $t);
                    $chk->setValue('1');
                    $checkedWidgets[] = $chk;
                }
            }
            
            // add other tables
            foreach($tables as $t) {
                if ($this->daotable_in_array($t, $selected_daotables) == false) {
                    $chk = new \core\forms\CheckboxField('tbl_'.$t, '', $rn.' - '.$t);
                    $chk->setField('resource_name', $rn);
                    $chk->setField('table_name', $t);
                    
                    $uncheckedWidgets[] = $chk;
                }
            }
        }
        
        $widgets = array_merge($checkedWidgets, $uncheckedWidgets);
        foreach($widgets as $w) {
            $this->addWidget( $w );
        }
    }
    
    protected function daotable_in_array($table_name, $daotables) {
        foreach($daotables as $dt) {
            if (is_string($dt)) {
                if ($dt == $table_name) return true;
                continue;
            }
            if ($table_name == $dt['table_name'])
                return true;
        }
        return false;
    }
    
    
}
