<?php


use core\controller\BaseController;
use core\exception\ObjectNotFoundException;
use filesync\service\StoreService;
use core\exception\InvalidStateException;

class apiController extends BaseController {
    
    
    public function action_liststores() {
        $storeService = $this->oc->get(StoreService::class);
        
        $stores = $storeService->readAllStores();
        
        $r = array();
        
        $r['stores'] = array();
        foreach($stores as $s) {
            $r['stores'][] = $s->asArray();
        }
        
        $this->json($r);
    }
    
    
    public function action_request_store() {
        $storeService = $this->oc->get(StoreService::class);
        
        $jsonData = json_decode($_POST['jsonData']);
        
        $storeName = @$jsonData->params->storeName;
        $store = $storeService->readStoreByName($storeName);
        if (!$store) {
            $this->json(array('error' => 'Store not found'));
            exit;
        }
        
        $r = array();
        $r['store'] = $store->asArray();
        
        $r['files'] = array();
        $files = $storeService->readStoreFiles( $store->getStoreId() );
        foreach($files as $f) {
            $r['files'][] = $f->asArray();
        }
        
        $this->json($r);
    }
    
    
    public function action_request_file() {
        $storeService = $this->oc->get(StoreService::class);
        
        $jsonData = json_decode($_POST['jsonData']);
        
        $storeName = @$jsonData->params->storeName;
        $store = $storeService->readStoreByName($storeName);
        
        if (!$store) {
            $this->json(array('error' => 'Store not found'));
            exit;
        }
        
        
        $r = array();
        $r['store'] = $store->asArray();
        
        $storeFile = $storeService->readStoreFileByPath($store->getStoreId(), $jsonData->params->path);
        if ($storeFile) {
            $r['storeFile'] = $storeFile->asArray();
            
            $revs = array();
            foreach($storeFile->getRevisions() as $rev) {
                $revs[] = $rev->asArray();
            }
            
            $r['revisions'] = $revs;
        } else {
            $r['storeFile'] = null;
        }
        
        
        $this->json($r);
    }
    
    
    public function action_download() {
        $storeService = $this->oc->get(StoreService::class);
        
        $jsonData = json_decode($_POST['jsonData']);
        
        $storeName = @$jsonData->params->storeName;
        $store = $storeService->readStoreByName($storeName);
        
        if (!$store) {
            $this->json(array('error' => 'Store not found'));
            exit;
        }
        
        $storeFile = $storeService->readStoreFileByPath($store->getStoreId(), @$jsonData->params->path);
        $rev = $storeFile->getLastRevision();
        
        $file = get_data_file('/filesync/'.$storeFile->getStoreId() . '/' . $storeFile->getStoreFileId() . '-' . $rev->getStoreFileRevId());
        
        if (!$file) {
            throw new ObjectNotFoundException('File not found');
        }
        
        header('Content-type: ' . mime_content_type ($file));
        header('Content-Disposition: '.(get_var('inline')?'inline':'attachment').'; filename="'.$storeFile->getFilename().'"');
        
        
        readfile($file);
    }
    
    
    
    public function action_mark_deleted() {
        $storeService = $this->oc->get(StoreService::class);
        
        $jsonData = json_decode($_POST['jsonData']);
        
        $storeName = @$jsonData->params->storeName;
        $store = $storeService->readStoreByName($storeName);
        
        if (!$store) {
            $this->json(array('error' => 'Store not found'));
            exit;
        }
        
        foreach(@$jsonData->params->files as $file) {
            $storeService->markDeleted($store->getStoreId(), $file);
        }
        
        $arr = array();
        $arr['success'] = true;
        $arr['store'] = $store->asArray();
        
        $this->json($arr);
    }
    
    
    public function action_upload() {
        try {
            $storeService = $this->oc->get(StoreService::class);
            
            $jsonData = json_decode($_POST['jsonData']);
            
            $storeName = @$jsonData->params->storeName;
            $store = $storeService->readStoreByName($storeName);
            
            if (!$store) {
                $this->json(array('error' => 'Store not found'));
                exit;
            }
            
            $path = @$jsonData->params->path;
            if (!$path) {
                throw new InvalidStateException('No path given');
            }
            
            $md5sum = @$jsonData->params->md5sum;
            if (!$md5sum) {
                throw new InvalidStateException('No checksum given');
            }
        
            $filesize = @$jsonData->params->filesize;
            $lastmodified = date('Y-m-d H:i:s', @$jsonData->params->lastmodified/1000);
            $encrypted = false;
            
            $storefile = $storeService->syncFile($store->getStoreId(), $path, $md5sum, $filesize, $lastmodified, $encrypted, @$_FILES['file']['tmp_name']);
            
            $arr = array();
            $arr['store'] = $store->asArray();
            $arr['storeFile'] = $storefile->asArray();
            $this->json($arr);
            
        } catch (\Exception $ex) {
            $arr = array();
            $arr['error'] = $ex->getMessage();
            $this->json($arr);
        }
    }
    
    
}

