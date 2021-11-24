<?php


use core\controller\BaseController;
use core\exception\FileException;
use dataimport\container\DataImportFormContainer;
use dataimport\form\UploadSheetForm;
use dataimport\import\XlsDataImporter;

class sheetImportController extends BaseController {
    
    
    public function action_index() {
        $difc = object_container_create( DataImportFormContainer::class );
        
        $this->dif = $difc->getForm( get_var('uid') );
        
        $this->form = new UploadSheetForm();
        
        if (is_post()) {
            if (isset($_FILES['file']) && $_FILES['file']['size']) {
                $ext = file_extension( $_FILES['file']['name'] );
                
                $p = ctx()->getDataDir() . '/dataimport/';
                if (file_exists($p) == false) {
                    if (mkdir( $p, 0755, true ) == false) {
                        throw new FileException( 'Unable to create dir ' . $p );
                    }
                }
                
                $file = time() . '.' . $ext;
                
                if ( copy($_FILES['file']['tmp_name'], $p . $file ) == false ) {
                    throw new FileException( 'Unable to copy sheet' );
                }
                
                redirect( '/?m=dataimport&c=sheetImport&a=load_file&uid=f5795d83908f1cd4bd832fa14cfc30c4&f='.urlencode($file) );
            }
        }
        
        return $this->render();
    }
    
    
    public function action_load_file() {
        $this->allowImport = false;
        
        $difc = object_container_create( DataImportFormContainer::class );
        
        $this->dif = $difc->getForm( get_var('uid') );
        
        // get file
        $f = get_data_file_safe('dataimport', get_var('f'));
        if (!$f)
            throw new FileException( 'File not found' );
        
        $di = new XlsDataImporter( $this->dif, $f );
        if (is_post()) {
            $di->setPost( $_POST );
            
            if (get_var('validate')) {
                if ( $di->validate() == 0 ) {
                    $this->allowImport = true;
                }
            }
            
            if (get_var('import') && $di->validate() == 0) {
                $count = $di->import();
                
                redirect('/?m=dataimport&c=sheetImport&a=done&cnt='.$count);
            }
        }
        
        $this->di = $di;
        
        return $this->render();
    }
    
    
    public function action_done() {
        
        $this->cnt = (int)get_var('cnt');
        
        return $this->render();
    }
    

    
}

