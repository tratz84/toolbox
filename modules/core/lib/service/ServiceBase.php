<?php


namespace core\service;


use core\container\ObjectHookable;
use core\db\DatabaseTransactionObject;
use core\forms\lists\ListResponse;

class ServiceBase implements DatabaseTransactionObject, ObjectHookable {
    
    protected $oc;
    
    public function __construct() {
        
    }
    
    
    public function setObjectContainer($oc) { $this->oc = $oc; }
    public function getObjectContainer() { return $this->oc; }
    
    
    protected function daoSearch($daoClass, $opts, $fields, $start=0, $limit=null) {
        if ($limit === null) {
            $limit = \core\Context::getInstance()->getPageSize();
        }
            
        $dao = new $daoClass();
        
        $cursor = $dao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, $fields);
        
        return $r;
    }
    
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

