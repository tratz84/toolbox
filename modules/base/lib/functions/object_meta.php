<?php




use base\model\ObjectMeta;
use base\service\MetaService;

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


