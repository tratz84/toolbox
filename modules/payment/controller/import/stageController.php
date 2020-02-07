<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\parser\SheetReader;
use payment\form\PaymentImportMappingForm;
use payment\service\PaymentImportService;


class stageController extends BaseController {
    
    
    
    
    public function action_index() {
        $id = get_var('id');
        
        $piService = object_container_get(PaymentImportService::class);
        $this->pi = $piService->readImport($id);
        
        
        
        
        return $this->render();
    }
    
    
    public function action_create() {
        $f = basename(get_var('f'));
        
        $fullpath = get_data_file_safe('/tmp/', $f);
        if ($fullpath == false) {
            throw new InvalidStateException('Sheet file not found');
        }
        
        
        $sr = new SheetReader( $fullpath );
        if ($sr->read() == false) {
            $this->error = 'Unable to read sheet';
            return $this->render();
        }
        
        
        $head = $sr->getRow(0);                         // fetch 1st row (head)
        $uq_sheet = md5( implode(',', $head) );         // unique key for sheet
        
        $f = get_data_file( '/payments/mapping-'.$uq_sheet );
        $mapping = @unserialize( file_get_contents($f) );
        
        if ($mapping == false || is_array($mapping) == false) {
            throw new InvalidStateException('Mapping not found');
        }
        
        
        $piService = object_container_get(PaymentImportService::class);
        $pi = $piService->stageImport( $fullpath, $mapping );
        
        redirect('/?m=payment&c=import/stage&id='.$pi->getPaymentImportId());
    }
    
    public function action_import() {
        
    }
    
    
}


