<?php

namespace signrequest\api;

use core\Context;

class SignRequestApi {
    
    protected $responseCreateDocument = null;
    protected $responseSignRequest = null;
    
    
    public function getResponseCreateDocument() { return $this->responseCreateDocument; }
    public function getResponseSignRequest() { return $this->responseSignRequest; }
    
    
    public function createDocument($files) {
        $token = Context::getInstance()->getSetting('signrequestToken');
        
        $opts = array();
        $opts['headers'] = array(
            'Authorization: Token '.$token,
            'Content-Type: application/json'
        );
        
        $keys = array_keys($files);
        
        // post document
        $sr = array();
        $sr['file_from_content'] = base64_encode( $files[$keys[0]] );
        $sr['file_from_content_name'] = $keys[0];
        
        $data = post_url( 'https://signrequest.com/api/v1/documents/', json_encode($sr), $opts );
        $json = @json_decode($data);
        
        if (isset($json->url) == false) {
            return $json;
        }
        
        if (count($keys) > 1) {
            $sr['attachments'] = array();
            
            for($x=1; $x < count($keys); $x++) {
                $r = $this->addAttachment($json->url, $keys[$x], $files[$keys[$x]]);
                
                if (isset($r->url) == false) {
                    // TODO: handle error?
                }
            }
        }
        
        
        $this->responseCreateDocument = $data;
        
        return $json;
    }
    
    
    public function signRequest($documentUrl, $fromEmail, $message, $signers=array()) {
        $token = Context::getInstance()->getSetting('signrequestToken');
        
        $opts = array();
        $opts['headers'] = array(
            'Authorization: Token '.$token,
            'Content-Type: application/json'
        );
        
        $sr = array();
        $sr['document'] = $documentUrl;
        //         $sr['document'] = 'https://itxplain-dev.signrequest.com/api/v1/documents/0aa0ee2e-b9f1-4fbc-bb30-e587aa0f4651/';
        $sr['from_email'] = $fromEmail;
        $sr['message'] = $message;
        $sr['signers'] = array();
        foreach($signers as $s) {
            $sr['signers'][] = array('email' => $s, 'language' => 'nl');
        }
        
        $data = post_url( 'https://signrequest.com/api/v1/signrequests/', json_encode($sr), $opts );
        
        $this->responseSignRequest = $data;
        
        return @json_decode($data);
    }
    
    
    public function addAttachment( $documentUrl, $name, $data ) {
        $token = Context::getInstance()->getSetting('signrequestToken');
        
        $opts = array();
        $opts['headers'] = array(
            'Authorization: Token '.$token,
            'Content-Type: application/json'
        );
        
        $sr = array();
        $sr['document'] = $documentUrl;
        $sr['file_from_content_name'] = $name;
        $sr['file_from_content'] = base64_encode( $data );
        
        
        $data = post_url( 'https://signrequest.com/api/v1/document-attachments/', json_encode($sr), $opts );
        
        return @json_decode($data);
    }
    
    
    
}

