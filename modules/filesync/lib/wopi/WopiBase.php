<?php


namespace filesync\wopi;


class WopiBase {
    
    
    
    public function __construct() {
        
        
    }
    
    
    
    public function validateAccessToken( $token, $path ) {
        
    }
    
    
    public function json($arr) {
        header('Content-type: application/json; charset=utf-8');
        
        print json_encode( $arr );
    }
    
    
    
    
    
    
}

