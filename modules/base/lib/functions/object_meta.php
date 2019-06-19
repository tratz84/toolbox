<?php




use base\model\ObjectMeta;
use base\service\MetaService;
use core\Context;
use core\db\DBObject;

function object_meta_get($objectName, $objectId, $objectKey, $unserialize=true) {
    $metaService = object_container_get(MetaService::class);
    
    $val = $metaService->getMetaValue($objectName, $objectId, $objectKey);
    
    // doesn't exist?
    if ($val === null) {
        return null;
    }
    
    if ($unserialize) {
        return unserialize($val);
    } else {
        return $val;
    }
}

function object_meta_get_object($objectName, $objectId, $objectKey, $unserialize=true) {
    $metaService = object_container_get(MetaService::class);
    
    return $metaService->readByKey($objectName, $objectId, $objectKey);
}

function object_meta_save($objectName, $objectId, $objectKey, $val, $serialize=true) {
    $metaService = object_container_get(MetaService::class);
    
    $om = $metaService->readByKey($objectName, $objectId, $objectKey);
    
    if (!$om) {
        $om = new ObjectMeta();
        $om->setObjectName($objectName);
        $om->setObjectId($objectId);
        $om->setObjectKey($objectKey);
    }
    
    if ($serialize) {
        $om->setObjectValue( serialize($val) );
    } else {
        $om->setObjectValue($val);
    }
    
    return $metaService->saveObject( $om );
}


function object_locking_enabled() {
    $ctx = Context::getInstance();
    
    return $ctx->getSetting('object_locking', false);
}

function render_dbobject_lock(DBObject $o) {
    if ($o->isNew() || object_locking_enabled() == false)
        return '';
    
    $id = $o->getField( $o->getPrimaryKey() );
    
    return render_object_lock(get_class($o), $id);
}

function render_object_lock($objectName, $id) {
    if (object_locking_enabled() == false)
        return '';
    
    return 'lock';
}

function dbobject_is_locked(DBObject $o) {
    if ($o->isNew() || object_locking_enabled() == false)
        return false;
        
    $id = $o->getField( $o->getPrimaryKey() );
    
    $v = object_meta_get(get_class($o), $id, 'object_locked');
    
    if ($v === true) {
        return true;
    } else {
        return false;
    }
}


function object_is_locked($objectName, $id) {
    if (object_locking_enabled() == false)
        return false;
    
    $v = object_meta_get($objectName, $id, 'object_locked');
    
    if ($v === true) {
        return true;
    } else {
        return false;
    }
}

function lock_object($objectName, $id) {
    object_meta_save($objectName, $id, 'object_locked', true);
}

function unlock_object($objectName, $id) {
    object_meta_save($objectName, $id, 'object_locked', false);
}

function lock_dbobject(DBObject $o) {
    if ($o->isNew())
        return false;
    
    $id = $o->getField( $o->getPrimaryKey() );
    lock_object(get_class($o), $id);
}

function unlock_dbobject(DBObject $o) {
    if ($o->isNew())
        return false;
        
    $id = $o->getField( $o->getPrimaryKey() );
    unlock_object(get_class($o), $id);
}


