<?php



use core\controller\BaseController;
use core\forms\lists\ListResponse;
use invoice\form\OfferStatusForm;
use invoice\model\OfferStatus;
use invoice\service\OfferService;

class offerStatusController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
    }
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $offerService = $this->oc->get(OfferService::class);
        if ($id) {
            $offerStatus = $offerService->readOfferStatus($id);
        } else {
            $offerStatus = new OfferStatus();
        }
        
        
        $offerStatusForm = new OfferStatusForm();
        $offerStatusForm->bind($offerStatus);
        
        if (is_post()) {
            $offerStatusForm->bind($_REQUEST);
            
            if ($offerStatusForm->validate()) {
                $offerService->saveOfferStatus($offerStatusForm);
                
                redirect('/?m=invoice&c=offerStatus');
            }
            
        }
        
        
        
        $this->isNew = $offerStatus->isNew();
        $this->form = $offerStatusForm;
        
        
        $this->render();
        
    }
    
    
    
    public function action_search() {
        $offerService = $this->oc->get(OfferService::class);
        
        $offerStatus = $offerService->readAllOfferStatus();
        
        $list = array();
        foreach($offerStatus as $os) {
            $list[] = $os->getFields(array('offer_status_id', 'description', 'active', 'default_selected'));
        }
        
        
        $lr = new ListResponse(0, count($offerStatus), count($offerStatus), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }
    
    public function action_sort() {
        if (isset($_REQUEST['ids'])) {
            $ids = explode(',', $_REQUEST['ids']);
            
            $os = $this->oc->get(OfferService::class);
            $os->updateOfferStatusSort($ids);
            
        }
        
        print 'OK';
    }
    
    
    public function action_delete() {
        
        $offerService = $this->oc->get(OfferService::class);
        $offerService->deleteOfferStatus($_REQUEST['id']);
        
        redirect('/?m=invoice&c=offerStatus');
    }
    
    
}


