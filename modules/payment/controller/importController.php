<?php


use core\controller\BaseController;
use payment\form\PaymentImportForm;
use payment\service\PaymentImportService;

class importController extends BaseController {
    
    public function init() {
        $this->addTitle(t('Import payments'));
    }
    
    public function action_index() {
        
        $this->form = new PaymentImportForm();
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                // TODO: handle
                $filetype = $this->form->getWidgetValue('filetype');
                
                if ($filetype == 'sheet') {
                    $ext = file_extension($_FILES['file']['name']);
                    $file = copy_data_tmp($_FILES['file']['tmp_name'], 'payment-import-'.date('YmdHis').'.'.$ext);
                    
                    redirect('/?m=payment&c=import/mapping&f='.urlencode(basename($file)));
                }
                
            }
        }
        
        return $this->render();
    }
    
    public function action_search() {
        
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $piService = $this->oc->get(PaymentImportService::class);
        
        $r = $piService->searchImport($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    
    
    
    public function action_delete() {
        $piService = object_container_get(PaymentImportService::class);
        $piService->deleteImport((int)get_var('id'));
        
        redirect('/?m=payment&c=import');
    }
    
}
