<?php

namespace core\service;

use base\forms\FormChangesHtml;
use base\util\ActivityUtil;
use core\db\DBObject;
use core\exception\DatabaseException;
use core\forms\lists\ListResponse;
use core\exception\ObjectNotFoundException;
use base\model\ObjectMetaDAO;
use core\exception\ObjectModifiedException;


class FormDbHandler {

    /** @var FormDbMapper $mapper */
    protected $mapper;
    
    public function __construct($formMapper) {
        $this->mapper = $formMapper;
    }
    
    
    public static function getHandler($formClass) {
        $mapperFunc = '\\'.$formClass.'::getDbMapper';
        $mapper = $mapperFunc();
        
        return new FormDbHandler( $mapper );
    }
    
    
    /**
     * 
     * @return \core\service\FormDbMapper
     */
    public function getMapper() { return $this->mapper; }
    
    
    public function readObject($id) {
        $dao = object_container_get( $this->mapper->getDaoClass() );
        
        $dbClass = $dao->getObjectName();
        
        $obj = new $dbClass( $id );
        if ($obj->read() == false) {
            return false;
        }
        
        foreach($this->mapper->getMTON() as $rel) {
            $list = $this->readMTON($obj, $rel);
            
            $func = 'set'.dbCamelCase( $rel['name'] );
            
            if (method_exists($obj, $func)) {
                $obj->$func( $list );
            }
        }
        
        foreach($this->mapper->getMTO1() as $rel) {
            $list = $this->readMTO1($obj, $rel);
            
            $func = 'set'.dbCamelCase( $rel['name'] );
            if (method_exists($obj, $func)) {
                $obj->$func( $list );
            }
        }
        
        hook_eventbus_publish([$this, $obj], 'core', 'FormDbHandler::readObject');
        
        return $obj;
    }
    
    
    public function readForm($id) {
        $obj = $this->readObject($id);
        
        $form = object_container_create( $this->mapper->getFormClass() );
        $form->bind ( $obj );
        
        hook_eventbus_publish([$this, $form], 'core', 'FormDbHandler::readForm');
        
        return $form;
    }
    
    public function formForObject( $obj ) {
        $form = object_container_create( $this->mapper->getFormClass() );
        $form->bind ( $obj );
        
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
        hook_eventbus_publish([$this, $form], 'core', 'FormDbHandler::saveForm-start');
        
        $dao = object_container_create( $this->mapper->getDaoClass() );
        $dbObj = object_container_create( $dao->getObjectName() );
        
        // fetch primary key
        $pk = $dbObj->getPrimaryKey();
        $pk_id = $form->getWidgetValue( $pk );
        
        
        if ($pk_id) {
            $dbObj = $this->readObject( $pk_id );
        }

        // check if object is changed while editing
        $widgetObjectVersion = $form->getWidget('object_version');
        if ($widgetObjectVersion && $form->getWidgetValue('object_version') != $dbObj->getObjectVersion()) {
            // TODO: something better then an exception..
            throw new ObjectModifiedException( 'Object changed by other session' );
        }
        
        
        $isNew = $dbObj->isNew();
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew( $form );
        } else {
            $oldForm = $this->mapper->getFormClass()::createAndBind( $dbObj );
            $fch = FormChangesHtml::formChanged($oldForm, $form);
        }
        
        $form->fill($dbObj, $this->mapper->getPublicFields());
        
        if (!$dbObj->save()) {
            throw new DatabaseException('Unable to save object');
        }
        
        $pk_id = $dbObj->getField($pk);
        $form->getWidget( $pk )->setValue( $pk_id );
        
        
        // save MTON relations
        foreach($this->mapper->getMTON() as $rel) {
            $objLink = object_container_get( $rel['daoLink'] );
            $linkObj = object_container_create( $objLink->getObjectName() );
            $linkTable = $linkObj->getTableName();
            
            $objDao = object_container_get( $rel['daoObject'] );
            
            $newList = $form->getWidget( $rel['name'] )->asArray();
            $sortfield = null;
            if ($linkObj->hasDatabaseField('sort')) {
                $sortfield = 'sort';
            }
            $objDao->mergeFormListMTON( $linkTable, $pk, $pk_id, $newList, $sortfield );
        }
        
