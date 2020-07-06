<?php



use core\controller\BaseController;
use core\forms\lists\ListResponse;
use filesync\form\StoreForm;
use filesync\model\Store;
use filesync\service\StoreService;
use core\exception\ObjectNotFoundException;

class storeController extends BaseController {
    
    public function init() {
        checkCapability('filesync', 'manager');
    }
    
    
    public function action_index() {
        
        
        $this->render();
    }
    
    public function action_search() {
        
        $storeService = $this->oc->get(StoreService::class);
        
        $stores = $storeService->readAllStores();
        
        $list = array();
        foreach($stores as $s) {
            $list[] = $s->getFields(array('store_id', 'store_type', 'store_name', 'edited', 'created'));
        }
        
        $lr = new ListResponse(0, count($stores), count($stores), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }
    
    public function action_edit() {
        $id = isset($_REQUEST['store_id'])?(int)$_REQUEST['store_id']:0;
        
        $storeService = $this->oc->get(StoreService::class);
        if ($id) {
            $store = $storeService->readStore($id);
        } else {
            $store = new Store();
        }
        
        
        $storeForm = new StoreForm();
        $storeForm->bind($store);
        
        if (is_post()) {
            $storeForm->bind($_REQUEST);
            
            if ($storeForm->validate()) {
                $storeService->saveStore($storeForm);
                
                redirect('/?m=filesync&c=store');
            }
        }
        
        
        $this->isNew = $store->isNew();
        $this->form = $storeForm;
        
        
        $this->render();
        
        
        
        
    }
    
    public function action_delete() {
        $store = null;
        
        $id = get_Var('store_id');
        $storeService = $this->oc->get(StoreService::class);
        if ($id) {
            $store = $storeService->readStore($id);
        }
        
        if ($store == null) {
            throw new ObjectNotFoundException('Store not found');
        }
        
        $storeService->deleteStore($store->getStoreId());
        
        redirect('/?m=filesync&c=store');
    }
    
    
    public function action_maintenance() {
        $id = isset($_REQUEST['store_id'])?(int)$_REQUEST['store_id']:0;
        
        $storeService = $this->oc->get(StoreService::class);
        $store = $storeService->readStore($id);
        
        if (!$store) {
            throw new ObjectNotFoundException('Store not found');
        }
        
        $this->store = $store;
        $this->statisticsStore = $storeService->statisticsStore( $store->getStoreId() );
        
        
        
        $this->render();
    }
    
}

