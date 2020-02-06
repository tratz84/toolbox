<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use core\parser\SheetReader;
use payment\form\PaymentImportMappingForm;

class stageController extends BaseController {
    
    
    
    
    public function action_index() {
        
        
        
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
        
        $this->form = new PaymentImportMappingForm();
        $this->form->setImportHeaders( $head );
        
        $this->tmpfile = basename($fullpath);
        
        
    }
    
    
}


