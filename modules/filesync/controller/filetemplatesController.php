<?php


use core\controller\BaseController;
use core\container\ArrayContainer;
use base\service\SettingsService;
use core\exception\InvalidStateException;
use filesync\service\StoreService;
use core\exception\ObjectNotFoundException;

class filetemplatesController extends BaseController {
    
    
    public function action_index() {
        
        $ac = filesync_filetemplates();
        
        $this->filetemplates = $ac;
        
        return $this->render();
    }
    
    
    public function action_link_template_to_file() {
        $ac = filesync_filetemplates();
        
        $template_id = get_var('template_id');          // this is a string!
        $store_file_id = (int)get_var('store_file_id');
        
        // check template_id
        $found = false;
        for($x=0; $x < $ac->count(); $x++) {
            $ft = $ac->get( $x );
            if ($template_id == $ft->getId()) {
                $found = true;
                break;
            }
        }
        if ($found == false) {
            throw new ObjectNotFoundException('Invalid template_id');
        }
        
        
        // check store_file_id
        $storeService = object_container_get( StoreService::class );
        $storeFile = $storeService->readStoreFile( $store_file_id );
        if ($storeFile == null) {
            throw new ObjectNotFoundException('Invalid storeFile');
        }
        // TODO: check if storeType == share?
        
        
        // save
        $settingsService = object_container_get( SettingsService::class );
        $settingsService->updateValue('filetemplate__'.$template_id, $store_file_id);
        
        $this->json([
            'success' => true
        ]);
    }
    
    
    public function action_unlink_template() {
        $ac = filesync_filetemplates();
        
        $template_id = get_var('template_id');          // this is a string!
        
        // check template_id
        $found = false;
        for($x=0; $x < $ac->count(); $x++) {
            $ft = $ac->get( $x );
            if ($template_id == $ft->getId()) {
                $found = true;
                break;
            }
        }
        if ($found == false) {
            throw new ObjectNotFoundException('Invalid template_id');
        }
        
        
        $settingsService = object_container_get( SettingsService::class );
        $settingsService->deleteSetting( 'filetemplate__'.$template_id );
        
    }
    
    
    
    
}

