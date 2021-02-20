<?php

namespace filesync\service;

use core\exception\FileException;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use filesync\form\ArchiveFileUploadForm;
use filesync\form\StoreFileMetaForm;
use filesync\form\StoreFileUploadForm;
use filesync\form\StoreForm;
use filesync\model\Store;
use filesync\model\StoreDAO;
use filesync\model\StoreFile;
use filesync\model\StoreFileDAO;
use filesync\model\StoreFileDownloadLog;
use filesync\model\StoreFileMeta;
use filesync\model\StoreFileMetaDAO;
use filesync\model\StoreFileRev;
use filesync\model\StoreFileRevDAO;
use filesync\exception\FilesyncException;

class StoreService extends ServiceBase {
    
    public function readStore($id) {
        $sDao = new StoreDAO();
        
        return $sDao->read($id);
    }
    
    public function readStoreByName($name) {
        $sDao = new StoreDAO();
        
        return $sDao->readByName($name);
    }
    
    public function readAllStores() {
        $sDao = new StoreDAO();
        
        return $sDao->readAll();
    }
    
    public function readArchiveStores() {
        $sDao = new StoreDAO();
        
        return $sDao->readArchives();
    }
    
    
    public function saveStore(StoreForm $form) {
        $storeId = $form->getWidgetValue('store_id');
        if ($storeId) {
            $store = $this->readStore($storeId);
        } else {
            $store = new Store();
        }
        
        $isNew = $store->isNew();
        
        $changes = $form->changes($store);
        
        
        $form->fill($store, array('store_id', 'store_name', 'note'));
        if ($isNew) {
            $store->setStoreType($form->getWidgetValue('store_type'));
        }
        
        if (!$store->save()) {
            // exception would also be on it's place
            return false;
        }
        
        $form->getWidget('store_id')->setValue($store->getStoreId());
    }
    
    
    public function deleteStore($storeId) {
        
        $sDao = new StoreDAO();
        $sDao->delete( $storeId );
        
        
    }
    
    public function readArchiveFilesByCompany($companyId) {
        $sfDao = new StoreFileDAO();
        return $sfDao->readArchiveFiles($companyId);
    }
    
    public function readArchiveFilesByPerson($personId) {
        $sfDao = new StoreFileDAO();
        return $sfDao->readArchiveFiles(null, $personId);
    }
    
    public function readFilesByCompany($companyId) {
        $sfDao = new StoreFileDAO();
        return $sfDao->readFilesByCustomer($companyId);
    }
    
    public function readFilesByPerson($personId) {
        $sfDao = new StoreFileDAO();
        return $sfDao->readFilesByCustomer(null, $personId);
    }
    
    public function readStoreFiles($storeId) {
        $sfDao = new StoreFileDAO();
        
        return $sfDao->readByStore($storeId);
    }
    
    public function readStoreFile($storeFileId) {
        $sfDao = new StoreFileDAO();
        $sf = $sfDao->read($storeFileId);
        
        if (!$sf)
            return null;
        
        $sfrDao = new StoreFileRevDAO();
        $revs = $sfrDao->readByFile($sf->getStoreFileId());
        $sf->setRevisions($revs);
        
        $lastRev = $sf->getLastRevision();
        if ($lastRev) {
            $sf->setField('md5sum', $lastRev->getMd5sum());
            $sf->setField('filesize', $lastRev->getFilesize());
        }
        
        return $sf;
    }

    public function readStoreFileByRev($storeFileRevId) {
        $sfrDao = new StoreFileRevDAO();
        $sfr = $sfrDao->read( $storeFileRevId );
        
        return $this->readStoreFile( $sfr->getStoreFileId() );
    }
    
