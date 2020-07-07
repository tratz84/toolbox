<?php




function mapUserTypes() {
    $t = array();
    
    $t['admin']         = t('userType.admin');
    $t['user']          = t('userType.user');
    $t['external_user'] = t('userType.external_user');
    
    $t = apply_filter('mapUserTypes', $t);
    
    return $t;
}
    
