<?php

namespace base\service;

use core\service\ServiceBase;
use base\model\ObjectMetaDAO;
use core\exception\InvalidStateException;
use base\model\ObjectMeta;

class MetaService extends ServiceBase {
    
    public function searchOne($opts=array()) {
        $omDao = new ObjectMetaDAO();
        
        $cursor = $omDao->search($opts);
        
        if ($cursor->numRows() > 1) {
            throw new InvalidStateException('Multiple objects found');
        } else {
            $o = $cursor->next();
            
            $cursor->free();
            
            return $o;
        }
    }
    
    public function readByObject($objectName, $objectId) {
        $omDao = new ObjectMetaDAO();
        
        return $omDao->readByObject($objectName, $objectId);
    }
    
    
    public function readByKey($objectName, $objectId, $objectKey) {
        $omDao = new ObjectMetaDAO();
        
        return $omDao->readByKey($objectName, $objectId, $objectKey);
    }
    
    
    public function getMetaValue($objectName, $objectId, $objectKey) {
        $omDao = new ObjectMetaDAO();
        
        $l = $omDao->readByKey($objectName, $objectId, $objectKey);
        
        if ($l)
            return $l->getObjectValue();
        else
            return null;
    }
    
    public function getIdByObjectValue($objectName, $objectKey, $objectValue) {
        $omDao = new ObjectMetaDAO();
        
        $l = $omDao->readByValue($objectName, $objectKey, $objectValue);
        
        if ($l)
            return $l->getObjectId();
        else
            return null;
    }
    
    public function saveMeta($objectName, $objectId, $objectKey, $value) {
        $omDao = new ObjectMetaDAO();
        
        $om = $omDao->readByKey($objectName, $objectId, $objectKey);
        if (!$om) {
            $om = new \base\model\ObjectMeta();
            $om->setObjectName($objectName);
            $om->setObjectId($objectId);
            $om->setObjectKey($objectKey);
        }
        
        $om->setObjectValue($value);
        
        return $om->save();
    }
    
    public function saveObject(ObjectMeta $om) {
        return $om->save();
    }
    
    
}