    public function readStoreFileByPath($storeId, $path) {
        $sfDao = new StoreFileDAO();
        
        $sf = $sfDao->readByPath($storeId, $path);
        if ($sf) {
            return $this->readStoreFile($sf->getStoreFileId());
        }
        
        return null;
    }
    
    
    public function deleteFile($storeFileId) {
        $f = $this->readStoreFile($storeFileId);
        
        $sfmDao = new StoreFileMetaDAO();
        $sfmDao->deleteByFile($storeFileId);
        
        $sfrDao = new StoreFileRevDAO();
        $sfrDao->deleteByFile($f->getStoreFileId());
        
        $sfDao = new StoreFileDAO();
        $sfDao->delete($f->getStoreFileId());
        
        foreach($f->getRevisions() as $r) {
            $file = get_data_file('filesync/'.$f->getStoreId().'/'.$f->getStoreFileId().'-'.$r->getStoreFileRevId());
            
            if ($file) {
                unlink($file);
            }
            
            // possibly preview-files generated
            if (file_exists( $file . '-preview.pdf' )) {
                unlink( $file . '-preview.pdf' );
            }
            
        }
        
    }
    
    public function deleteStoreFileRev($storeFileRevId) {
        $storeFile = $this->readStoreFileByRev($storeFileRevId);
        
        $revisions = $storeFile->getRevisions();
        
        if (count($revisions) == 1) {
            $this->deleteFile($storeFile->getStoreFileId());
        } else {
            $rev = null;
            foreach($revisions as $r) {
                if ($r->getStoreFileRevId() == $storeFileRevId) {
                    $rev = $r;
                    break;
                }
            }
            
            if ($r == null) {
                throw new InvalidStateException('Revision not found');
            }
            
            $sfrDao = new StoreFileRevDAO();
            $sfrDao->delete( $rev->getStoreFileRevId());
            
            $file = get_data_file('filesync/'.$storeFile->getStoreId().'/'.$storeFile->getStoreFileId().'-'.$rev->getStoreFileRevId());
            unlink( $file );
        }
        
    }
    
    
    public function readFilemeta($storeFileId) {
        $sfDao = new StoreFileDAO();
        $sf = $sfDao->read($storeFileId);
        
        if ($sf == null)
            return null;
        
        $form = new StoreFileMetaForm();
        $form->bind($sf);
        
        $sfmDao = new StoreFileMetaDAO();
        $sfm = $sfmDao->readByFile($storeFileId);
        
        if ($sfm)
            $form->bind($sfm);
        
        return $form;
    }
    
    
    public function saveFilemeta(StoreFileMetaForm $form) {
        
        
        $storeFileId = $form->getWidgetValue('store_file_id');
        if ($storeFileId) {
            $sfmDao = new StoreFileMetaDAO();
            $sfm = $sfmDao->readByFile($storeFileId);
        }
        
        if ($sfm == null) {
            $sfm = new StoreFileMeta();
            $sfm->setStoreFileId($storeFileId);
        }
        
        
        $form->fill($sfm, array('store_file_id', 'customer_id', 'subject', 'long_description', 'document_date', 'public'));
        
        if ($sfm->getCompanyId() == 0) {
            $sfm->setCompanyId( null );
        }
        if ($sfm->getPersonId() == 0) {
            $sfm->setPersonId( null );
        }
        
        
        if ($sfm->getPublic()) {
            $sfm->setPublicSecret( md5(rand().rand().rand().rand().rand().rand().rand()) );
        }
        else {
            $sfm->setPublicSecret('');
        }
        
        
        if (!$sfm->save()) {
            // exception would also be on it's place
            return false;
        }
        
        
        // file-field attached & file uploaded? => sync
        if ($form->getWidget('file') && isset($_FILES['file']['size']) && $_FILES['file']['size']) {
            $sf = $this->readStoreFile( $sfm->getStoreFileId() );
            
            $this->syncFile($sf->getStoreId(), $sf->getPath(), md5_file($_FILES['file']['tmp_name']), filesize($_FILES['file']['tmp_name']), date('Y-m-d H:i:s'), false, $_FILES['file']['tmp_name']);
        }
    }
    
    
    
    
    public function searchFile($start, $limit, $opts) {
        $sfDao = new StoreFileDAO();
        
        $cursor = $sfDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('store_id', 'rev', 'store_name', 'company_id', 'company_name', 'person_id', 'firstname', 'insert_lastname', 'lastname', 'document_date', 'edited', 'created', 'subject', 'document_date', 'long_description', 'filesize', 'store_file_id', 'path', 'deleted', 'encrypted', 'lastmodified', 'public'));
        
