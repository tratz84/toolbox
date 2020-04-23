<?php



use core\controller\BaseController;
use filesync\service\StoreService;
use core\exception\ObjectNotFoundException;
use filesync\form\ArchiveFileUploadForm;
use core\forms\SelectField;

class archiveController extends BaseController {
    
    
    public function action_upload() {
        $storeService = $this->oc->get(StoreService::class);
        
        $this->store = $storeService->readStore(get_var('store_id'));
        if (!$this->store) {
            throw new ObjectNotFoundException('Store not found');
        }
        
        $this->form = new ArchiveFileUploadForm();
        $this->form->getWidget('store_id')->setValue($this->store->getStoreId());
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                $storeService->saveArchiveFile($this->form);
                
                redirect('/?m=filesync&c=storefile&id='.$this->store->getStoreId());
            }
        }
        
        
        return $this->render();
    }
    
    
    
    public function action_popup() {
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    public function action_popup_new_file( ){
        
        $this->form = new ArchiveFileUploadForm();
        $this->form->hideSubmitButtons();
        
        $this->form->removeWidget('store_id');
        
//         public function __construct($name, $value=null, $optionItems=array(), $label=null, $opts=array()) {
        $mapStores = mapArchiveStores();
        
        $storeId = get_var('store_id');
        $selectStoreId = new SelectField('store_id', $storeId, $mapStores, 'Store');
        $selectStoreId->setPrio(5);
        $this->form->addWidget($selectStoreId);
        
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    
    
}
