<?php


namespace webmail\storage;

use core\parser\HtmlParser;
use webmail\mail\MailProperties;
use webmail\model\ConnectorImapfolderDAO;

class ImportMailBase {
    
    protected $mapImapFolders = null;
    
    
    public function __construct() {
        
    }
    
    
    protected function attachmentCount(\PhpMimeMailParser\Parser $p) {
        $att = $p->getAttachments();
        
        $attachmentCount = 0;
        
        foreach($att as $at) {
            $attHeaders = $at->getHeaders();
            
            //             if (isset($attHeaders['content-id'])) {
            //                 // content-id set? => attachment used in e-mail. Don't count as attachment
            //             }
            //             else
            if (isset($attHeaders['content-disposition']) && stripos($attHeaders['content-disposition'], 'attachment') !== false) {
                $attachmentCount++;
            }
            else if ($at->getContentDisposition() == 'attachment') {
                // not sure to count this one
                $attachmentCount++;
            }
        }
        
        return $attachmentCount;
    }
    
    
    /**
     * buildRefMessageIds() - returns array with all id's that reference this eml (In-Reply-To, References, Thread-Index)
     *
     * @return string[]
     */
    protected function buildRefMessageIds(\PhpMimeMailParser\Parser $p) {
        $ids = array();
        
        $str = '';
        $str .= $p->getHeader('In-Reply-To') . "\n";
        $str .= $p->getHeader('References') . "\n";
        $str .= $p->getHeader('Thread-Index') . "\n";
        
        $raw_ids = preg_split("/(\n| )/", $str);
        foreach($raw_ids as $ri) {
            $ri = trim($ri);
            
            if ($ri && in_array($ri, $ids) == false) {
                $ids[] = $ri;
            }
        }
        
        return $ids;
    }
    
