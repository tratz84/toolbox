<?php


namespace securemail\model;


class SecureMessage extends base\SecureMessageBase {
    
    
    protected $saveKey = true;
    protected $plainMessage;
    

	public function __construct($id=null) {
		parent::__construct( $id );
		
		
		$this->setTtlExpiresOn( date('Y-m-d', strtotime('+5 day')) . ' 00:00:00');
	}
	
	
	
	public function setSaveKey($bln) { $this->saveKey = $bln ? true : false; }
	public function getSaveKey() { return $this->saveKey; }
	
	public function setPlainMessage($p) { $this->plainMessage = $p; }
	public function getPlainMessage() { return $this->plainMessage; }
	
	
	public function getPublicLink() {
	    $p = $this->getPassword();
	    if (!$p)
	        return null;
        
        return BASE_URL . appUrl('/?m=securemail&c=public/showsecret&smid='.$this->getSecureMessageId().'&p='.urlencode(base64_encode($this->getPassword())));
	}
	
	
	public function getDecryptedMessage($password = null) {
	    if ($password == null)
	        $password = $this->getPassword();
	    
        $iv = base64_decode($this->getEncryptionIv());
        $cipher = $this->getEncryptionAlgo();
	    
	    return sm_decrypt_message($password, $iv, $this->getEncryptedMessage(), ['cipher' => $cipher]);
	}
	
	
	public function isAvailable() {
	    if ($this->isDeleted())
	        return false;
	    return $this->getEncryptedMessage() != '' ? true : false;
	}
	
	public function isDeleted() {
	    return valid_datetime( $this->getDeleted() ) ? true : false;
	}
	
	
	public function checkPassword( $pw ) {
	    $hash = md5( $pw );
	    
	    if ($hash == $this->getPasswordHash()) {
	        return true;
	    }
	    else {
	        return false;
	    }
	}
	
	
	
}

