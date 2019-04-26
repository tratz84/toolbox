<?php



use core\db\DBObject;

function format_personname($obj) {
    if (is_a($obj, DBObject::class))
        $obj = $obj->getFields();
    
    $t = '';

    if (isset($obj['lastname']) && $obj['lastname']) {
        if ($t) $t .= ' ';
        $t .= $obj['lastname'];
    }
    if (isset($obj['insert_lastname']) && $obj['insert_lastname']) {
        if ($t) $t .= ' ';
        $t .= $obj['insert_lastname'];
    }
    if (isset($obj['firstname']) && $obj['firstname']) {
        if ($t) $t .= ', ';
        $t .= ' ' . $obj['firstname'];
    }
    
    
    return $t;
}