    public function parseEml($emlFile) {
        $mp = new MailProperties($emlFile);
        $mp->load();
        
        $p = $mp->getParsedEml();
        
        $datadir = \core\Context::getInstance()->getDataDir();
        
        $r = array();
        $r['id'] = str_replace($datadir, '', $emlFile);
        $r['contextName'] = $this->contextName;
        $r['file'] = str_replace($datadir, '', $emlFile);
        $r['date'] = $mp->getCreated();
        
        // use cleanup_string(), else solr might not accept document. In that case, the whole document-batch is rejected!
        $r['emlMessageId'] = cleanup_string( $p->getHeader('Message-ID') );
        $r['emlThreadId'] = cleanup_string( $p->getHeader('Thread-Index') );
        $r['refMessageId'] = $this->buildRefMessageIds( $p );
        $this->lastInReplyTo = $p->getHeader('In-Reply-To');
        
        $r['subject'] = cleanup_string( $p->getHeader('subject') );
        
        // html mail?
        $htmlMessageBody = $p->getMessageBody('html');
        if (trim( $htmlMessageBody ) != '') {
            $hp = new HtmlParser();
            $hp->loadString( $htmlMessageBody );
            $hp->parse();
            
            // use cleanup_string(), else solr might not accept document. In that case, the whole document-batch is rejected!
            $r['content'] = cleanup_string( $hp->getBodyText() );
        }
        // text mail?
        else {
            // use cleanup_string(), else solr might not accept document. In that case, the whole document-batch is rejected!
            $r['content'] = cleanup_string( $p->getMessageBody('text') );
        }
        
        
        //         $r['text'] = array();
        
        if (trim($r['content']) == '') {
            $r['content'] = cleanup_string( $p->getMessageBody('text') );
            //             $r['text'][] = $r['content'];
        }
        
        // fields
        // TODO: check out why 'folder' is not set in some cases?
        $r['mailboxName']          = $mp->getFolder();
        $r['connectorId']          = $mp->getConnectorId();
        $r['connectorDescription'] = $mp->getConnectorDescription();
        $r['isJunk']               = $mp->getFolder() == 'Junk' || $mp->getFolder() == 'Spam';
        $r['isNotJunk']            = !$r['isJunk'];
        $r['isAnswered']           = $mp->getAnswered() ? true : false;
        $r['isRead']               = $mp->getSeen() ? true : false;
        $r['isForwarded']          = null;
        $r['isSeen']               = $r['isRead'];
        $r['isDeleted']            = $mp->getDeleted() ? true : false;
        $r['status']               = array('imported');
        $r['permissions']          = array();
        $r['server_properties_checksum'] = MailProperties::checksumServerProperties($emlFile);
        
        $r['markDeleted']          = $mp->getMarkDeleted();
        $r['attachmentCount']      = $this->attachmentCount($p);
        
        $r['userId'] = $mp->getUserId();
        
        // forcedAction set?
        if ($this->forcedAction && $mp->getAction() != $this->forcedAction) {
            $mp->setAction( $this->forcedAction );
            $mp->save();
        }
        
        $r['action'] = $mp->getAction();
        
        $r['fromName'] = '';
        $r['fromEmail'] = '';
        $r['toName'] = array();
        $r['toEmail'] = array();
        
        $from = $p->getAddresses('from');
        if (count($from)) {
            if (isset($from[0]['display']) && $from[0]['display']) {
                $r['fromName'] = $from[0]['display'];
            }
            
            if (isset($from[0]['address']) && $from[0]['address']) {
                $r['fromEmail'] = $from[0]['address'];
            }
        }
        
        $tos = array_merge($p->getAddresses('to'), $p->getAddresses('cc'), $p->getAddresses('bcc'));
        foreach ($tos as $t) {
            if (isset($t['display']) && $t['display']) {
                $r['toName'][] = $t['display'];
            }
            if (isset($t['address']) && $t['address']) {
                $r['toEmail'][] = $t['address'];
            }
        }
        
        $r['recipients'] = array();
        foreach($p->getAddresses('to') as $t) {
            $a = array();
            $a['to_name'] = '';
            $a['to_email'] = '';
            $a['to_type'] = 'To';
            if (isset($t['display']) && $t['display'])
                $a['to_name'] = $t['display'];
            if (isset($t['address']) && $t['address'])
                $a['to_email'] = $t['address'];
            
            if ($a['to_email'])
                $r['recipients'][] = $a;
        }
        foreach($p->getAddresses('cc') as $t) {
            $a = array();
            $a['to_name'] = '';
            $a['to_email'] = '';
            $a['to_type'] = 'Cc';
            if (isset($t['display']) && $t['display'])
                $a['to_name'] = $t['display'];
            if (isset($t['address']) && $t['address'])
                $a['to_email'] = $t['address'];
            
            if ($a['to_email'])
                $r['recipients'][] = $a;
        }
        foreach($p->getAddresses('bcc') as $t) {
            $a = array();
            $a['to_name'] = '';
            $a['to_email'] = '';
            $a['to_type'] = 'Bcc';
            if (isset($t['display']) && $t['display'])
                $a['to_name'] = $t['display'];
            if (isset($t['address']) && $t['address'])
                $a['to_email'] = $t['address'];
            
            if ($a['to_email'])
                $r['recipients'][] = $a;
        }
        
        
        
        unset( $p );
        
        return $r;
    }
    
    
    protected function lookupContent($message, $contentType='text/html') {
        if ($message->getContentType() == $contentType) {
            return $message->getContents();
        }
        
        foreach($message->getParts() as $p) {
            $c = $this->lookupContent($p);
            
            if ($c) {
                return $c;
            }
        }
        
        return null;
    }
    
    
    
    public function importFolder( $dir ) {
        $files = list_files($dir, ['fileonly' => true, 'recursive' => true]);
        
        if (is_array($files)) for($x=0; $x < count($files); $x++) {
            if (file_extension($files[$x]) == 'eml') {
                
                if ($this->updateMode) {
                    $this->updateEml( $dir . '/' . $files[$x] );
                } else {
                    $this->queueEml( $dir . '/' . $files[$x] );
                }
                
                // purge handles minimum docs
                $this->purge();
            }
        }
        
        // empty update-queue
        if ($this->updateMode) {
            $this->updateEml(null, true);
        }
        
        $this->purge( true );
        $this->commit();
    }
    
    
    
    public function lookupImapFolderId( $connectorId, $folderName ) {
        // fill cache
        if ($this->mapImapFolders === null) {
            $this->mapImapFolders = array();
            
            
            $cifDao = new ConnectorImapfolderDAO();
            $cifs = $cifDao->readAll();
            
            foreach($cifs as $cif) {
                $uid = $cif->getConnectorId() . '-' . $cif->getFolderName();
                $this->mapImapFolders[ $uid ] = $cif;
            }
        }
        
        // lookup
        $uid = $connectorId . '-' . $folderName;
        
        if (isset($this->mapImapFolders[ $uid ])) {
            return $this->mapImapFolders[ $uid ]->getConnectorImapFolderId();
        }
        else {
            return null;
        }
    }
    
    
    
}

