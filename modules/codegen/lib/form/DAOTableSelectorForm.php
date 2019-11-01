<?php

namespace codegen\form;


use core\forms\BaseForm;

class DAOTableSelectorForm extends BaseForm {
    
    
    public function __construct(\codegen\CodegenModuleSettings $cms) {
        parent::__construct();
        
        $result = queryListAsArray('default', 'show tables');
        $tables = array();
        foreach($result as $r) {
            $tables[] = $r[0];
        }
        
        $selected_daotables = $cms->getVar('daotables', array());
        
        // add first selected tables
        foreach($tables as $t) {
            if (in_array($t, $selected_daotables)) {
                $chk = new \core\forms\CheckboxField('tbl_'.$t, '', $t);
                $chk->setField('tablename', $t);
                $chk->setValue('1');
                $this->addWidget( $chk );
            }
        }
        
        // add other tables
        foreach($tables as $t) {
            if (in_array($t, $selected_daotables) == false) {
                $chk = new \core\forms\CheckboxField('tbl_'.$t, '', $t);
                $chk->setField('tablename', $t);
                $this->addWidget( $chk );
            }
        }
        
        
    }
    
    
}
