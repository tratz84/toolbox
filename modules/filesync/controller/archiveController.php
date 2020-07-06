<?php



use core\controller\BaseController;
use filesync\service\StoreService;
use core\exception\ObjectNotFoundException;
use filesync\form\ArchiveFileUploadForm;
use core\forms\SelectField;
use customer\service\CustomerService;

class archiveController extends BaseController {
    
    public function init() {
//         checkCapability('filesync', 'manager');
    }
    
    
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
                $storeFile = $storeService->saveArchiveFile($this->form);
                
                if (get_var('r') == 'json') {
                    return $this->json([
                        'success'     => true,
                        'storeFileId' => $storeFile->getStoreFileId(),
                        'path'        => $storeFile->getPath(),
                    ]);
                }
                
                redirect('/?m=filesync&c=storefile&id='.$this->store->getStoreId());
            }
        }

        // json response?
        if (get_var('r') == 'json') {
            $errors = array();
            foreach($this->form->getErrors() as $field => $val) {
                foreach($val as $msg) {
                    $errors[$field] = $this->form->getLabelByFieldname($field) . ' - ' . $msg;
                }
            }
            
            return $this->json([
                'success' => false,
                'error'   => true,
                'errors'  => $errors
            ]);
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
        
        if (get_var('document_date')) {
            $this->form->getWidget('document_date')->setValue( get_var('document_date') );
        }
        
        if (get_var('customer_id'))
            $this->form->getWidget('customer_id')->bindObject([ 'customer_id' => get_var('customer_id') ]);
        if (get_var('company_id'))
            $this->form->getWidget('customer_id')->bindObject([ 'customer_id' => 'company-'.get_var('company_id') ]);
        if (get_var('person_id'))
            $this->form->getWidget('customer_id')->bindObject([ 'customer_id' => 'person-'.get_var('person_id') ]);
        
        $this->callback_func = get_var('callback') ? get_var('callback') : 'filesyncArchiveFile_Select';
        
        $this->setShowDecorator(false);
        return $this->render();
    }
    
    
    public function action_file_example() {
        
        $storeService = $this->oc->get(StoreService::class);

        if (isset($this->storeFileId))
            $storeFileId = $this->storeFileId;
        else
            $storeFileId = get_var('storeFileId');
        
        /** @var \filesync\model\StoreFile $storeFile */
        $storeFile = $storeService->readStoreFile( $storeFileId );
        
        if ($storeFile) {
            $this->filename = $storeFile->getFilename();
            $this->file_extension = file_extension( $this->filename );
            $this->file_url = appUrl('/?m=filesync&c=storefile&a=download&inline=1&id='.$storeFile->getStoreFileId());
            
            $storeFileMetaForm = $storeService->readFilemeta( $storeFile->getStoreFileId() );
            
            $this->storeFileData = $storeFileMetaForm->asArray();
            
            // set customer-name
            if ($this->storeFileData['customer_id']) {
                $customerService = object_container_get(CustomerService::class);
                $customer = $customerService->readCustomerStrId( $this->storeFileData['customer_id'] );
                if ($customer) {
                    $this->storeFileData['customer_name'] = $customer->getName();
                }
            }
            
        }
        
        if (!$storeFile) {
            $this->error = 'File not found';
        }
        
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    
    
}
