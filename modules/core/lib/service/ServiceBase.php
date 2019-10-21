<?php


namespace core\service;


use core\db\DatabaseTransactionObject;
use core\container\ObjectHookable;

class ServiceBase implements DatabaseTransactionObject, ObjectHookable {
    
    protected $oc;
    
    public function __construct() {
        
    }
    
    
    public function setObjectContainer($oc) { $this->oc = $oc; }
    public function getObjectContainer() { return $this->oc; }
    
    
    public function saveForm($form, $objectClassName, $fields, $valuePk=null) {
        $obj = new $objectClassName();
        
        if ($valuePk == null)
            $valuePk = $form->getWidgetValue($obj->getPrimaryKey());
        
        if ($valuePk) {
            $obj->setField($obj->getPrimaryKey(), $valuePk);
            $obj->read();
        }
        
        $form->fill($obj, $fields);
        
        $r = $obj->save();
        
        
        if ($r) {
            $w = $form->getWidget( $obj->getPrimaryKey() );
            if ($w) {
                $w->setValue( $obj->getField($obj->getPrimaryKey()) );
            }
        }
        
        return $r;
    }
    
}

