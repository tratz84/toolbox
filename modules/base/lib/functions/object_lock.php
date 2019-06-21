<?php

/*
 * object locking methods
 */

use core\Context;
use core\db\DBObject;
use core\exception\InvalidStateException;


function check_dbobject_locked(DBObject $obj) {
    if (dbobject_is_locked($obj)) {
        throw new InvalidStateException('Unable to perform action, object locked');
    }
}


function check_object_locked($objectName, $id) {
    if (object_is_locked($objectName, $id)) {
        throw new InvalidStateException('Unable to perform action, object locked');
    }
}


function object_locking_enabled() {
    $ctx = Context::getInstance();
    
    return $ctx->getSetting('object_locking', false);
}

function render_dbobject_lock(DBObject $o, $prefix) {
    if ($o->isNew() || object_locking_enabled() == false)
        return '';
        
    $id = $o->getField( $o->getPrimaryKey() );
    
    return render_object_lock(get_class($o), $id, $prefix);
}

function render_object_lock($objectName, $id, $prefix) {
    if (object_locking_enabled() == false)
        return '';
        
    if (object_is_locked($objectName, $id)) {
        $url = appUrl('/?m=base&c=objectlock&a=unlock&objectName='.urlencode($objectName).'&id='.urlencode($id).'&prefix='.urlencode($prefix).'&r='.urlencode($_SERVER['REQUEST_URI']));
        
        $html = '<a class="object-lock-toggle" href="'.esc_attr($url).'" title='.t('Unlock object').'><span class="fa fa-lock"></span></a>';
        return $html;
    } else {
        $url = appUrl('/?m=base&c=objectlock&a=lock&objectName='.urlencode($objectName).'&id='.urlencode($id).'&prefix='.urlencode($prefix).'&r='.urlencode($_SERVER['REQUEST_URI']));
        
        $html = '<a class="object-lock-toggle" href="'.esc_attr($url).'" title='.t('Lock object').'><span class="fa fa-unlock"></span></a>';
        return $html;
    }
}

function dbobject_is_locked(DBObject $o) {
    if ($o == null || $o->isNew() || object_locking_enabled() == false)
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
    
    return true;
}

function unlock_object($objectName, $id) {
    object_meta_save($objectName, $id, 'object_locked', false);
    
    return true;
}

function lock_dbobject(DBObject $o) {
    if ($o == null || $o->isNew())
        return false;
        
    $id = $o->getField( $o->getPrimaryKey() );
    return lock_object(get_class($o), $id);
}

function unlock_dbobject(DBObject $o) {
    if ($o == null || $o->isNew())
        return false;
        
    $id = $o->getField( $o->getPrimaryKey() );
    return unlock_object(get_class($o), $id);
}


