<?php


namespace filesync\service;


use filesync\model\WopiAccess;
use filesync\model\WopiAccessDAO;

class WopiService {
    
    
    public function createToken( $userId, $path ) {
        $wa = new WopiAccess();
        $at = '';
        for($x=0; $x < 3; $x++) {
            $at .= md5(uniqid().uniqid().uniqid().uniqid().uniqid());
        }
        $wa->setAccessToken( $at );
        
        // use multiples of 60!
        // TODO: put to 60 * 24
        $wa->setAccessTokenTtl( 60 * 1 );
        $wa->setUserId( $userId );
        $wa->setPath( $path );
        $wa->save();
        
        return $wa;
    }
    
    public function readTokenById( $wopiAccessId ) {
        $waDao = object_container_get( WopiAccessDAO::class );
        
        return $waDao->read( $wopiAccessId );
    }
    
}



