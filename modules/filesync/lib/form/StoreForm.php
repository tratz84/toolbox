<?php

namespace filesync\form;


use core\forms\BaseForm;
use core\forms\DynamicSelectField;
use core\forms\HiddenField;
use core\forms\TextField;
use filesync\model\Store;
use core\forms\SelectField;
use core\forms\validator\NotEmptyValidator;

class StoreForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('store_id');
        
        $this->addWidget(new HiddenField('store_id'));
        
        $mapTypes = array();
        $mapTypes[''] = t('Make your choice');
        $mapTypes['archive'] = t('Archive');
        $mapTypes['backup']  = t('Backup');
        $mapTypes['share']   = t('Share');
        
        $this->addWidget(new SelectField('store_type', '', $mapTypes, t('Store type')));
        
        $this->addWidget(new TextField('store_name', '', t('Name')));
        
        $this->addValidator('store_type', new NotEmptyValidator());
        $this->addValidator('store_name', new NotEmptyValidator());
    }
    
    
    public function bind($obj) {
        parent::bind( $obj );
        
        if (is_a($obj, Store::class)) {
            if (!$obj->isNew()) {
                $this->removeWidget('store_type');
            }
        }
        
    }
    
}

