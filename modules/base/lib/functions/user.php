<?php




function mapUserTypes() {
    $t = array();
    
    $t['admin'] = 'Administrator';
    $t['user'] = t('User');
    $t['external_user'] = t('External user');
    
    $t = apply_filter('mapUserTypes', $t);
    
    return $t;
}
    
