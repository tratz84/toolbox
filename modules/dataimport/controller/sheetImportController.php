<?php


use core\controller\BaseController;
use dataimport\container\DataImportFormContainer;
use dataimport\form\UploadSheetForm;
use core\exception\FileException;
use core\forms\ListFormWidget;
use core\forms\ListEditWidget;
use core\forms\HiddenField;

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
        $difc = object_container_create( DataImportFormContainer::class );
        
        $this->dif = $dif = $difc->getForm( get_var('uid') );
        
        // get file
        $f = get_data_file_safe('dataimport', get_var('f'));
        if (!$f)
            throw new FileException( 'File not found' );
        
        $m = $this->mapOptions();
        var_export($m);exit;
        
        return $this->render();
    }
    
    
    public function mapOptions() {
        $map = array();
        $form = object_container_create( $this->dif['formClass'] );
        
        $widgets = $form->getWidgetsRecursive( ['include_lists' => true] );
        foreach($widgets as $w) {
            if (is_a($w, HiddenField::class) || in_array($w->getName(), ['edited', 'created']))
                continue;
            
            if (is_a($w, ListFormWidget::class)) {
                $subform = object_container_create($w->getFormClass());
                
                $subprio = 0.01;
                foreach( $subform->getWidgetsRecursive() as $w2 ) {
                    if (is_a($w2, HiddenField::class))
                        continue;
                    
                    $map[ ] = array(
                        'name'    => $w->getName() . '.' . $w2->getName() 
                        , 'label' => $w->getLabel() . ' - ' . $w2->getLabel()
                        , 'prio'  => ($w->getPrio()+$subprio)
                        , 'list'  => true
                    );
                    
                    $subprio += 0.01;
                }
                
            }
            else if (is_a($w, ListEditWidget::class)) {
                
            }
            else {
                $map[ $w->getName() ] = array(
                    'name'    => $w->getName()
                    , 'label' => $w->getLabel()
                    , 'prio'  => $w->getPrio()
                    , 'list'  => false
                );
            }
        }
        
        usort($map, function($o1, $o2) {
            return $o1['prio'] - $o2['prio'];
        });
        
        return $map;
    }
    
    
}

