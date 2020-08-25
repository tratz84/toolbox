<?php


namespace filesync\service;


use filesync\FilesyncSettings;
use filesync\model\WopiAccess;
use filesync\model\WopiAccessDAO;

class WopiService {
    
    
    public function createToken( $userId, $path, $opts=array() ) {
        $wa = new WopiAccess();
        $at = '';
        for($x=0; $x < 3; $x++) {
            $at .= md5(uniqid().uniqid().uniqid().uniqid().uniqid());
        }
        $wa->setAccessToken( $at );
        
        // set access token TTL
        $filesyncSettings = object_container_get( FilesyncSettings::class );
        $ttl = $filesyncSettings->getWopiAccessTokenTtl();
        if ($ttl < 0) $ttl = 1;
        $wa->setAccessTokenTtl( 60 * $ttl );
        
        $wa->setUserId( $userId );
        if (isset($opts['base_path']) && $opts['base_path']) {
            $wa->setBasePath( $opts['base_path'] );
        }
        $wa->setPath( $path );
        $wa->save();
        
        return $wa;
    }
    
    public function readTokenById( $wopiAccessId ) {
        $waDao = object_container_get( WopiAccessDAO::class );
        
        return $waDao->read( $wopiAccessId );
    }
    
    
    public function cleanupTokens() {
        $waDao = object_container_get( WopiAccessDAO::class );
        
        $waDao->cleanup();
    }
    
}



