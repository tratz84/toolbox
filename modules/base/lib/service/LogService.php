<?php

namespace base\service;


use base\model\ObjectLogDAO;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use base\forms\FormChangesHtml;
use core\db\DBObject;

class LogService extends ServiceBase {
    

    public function search($start, $limit, $opts = array()) {
        $olDao = new ObjectLogDAO();
        
        $cursor = $olDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('object_log_id', 'object_name', 'object_id', 'object_key', 'object_action', 'value_old', 'value_new', 'created'));
        
        return $r;
    }
    
    public function saveChangesDBObject(DBObject $obj, FormChangesHtml $fch) {
        $objectId = $obj->getField( $obj->getPrimaryKey() );
        $objectName = get_class($obj);
        
        return $this->saveChanges($objectName, $objectId, $fch);
    }
    
    public function saveChanges($objectName, $objectId, FormChangesHtml $fch) {
        $olDao = new ObjectLogDAO();
        return $olDao->saveChanges($objectName, $objectId, $fch->getChanges());
    }
    
    
    
}
