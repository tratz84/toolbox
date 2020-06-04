<?php


namespace core\service;


use base\forms\FormChangesHtml;
use base\util\ActivityUtil;
use core\db\DBObject;
use core\exception\DatabaseException;
use core\forms\HiddenField;
use core\forms\lists\ListResponse;

class FormDbMapper {
    
    protected $formClass;
    protected $daoClass;
    protected $dbClass;
    protected $dbClassBaseName;
    
    protected $relationMTON = array();
    protected $relationMTO1 = array();
    
    protected $logRefObject = null;
    protected $logCreatedCode = null;
    protected $logUpdatedCode = null;
    protected $logDeletedCode = null;
    protected $logCreatedText = null;
    protected $logUpdatedText = null;
    protected $logDeletedText = null;
    
    protected $publicFields = array();
    
    public function __construct( $formClass, $daoClass ) {
        $this->formClass = $formClass;
        $this->daoClass = $daoClass;
        
        // fetch DBObject-class
        $daoObject = object_container_create( $daoClass );
        $this->dbClass = $daoObject->getObjectName();
        $this->dbClassBaseName = substr($this->dbClass, strrpos($this->dbClass, '\\')+1);
        
        
        // default to all fields in form
        $objForm = new $this->formClass();
        foreach($objForm->getWidgetsRecursive() as $w) {
            if ($w->getName()) {
                $this->publicFields[] = $w->getName();
            }
        }
        
    }
    
    public function getForm() { return $this->form; }
    public function setForm($f) { $this->form = $f; }
    
    public function getDaoClass() { return $this->daoClass; }
    public function setDaoClass($class) { $this->daoClass = $class; }
    
    public function getPublicFields() { return $this->publicFields; }
    public function setPublicFields($fields) { $this->publicFields = $fields; }
    
    
    public function addMTON($daoLink, $daoObject, $name=null, $opts=array()) {
        if ($name == null) {
            $name = lcfirst( substr( $daoObject, strrpos($daoObject, '\\')+1, -3 ) ) . 'List';
        }
        
        $this->relationMTON[] = [
            'daoLink'   => $daoLink,
            'daoObject' => $daoObject,
            'name'      => $name,
            'opts'      => $opts
        ];
    }
    
    public function addMTO1($daoObject, $name, $opts=array()) {
        $this->relationMTO1[] = [
            'daoObject' => $daoObject,
            'name'      => $name,
            'opts'      => $opts
        ];
    }
    
    
    public function setLogRefObject( $str ) { $this->logRefObject = $str; }
    public function getLogRefObject() {
        if ($this->logRefObject)
            return $this->logRefObject;
        else
            return $this->dbClass;
    }
    
    public function setLogCreatedCode( $str ) { $this->logCreatedCode = $str; }
    public function getLogCreatedCode() {
        if ($this->logCreatedCode)
            return $this->logCreatedCode;
        else
            return 'created';
    }
    
    public function setLogUpdatedCode( $str ) { $this->logUpdatedCode = $str; }
    public function getLogUpdatedCode() {
        if ($this->logUpdatedCode)
            return $this->logUpdatedCode;
        else
            return 'updated';
    }
    
    public function setLogDeletedCode( $str ) { $this->logDeletedCode = $str; }
    public function getLogDeletedCode() {
        if ($this->logDeletedCode)
            return $this->logDeletedCode;
        else
            return 'deleted';
    }
    
    public function setLogCreatedText( $str ) { $this->logCreatedText = $str; }
    public function getLogCreatedText() {
        if ($this->logCreatedText)
            return $this->logCreatedText;
        else
            return $this->dbClassBaseName . ' created';
    }
    
    public function setLogUpdatedText( $str ) { $this->logUpdatedText = $str; }
    public function getLogUpdatedText() {
        if ($this->logUpdatedText)
            return $this->logUpdatedText;
        else
            return $this->dbClassBaseName . ' updated';
    }
    
    public function setLogDeletedText( $str ) { $this->logDeletedText = $str; }
    public function getLogDeletedText() {
        if ($this->logDeletedText)
            return $this->logDeletedText;
        else
            return $this->dbClassBaseName . ' deleted';
    }
    
    
    
    
    
    
    
    public function readObject($id) {
        $dao = object_container_get( $this->daoClass );
        
        $dbClass = $dao->getObjectName();
        
        $obj = new $dbClass( $id );
        if ($obj->read() == false) {
            return false;
        }
        
        foreach($this->relationMTON as $rel) {
            $list = $this->readMTON($obj, $rel);
            
            $func = 'set'.dbCamelCase( $rel['name'] );
            
            if (method_exists($obj, $func)) {
                $obj->$func( $list );
            }
        }

        foreach($this->relationMTO1 as $rel) {
            $list = $this->readMTO1($obj, $rel);
            
            $func = 'set'.dbCamelCase( $rel['name'] );
            if (method_exists($obj, $func)) {
                $obj->$func( $list );
            }
        }

        hook_eventbus_publish($obj, 'core', 'FormDbMapper::readObject');
        
        return $obj;
    }
    
    
    public function readForm($id) {
        $obj = $this->readObject($id);
        
        $form = object_container_create( $this->formClass );
        $form->bind ( $obj );
        
        hook_eventbus_publish($form, 'core', 'FormDbMapper::readForm');
        
        return $form;
    }
    
    
    
