<?php


namespace filesync\service;


use core\forms\lists\ListResponse;
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
        
        // The access_token_ttl property tells a WOPI client when an access token expires, represented as the number of milliseconds since January 1, 1970 UTC (the date epoch in JavaScript)
        // doc @ https://wopi.readthedocs.io/projects/wopirest/en/latest/concepts.html#term-access-token-ttl
        $wa->setAccessTokenTtl( (time() + (60 * $ttl))*1000 );
        
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
    
    public function deleteAllTokens() {
        $waDao = object_container_get( WopiAccessDAO::class );
        
        $waDao->deleteAll();
    }
    
    
    public function deleteToken( $wopiAccessId ) {
        $waDao = object_container_get( WopiAccessDAO::class );
        $waDao->delete( $wopiAccessId );
    }
    
    
    public function searchWopiAccess($start, $limit, $opts = array()) {
        $waDao = object_container_get( WopiAccessDAO::class );
        
        $cursor = $waDao->search( $opts );
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('wopi_access_id', 'access_token', 'access_token_ttl', 'base_path', 'path', 'edited', 'created', 'username'));
        
        return $r;
    }
    
    
    
}