        $objects = $r->getObjects();
        foreach($objects as &$obj) {
            $obj['customer_name'] = format_customername($obj);
            $obj['filesize_text'] = format_filesize($obj['filesize']);
        }
        $r->setObjects($objects);
        
        return $r;
    }
    
    
    public function markDeleted($storeId, $file) {
        $file = trim($file);
        $file = str_replace("\\", '/', $file);

        if (!$file)
            return;
        
        $sfDao = new StoreFileDAO();
        $sfDao->markDeleted($storeId, $file);
        
        $this->updateLastFileChange($storeId);
    }
    
    
    protected function updateLastFileChange($storeId) {
        $sDao = new StoreDAO();
        
//         $sDao->updateLastFileChange($storeId, round(microtime(true)*1000));
        $sDao->updateLastFileChange($storeId, time());
    }
    
    
    public function syncFile($storeId, $path, $md5sum, $filesize, $lastmodified, $encrypted, $tmpfile) {
        
        $store = $this->readStore($storeId);
        
        if ($store == null) {
            throw new InvalidStateException('Store not found');
        }
        
        
        $path = str_replace('\\', '/', $path);
        if ($store->getStoreType() == 'archive') {
            $path = date('Ymd-His') . '-' . basename($path);
        }
        
        $storeFile = $this->readStoreFileByPath($store->getStoreId(), $path);
        
        if ($storeFile == null) {
            $storeFile = new StoreFile();
            $storeFile->setStoreId($store->getStoreId());
            $storeFile->setPath($path);
        }
        
        $storeFile->setDeleted(false);
        
        $storeFile->save();
        
        $sfrDao = new StoreFileRevDAO();
        $lastRev = $sfrDao->readLastRevision($storeFile->getStoreFileId());
        if ($lastRev != null && $lastRev->getMd5sum() == $md5sum && $lastRev->getFilesize() == $filesize) {
            $fe = new FilesyncException('File already synced and @ top');
            $fe->setStoreFileId( $storeFile->getStoreFileId() );
            throw $fe;
        }
        
        $nextRev = 1;
        if ($lastRev != null) {
            $nextRev = $lastRev->getRev() + 1;
        }
        
        
        $sfr = new StoreFileRev();
        $sfr->setStoreFileId($storeFile->getStoreFileId());
        $sfr->setFilesize($filesize);
        $sfr->setMd5sum($md5sum);
        $sfr->setRev($nextRev);
        $sfr->setEncrypted($encrypted);
        $sfr->setLastmodified($lastmodified);
        $sfr->save();
        
        $sfDao = new StoreFileDAO();
        $sfDao->setRevision($storeFile->getStoreFileId(), $sfr->getRev());
        
        $ctx = \core\Context::getInstance();
        
        $destDir = $ctx->getDataDir() . '/filesync/'.$store->getStoreId().'/';
        if (is_dir($destDir) == false) {
            mkdir($destDir, 0755, true);
        }
        
        if (!copy($tmpfile, $ctx->getDataDir() . '/filesync/'.$store->getStoreId().'/'.$storeFile->getStoreFileId().'-'.$sfr->getStoreFileRevId())) {
            throw new FileException('Error saving file');
        }
        
        $this->updateLastFileChange($store->getStoreId());
        
        if ($store->getStoreType() == 'archive') {
            $sfm = new StoreFileMeta();
            $sfm->setStoreFileId($storeFile->getStoreFileId());
            $sfm->setDocumentDate(date('Y-m-d'));
            $sfm->save();
        }
        
        return $this->readStoreFile($storeFile->getStoreFileId());
    }
    
    
    public function autocomplete( $storeId, $term ) {
        $sfDao = object_container_get( StoreFileDAO::class );
        $paths = $sfDao->autocomplete( $storeId, $term );
        
        $r = array();
        foreach($paths as $p) {
            if (strpos($p, '/') === false) continue;
            
            $tokens = explode('/', $p);
            for($x=0; $x < count($tokens); $x++) {
                
                $p2 = '';
                
                for ($y=0; $y <= $x && $y < count($tokens)-1; $y++) {
                    $p2 .= $tokens[$y] . '/';
                }
                
                $r[] = $p2;
            }
        }
        
        $r = array_unique($r);
        
        return $r;
    }
    
    
    public function statisticsStore($storeId) {
        $r = array();
        
        $sfrDao = new StoreFileRevDAO();
        
        $r['size_all_files'] = $sfrDao->getStoreSize($storeId);
        $r['size_active_files'] = $sfrDao->getStoreSizeActiveFiles($storeId);
        
        return $r;
    }
    
    
    public function saveArchiveFile(ArchiveFileUploadForm $form) {
        $storeId = $form->getWidgetValue('store_id');
        $store = $this->readStore($storeId);
        
        if (!$store) {
            throw new ObjectNotFoundException('Store not found');
        }
        
        // save file
        $filename     = $_FILES['file']['name'];
        $md5          = md5_file($_FILES['file']['tmp_name'], false);
        $filesize     = $_FILES['file']['size'];
        $lastmodified = date('Y-m-d H:i:s');
        
        $storeFile = $this->syncFile( $storeId, $filename, $md5, $filesize, $lastmodified, false, $_FILES['file']['tmp_name'] );
        
        
        // save meta
        $sfmDao = new StoreFileMetaDAO();
        $sfm = $sfmDao->readByFile($storeFile->getStoreFileId());
        $form->fill($sfm, array('store_file_id', 'subject', 'long_description', 'document_date'));
        $customer_id = $form->getWidgetValue('customer_id');
        
        $sfm->setCompanyId(null);
        $sfm->setPersonId(null);
        
        if (strpos($customer_id, 'company-') === 0) {
            $sfm->setCompanyId((int)str_replace('company-', '', $customer_id));
        }
        if (strpos($customer_id, 'person-') === 0) {
            $sfm->setPersonId((int)str_replace('person-', '', $customer_id));
        }
        $sfm->save();
        
        return $storeFile;
    }
    
    
    
    public function saveStoreFile(StoreFileUploadForm $form) {
        $storeId = $form->getWidgetValue('store_id');
        $store = $this->readStore($storeId);
        
        if (!$store) {
            throw new ObjectNotFoundException('Store not found');
        }
        
        // save file
        $filename     = $_FILES['file']['name'];
        $md5          = md5_file($_FILES['file']['tmp_name'], false);
        $filesize     = $_FILES['file']['size'];
        $lastmodified = date('Y-m-d H:i:s');
        
        
        $path = '';
        $tokens = explode('/', str_replace('\\', '/', $form->getWidgetValue('path')));
        foreach($tokens as $t) {
            if (trim($t)) {
                
                $path = $path . trim($t) . '/';
            }
        }
        
        $storeFile = $this->syncFile( $storeId, $path . $filename, $md5, $filesize, $lastmodified, false, $_FILES['file']['tmp_name'] );
        
        // save meta
        $sfmDao = new StoreFileMetaDAO();
        $sfm = $sfmDao->readByFile($storeFile->getStoreFileId());
        if (!$sfm) {
            $sfm = new StoreFileMeta();
            $sfm->setStoreFileId($storeFile->getStoreFileId());
            $sfm->setDocumentDate(date('Y-m-d'));
        }
        
        $form->fill($sfm, array('store_file_id', 'subject', 'long_description', 'document_date'));
        $customer_id = $form->getWidgetValue('customer_id');
        
        $sfm->setCompanyId(null);
        $sfm->setPersonId(null);
        
        if (strpos($customer_id, 'company-') === 0) {
            $sfm->setCompanyId((int)str_replace('company-', '', $customer_id));
        }
        if (strpos($customer_id, 'person-') === 0) {
            $sfm->setPersonId((int)str_replace('person-', '', $customer_id));
        }
        $sfm->save();
        
        return $storeFile;
    }
    
    
    public function logPublicDownload($storeFileId) {
        $dl = new StoreFileDownloadLog();
        $dl->setStoreFileId( $storeFileId );
        $dl->setIp( remote_addr() );
        
        $dl->setDump(var_export([
            'request' => $_REQUEST,
            'server' => $_SERVER
        ], true));
        
        $dl->save();
        
        return $dl;
    }
    
}

