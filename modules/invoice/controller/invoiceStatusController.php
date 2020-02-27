<?php



use core\controller\BaseController;
use core\forms\lists\ListResponse;
use invoice\form\InvoiceStatusForm;
use invoice\model\InvoiceStatus;
use invoice\service\InvoiceService;

class invoiceStatusController extends BaseController {
    
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
        
        $this->addTitle(strOrder(1) . ' ' . t('states'));
    }
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        if ($id) {
            $invoiceStatus = $invoiceService->readInvoiceStatus($id);
            
            $this->addTitle(t('Edit') . ' ' . strOrder(1) . ' ' . t('state') . ' ' . $invoiceStatus->getDescription());
        } else {
            $invoiceStatus = new InvoiceStatus();
            
            $this->addTitle(t('New') . ' ' . strOrder(1) . ' ' . t('state'));
        }
        
        
        $invoiceStatusForm = new InvoiceStatusForm();
        $invoiceStatusForm->bind($invoiceStatus);
        
        if (is_post()) {
            $invoiceStatusForm->bind($_REQUEST);
            
            if ($invoiceStatusForm->validate()) {
                $invoiceService->saveInvoiceStatus($invoiceStatusForm);
                
                redirect('/?m=invoice&c=invoiceStatus');
            }
            
        }
        
        
        
        $this->isNew = $invoiceStatus->isNew();
        $this->form = $invoiceStatusForm;
        
        
        $this->render();
        
    }
    
    
    
    public function action_search() {
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $invoiceStatus = $invoiceService->readAllInvoiceStatus();
        
        $list = array();
        foreach($invoiceStatus as $is) {
            $list[] = $is->getFields(array('invoice_status_id', 'description', 'active', 'default_selected'));
        }
        
        
        $lr = new ListResponse(0, count($invoiceStatus), count($invoiceStatus), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }
    
    public function action_sort() {
        if (isset($_REQUEST['ids'])) {
            $ids = explode(',', $_REQUEST['ids']);
            
            $is = $this->oc->get(InvoiceService::class);
            $is->updateInvoiceStatusSort($ids);
            
        }
        
        print 'OK';
    }
    
    
    public function action_delete() {
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        $invoiceService->deleteInvoiceStatus($_REQUEST['id']);
        
        redirect('/?m=invoice&c=invoiceStatus');
    }
    
}
