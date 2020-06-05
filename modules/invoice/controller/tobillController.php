<?php


use core\controller\BaseController;
use invoice\service\InvoiceService;
use invoice\form\ToBillForm;
use invoice\model\ToBill;
use core\exception\ObjectNotFoundException;

class tobillController extends BaseController {
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $r = $invoiceService->searchBillable($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    public function action_edit() {
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        if (get_var('id')) {
            $tobill = $invoiceService->readToBill(get_var('id'));
        } else {
            $tobill = new ToBill();
        }
        
        
        $this->form = new ToBillForm();
        $this->form->bind($tobill);
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                $invoiceService->saveToBill($this->form);
                
                redirect('/?m=invoice&c=tobill');
            }
            
        }
        
        $this->isNew = $tobill->isNew();
        
        $this->render();
    }
    
    public function action_delete() {
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $invoiceService->deleteToBill(get_var('id'));
        
        redirect('/?m=invoice&c=tobill');
    }
    
    
    
    public function action_toggle_paid() {
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $tobill = $invoiceService->readToBill(get_var('id'));
        $tobill->setPaid( $tobill->getPaid() ? 0 : 1 );
        
        $form = new ToBillForm();
        $form->bind($tobill);
        
        $invoiceService->saveToBill($form);
        
        $this->json(
            array('status' => 'OK', 'paid' => $tobill->getPaid())
        );
    }
    
    
}
