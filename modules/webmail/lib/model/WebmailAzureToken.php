<?php


namespace webmail\model;


class WebmailAzureToken extends base\WebmailAzureTokenBase {

	public function __construct($id=null) {
		parent::__construct( $id );
		
	}
	
	
	public function getConnectionStatus() {
	    $r = $this->getResponseData();
	    
	    if ($r) {
	        $json = json_decode( $r );
	        
	        if ($json && isset($json->token_type)) {
	            return 'OK';
	        }
	        else if ($json) {
	            if (isset($json->error)) {
	                return t('Error') . ': '. $json->error . ', '. $json->error_description;
	            }
	            else {
	                return t('Unknown error');
	            }
	        }
	        else {
	            return t('Invalid response');
	        }
	    }
	    else {
	        return t('Token not set');
	    }
	}
	
}

