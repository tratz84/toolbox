<?php


namespace webmail\mail;



use core\exception\FileException;
use core\exception\InvalidArgumentException;

class MailProperties {

    protected $sfile = null;
    protected $tbfile = null;
    protected $emlFile = null;
    
    protected $parsedEml = null;
    
    protected $serverProperties = array();              // imap-properties
    protected $serverPropertiesChanged = false;
    
    protected $toolboxProperties = array();             // toolbox-specific properties (status, assigned user, etc..)
    protected $toolboxPropertiesChanged = false;

    public function __construct($emlFile=null) {
        $this->emlFile = $emlFile;
        
        if (!$emlFile) {
            // null?
        }
        else if (strpos($emlFile, ctx()->getDataDir()) === 0) {
            $this->sfile = $emlFile.'.sproperties';
            $this->tbfile = $emlFile.'.tbproperties';
        } else {
            $f = get_data_file( $emlFile );
            if ($f) {
                $this->sfile = $f . '.sproperties';
                $this->tbfile = $f . '.tbproperties';
            }
        }
    }
    
    
    public function getParsedEml() {
        if ($this->parsedEml == null) {
            $this->parsedEml = new \PhpMimeMailParser\Parser();
            $this->parsedEml->setPath( $this->emlFile );
        }
        
        return $this->parsedEml;
    }
    
    
    public function getCreated() {
        $p = $this->getParsedEml();
        
        // date in mailheader
        if ($p->getHeader('date')) {
            $dt = new \DateTime(null, new \DateTimeZone('+0000'));
            $dt->setTimestamp(strtotime($p->getHeader('date')));
            return $dt->format('Y-m-d').'T'.$dt->format('H:i:s').'Z';
        }
        // server date
        else if ($this->getProperty('udate')) {
            $dt = new \DateTime( );
            $dt->setTimestamp( $this->getProperty('udate') );
            return $dt->format('Y-m-d').'T'.$dt->format('H:i:s').'Z';
        }
        else if ($this->getProperty('created')) {
            $dt = new \DateTime( $this->getProperty('created') );
            return $dt->format('Y-m-d').'T'.$dt->format('H:i:s').'Z';
        }
        
        return null;
    }
    
    
    
    public function toolboxPropertyFileExists() { return file_exists($this->tbfile) ? true : false; }
    
    public static function checksumServerProperties($emlFile) {
        $pf = $emlFile . '.sproperties';
        
        if (file_exists($pf)) {
            return crc32_int32( file_get_contents($pf) );
        } else {
            return -1;
        }
    }
    
    
    public function load() {
        if ($this->sfile && file_exists($this->sfile)) {
            $data = file_get_contents( $this->sfile );
            $this->serverProperties = json_decode( $data, true );
        }
        if ($this->tbfile && file_exists($this->tbfile)) {
            $data = file_get_contents( $this->tbfile );
            $this->toolboxProperties = json_decode( $data, true );
        }
        
        if (is_array($this->serverProperties)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function save() {
        if (!$this->sfile) {
            throw new FileException('No file set');
        }
        
        if ($this->serverPropertiesChanged) {
            file_put_contents($this->sfile, json_encode($this->serverProperties));
        }
        
        if ($this->toolboxPropertiesChanged) {
            file_put_contents($this->tbfile, json_encode($this->toolboxProperties));
        }
        
        return true;
    }
    
    public function getProperties() { return $this->serverProperties; }
    
    public function getServerProperties() { return $this->serverProperties; }
    public function setServerProperty($name, $val) {
        if (isset($this->serverProperties[$name]) == false || $this->serverProperties[$name] != $val) {
            $this->serverPropertiesChanged = true;
        }
        
        $this->serverProperties[$name] = $val;
    }
    
    public function setToolboxProperty($name, $val) {
        if (isset($this->toolboxProperties[$name]) == false || $this->toolboxProperties[$name] != $val) {
            $this->toolboxPropertiesChanged = true;
        }
        
        $this->toolboxProperties[$name] = $val;
    }
    
    public function getProperty($name, $defaultValue=null) {
        if (isset($this->serverProperties[$name])) {
            return $this->serverProperties[$name];
        } else if (isset($this->toolboxProperties[$name])) {
            return $this->toolboxProperties[$name];
        } else {
            return $defaultValue;
        }
    }
    
    
    public function getConnectorId() { return $this->getProperty('connectorId'); }
    public function setConnectorId($id) { $this->setServerProperty('connectorId', $id); }
    
    public function getUid() { return $this->getProperty('uid'); }
    public function setUid($u) { return $this->setServerProperty('uid', $u); }
    
    public function getFolder() { return $this->getProperty('folder'); }
    public function setFolder($f) { $this->setServerProperty('folder', $f); }
    
    // set in \webmai\mail\ImapConnection
    public function getConnectorDescription() { return $this->getProperty('connectorDescription'); }
    public function getSubject() { return $this->getProperty('subject'); }
    public function getFrom() { return $this->getProperty('from'); }
    public function getTo() { return $this->getProperty('to'); }
    public function getSize() { return $this->getProperty('size'); }
    public function getMessageId() { return $this->getProperty('message_id'); }
    public function getUDate() { return $this->getProperty('udate'); }
    public function getFlagged() { return $this->getProperty('flagged'); }

    public function getAnswered() { return $this->getProperty('answered'); }
    public function setAnswered($bln) { $this->setServerProperty('answered', $bln ? true : false); }
    
    public function getDeleted() { return $this->getProperty('deleted'); }
    public function setDeleted($bln) { $this->setServerProperty('deleted', $bln ? true : false); }
    
    public function getSeen() { return $this->getProperty('seen'); }
    public function setSeen($bln) { $this->setServerProperty('seen', $bln ? true : false); }
    
    public function getDraft() { return $this->getProperty('draft'); }
    
    
    public function setJunk($bln) { $this->setToolboxProperty('junk', $bln ? true : false); }
    public function isJunk() { return $this->getProperty('junk', false); }
    
    public function setMarkDeleted($bln) { $this->setToolboxProperty('markDeleted', $bln ? true : false); }
    public function getMarkDeleted() { return $this->getProperty('markDeleted', false); }
    
    
    
    public function setAction($a) {
        $mailActions = mapMailActions();
        if (isset($mailActions[$a]) == false) {
            throw new InvalidArgumentException('Invalid action');
        }
        
        $this->setToolboxProperty('action', $a);
    }
    public function getAction() { return $this->getProperty('action', 'open'); }
    
    public function setUserId($id) { $this->setToolboxProperty('userId', $id); }
    public function getUserId() { return $this->getProperty('userId'); }
    
}

