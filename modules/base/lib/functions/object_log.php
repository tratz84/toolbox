<?php


use core\Context;
use core\db\DBObject;

function render_object_log_button($objectName, $objectId) {
    if (!$objectId)
        return '';
    
    if (Context::getInstance()->isObjectLogEnabled() == false)
        return '';
    
    $onclick = 'show_object_log('.json_encode($objectName).', '.json_encode($objectId).');';
    
    $html = '<a href="javascript:void(0);" onclick="'.esc_attr($onclick).'" class="fa fa-history" title="'.esc_attr(t('View history')).'"></a>';
    
    return $html;
}

function render_object_log_button_dbobject(DBObject $db) {
    $objectName = get_class($db);
    $objectId = $db->getField( $db->getPrimaryKey() );
    
    return render_object_log_button($objectName, $objectId);
}