        // save MTO1 relations
        foreach($this->mapper->getMTO1() as $rel) {
            $objDao = object_container_get( $rel['daoObject'] );
            $linkObj = object_container_create( $objDao->getObjectName() );
            $linkTable = $linkObj->getTableName();
            
            $newList = $form->getWidget( $rel['name'] )->asArray();
            $sortfield = null;
            if ($linkObj->hasDatabaseField('sort')) {
                $sortfield = 'sort';
            }
            
            $objDao->mergeFormListMTO1($pk, $pk_id, $newList, $sortfield );
        }
        
        // logging
        $company_id = null;
        $person_id = null;
        if (method_exists($dbObj, 'getCompanyId'))
            $company_id = $dbObj->getCompanyId();
        if (method_exists($dbObj, 'getPersonId'))
            $person_id = $dbObj->getPersonId();
        
        // ref_id set?
        $refId = null;
        if ($this->mapper->getLogRefIdField()) {
            $refId = $dbObj->getField( $this->mapper->getLogRefIdField() );
        }
        
        if ($isNew) {
            ActivityUtil::logActivity($company_id, $person_id, $this->mapper->getLogRefObject(), $refId, $this->mapper->getLogCreatedCode(), $this->mapper->getLogCreatedText(), $fch->getHtml());
        } else {
            // TODO: check if object is actually changed (or always create a record, so it's logged that someone clicked 'save' ?)
            ActivityUtil::logActivity($company_id, $person_id, $this->mapper->getLogRefObject(), $refId, $this->mapper->getLogUpdatedCode(), $this->mapper->getLogUpdatedText(), $fch->getHtml());
        }
        
        hook_eventbus_publish([$this, $dbObj], 'core', 'FormDbHandler::saveForm-end');
        
        return $dbObj;
    }
    
    
    public function saveObject(DBObject $obj) {
        $form = object_container_create( $this->mapper->getFormClass() );
        $form->bind( $obj );
        
        return $this->saveForm( $obj );
    }
    
    
    public function deleteById( $id ) {
        // fetch object
        $dbObj = $this->readObject( $id );
        if (!$dbObj) {
            throw new ObjectNotFoundException('Object not found');
        }
        
        // get form
        $form = $this->formForObject( $dbObj );
        
        // object has deleted-flag? => just mark deleted
        if ($dbObj->hasDatabaseField('deleted')) {
            // delete
            $dao = object_container_get( $this->mapper->getDaoClass() );
            $dao->delete( $id );
        }
        else {
            $objectName = $this->mapper->getName();
            
            // delete relationMTO1
            foreach($this->mapper->getMTO1() as $rel) {
                $dao = object_container_get( $rel['daoObject'] );
                
                $func = 'deleteBy'.$objectName;
                if (method_exists($dao, $func))
                    $dao->func( $id );
            }
            
            // delete relationMTON
            foreach($this->mapper->getMTON() as $rel) {
                $dao = object_container_get( $rel['daoObject'] );
                
                $func = 'deleteBy'.$objectName;
                if (method_exists($dao, $func))
                    $dao->func( $id );
            }
            
            // delete object_meta
            $omDao = object_container_get(ObjectMetaDAO::class);
            $omDao->deleteByObject( $this->mapper->getDbClass(), $id );
            
            // Object itself
            $dbObj->delete();
        }
        
        // logging
        $company_id = $person_id = null;
        if (method_exists($dbObj, 'getCompanyId'))
            $company_id = $dbObj->getCompanyId();
        if (method_exists($dbObj, 'getPersonId'))
            $person_id = $dbObj->getPersonId();
        
        // ref_id set?
        $refId = null;
        if ($this->mapper->getLogRefIdField()) {
            $refId = $dbObj->getField( $this->mapper->getLogRefIdField() );
        }
        
        
        $fch = FormChangesHtml::formDeleted($form);
        ActivityUtil::logActivity($company_id, $person_id, $this->mapper->getLogRefObject(), $refId, $this->mapper->getLogDeletedCode(), $this->mapper->getLogDeletedText(), $fch->getHtml());
    }
    
    
    public function search($start, $limit, $opts) {
        $dao = object_container_get( $this->mapper->getDaoClass() );
        
        $cursor = $dao->search($opts);
        
        $r = ListResponse::fillByCursor( $start, $limit, $cursor, $this->mapper->getPublicFields() );
        
        hook_eventbus_publish([$this, $r], 'core', 'FormDbHandler::search');
        
        return $r;
    }
    
}

