<?php



use core\controller\BaseController;
use core\forms\lists\ListResponse;
use invoice\form\VatForm;
use invoice\model\Vat;
use invoice\service\InvoiceService;

class vatController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
        
        $this->addTitle( t('Vat tarifs') );
    }
    
    
    public function action_index() {
        
        
        $this->render();
    }
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $invoiceService = $this->oc->get(InvoiceService::class);
        if ($id) {
            $vat = $invoiceService->readVat($id);
            
            $this->addTitle( t('Edit vat tarifs') . ' ' . $vat->getDescription());
        } else {
            $vat = new Vat();
            
            $this->addTitle( t('New vat tarifs') );
        }
        
        
        $vatForm = object_container_create( VatForm::class );
        $vatForm->bind($vat);
        
        if (is_post()) {
            $vatForm->bind($_REQUEST);
            
            if ($vatForm->validate()) {
                $invoiceService->saveVat($vatForm);
                
                redirect('/?m=invoice&c=vat');
            }
            
        }
        
        
        
        $this->isNew = $vat->isNew();
        $this->form = $vatForm;
        
        
        $this->render();
    }
    
    
    
    public function action_search() {
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $vatTarifs = $invoiceService->readAllVatTarifs();
        
        $list = array();
        foreach($vatTarifs as $vt) {
            $list[] = $vt->getFields(array('vat_id', 'description', 'percentage', 'active', 'default_selected', 'visible'));
        }
        
        
        $lr = new ListResponse(0, count($list), count($list), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }
    
    
    public function action_sort() {
        if (isset($_REQUEST['ids'])) {
            $ids = explode(',', $_REQUEST['ids']);
            
            $os = $this->oc->get(InvoiceService::class);
            $os->updateVatSort($ids);
        }
        
        print 'OK';
    }
    
    
    
    public function action_delete() {
        $invoiceService = $this->oc->get(InvoiceService::class);
        
        $invoiceService->deleteVat((int)$_REQUEST['id']);
        
        redirect('//?m=invoice&c=vat');
    }
    
    
}