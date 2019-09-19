<?php


use core\controller\BaseController;
use fastsite\form\MediaFileForm;
use fastsite\service\MediaService;

class uploadController extends BaseController {
    
    
    public function action_index() {
        $this->form =  new MediaFileForm();
        
        $mediaService = $this->oc->get(MediaService::class);
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                $mediaService->saveUpload( $this->form );
                
                redirect('/?m=fastsite&c=media/files');
            }
        }
        
        return $this->render();
    }
    
    
    
    
}

