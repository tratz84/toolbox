<?php


use core\db\DBObject;

function format_customername($obj) {
    if (is_object($obj)) {
        if (is_a($obj, \customer\model\Person::class)) {
            return format_personname( $obj );
        } else if (is_a($obj, \customer\model\Company::class)) {
            return $obj->getCompanyName();
        } else if (is_a($obj, DBObject::class)) {
            $obj = $obj->getFields();
        }
    }
    if (is_array($obj)) {
        if (isset($obj['company_id']) && $obj['company_id']) {
            return $obj['company_name'];
        } else if (isset($obj['person_id']) && $obj['person_id']) {
            
            return format_personname($obj);
        }
    }
    
    return null;
}