    protected function readMTON($parentObj, $rel) {
        $daoLink = object_container_get( $rel['daoLink'] );
        $daoObject = object_container_get( $rel['daoObject'] );
        
        $linkObj = object_container_create( $daoLink->getObjectName() );
        $retrObj = object_container_create( $daoObject->getObjectName() );
        
        $linkKey = $retrObj->getPrimaryKey();
        $parentPk = $parentObj->getPrimaryKey();

        $sql = "select linktbl.*, tbl1.*
                from `".$retrObj->getTableName()."` tbl1
                join `".$linkObj->getTableName()."` linktbl on (tbl1.{$linkKey} = linktbl.{$linkKey})
                where linktbl.{$parentPk} = ? "; 
        
        if ($linkObj->hasDatabaseField('deleted')) {
            $sql .= ' AND linktbl.deleted = false ';
        }
        if ($retrObj->hasDatabaseField('deleted')) {
            $sql .= ' AND tbl1.deleted = false ';
        }
        
        if ($linkObj->hasDatabaseField('sort')) {
            $sql .= "\n ORDER BY linktbl.sort ASC ";
        }
        else if ($retrObj->hasDatabaseField('sort')) {
            $sql .= "\n ORDER BY tbl1.sort ASC ";
        }
        
        return $daoObject->queryList( $sql, array( $parentObj->getField( $parentPk ) ));
    }
    
    protected function readMTO1($parentObj, $rel) {
        $daoObject = object_container_get( $rel['daoObject'] );
        
        $retrObj = object_container_create( $daoObject->getObjectName() );
        
        $parentPk = $parentObj->getPrimaryKey();
        
        $sql = "select tbl1.*
                from `".$retrObj->getTableName()."` tbl1
                where tbl1.{$parentPk} = ? ";
        if ($retrObj->hasDatabaseField('sort')) {
            $sql .= "\n ORDER BY linktbl.sort ASC ";
        }
        
        return $daoObject->queryList( $sql, array( $parentObj->getField( $parentPk ) ));
    }
    
    
    public function saveForm($form) {
        hook_eventbus_publish($form, 'core', 'FormDbMapper::saveForm-start');
        
        $dao = object_container_create( $this->daoClass );
        $dbObj = object_container_create( $dao->getObjectName() );
        
        // fetch primary key
        $pk = $dbObj->getPrimaryKey();
        $pk_id = $form->getWidgetValue( $pk );
        
        
        if ($pk_id) {
            $dbObj = $this->readObject( $pk_id );
        }
        
        
        $isNew = $dbObj->isNew();
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew( $form );
        } else {
            $oldForm = $this->formClass::createAndBind( $dbObj );
            $fch = FormChangesHtml::formChanged($oldForm, $form);
        }
        
        $form->fill($dbObj, $this->publicFields);
        
        if (!$dbObj->save()) {
            throw new DatabaseException('Unable to save object');
        }
        
        $pk_id = $dbObj->getField($pk);
        $form->getWidget( $pk )->setValue( $pk_id );
        
        
        // save MTON relations
        foreach($this->relationMTON as $rel) {
            $objLink = object_container_get( $rel['daoLink'] );
            $linkObj = object_container_create( $objLink->getObjectName() );
            $linkTable = $linkObj->getTableName();
            
            $objDao = object_container_get( $rel['daoObject'] );
            
            $newList = $form->getWidget( $rel['name'] )->asArray();
            $sortfield = null;
            if (count($newList) && isset($newList[0]['sort'])) {
                $sortfield = 'sort';
            }
            $objDao->mergeFormListMTON( $linkTable, $pk, $pk_id, $newList, $sortfield );
        }
        
        // TODO: save MTO1 relations
        
        
        // logging
        $company_id = null;
        $person_id = null;
        if (method_exists($dbObj, 'getCompanyId'))
            $company_id = $dbObj->getCompanyId();
        if (method_exists($dbObj, 'getPersonId'))
            $person_id = $dbObj->getPersonId();
        if ($isNew) {
            ActivityUtil::logActivity($company_id, $person_id, $this->getLogRefObject(), null, $this->getLogCreatedCode(), $this->getLogCreatedText(), $fch->getHtml());
        } else {
            // TODO: check if object is actually changed (or always create a record, so it's logged that someone clicked 'save' ?)
            ActivityUtil::logActivity($company_id, $person_id, $this->getLogRefObject(), null, $this->getLogUpdatedCode(), $this->getLogUpdatedText(), $fch->getHtml());
        }
        
        hook_eventbus_publish($dbObj, 'core', 'FormDbMapper::saveForm-end');
        
        return $dbObj;
    }
    
    
    public function saveObject(DBObject $obj) {
        $form = object_container_create( $this->formClass );
        $form->bind( $obj );
        
        return $this->saveForm( $obj );
    }
    
    
    
    public function search($start, $limit, $opts) {
        $dao = object_container_get( $this->daoClass );
        
        $cursor = $dao->search($opts);
        
        $r = ListResponse::fillByCursor( $start, $limit, $cursor, $this->publicFields );
        
        hook_eventbus_publish($r, 'core', 'FormDbMapper::search');
        
        
        return $r;
    }
    
    
    
}
