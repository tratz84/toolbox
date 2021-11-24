<?php


namespace dataimport\import;

use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\ListFormWidget;
use core\forms\Select2Field;
use core\forms\SelectField;


class XlsDataImporter {
    
    protected $dif = array();
    protected $path;
    protected $post = array();
    
    protected $mapOptions = null;
    protected $data = null;
    protected $validationErrors = array();
    
    public function __construct( $arrDif, $pathXls ) {
        
        $this->dif = $arrDif;
        $this->path = $pathXls;
        
    }
    
    
    public function setPost( $arrPostData ) {
        $this->post = $arrPostData;
    }
    public function getPostValue( $key, $defaultValue=null ) {
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }
        else {
            return $defaultValue;
        }
    }
    
    public function getData( $sheetNo = null ) {
        if ($this->data !== null) {
            return $this->data;
        }
        
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile( $this->path );
//         $reader->setReadDataOnly( true );
        $spreadsheet = $reader->load( $this->path );
        
//         if ($sheetNo !== null)
//             $spreadsheet->setActiveSheetIndex( $sheetNo );
// var_export($spreadsheet->getActiveSheetIndex());exit;
        $sheet = $spreadsheet->getActiveSheet();
        
        $this->data = $sheet->toArray();
        
        return $this->data;
    }
    
    public function getRow( $rowNo ) {
        $d = $this->getData();
        
        return $d[$rowNo];
    }
    
    
    public function countRowsWithErrors() {
        $c = 0;
        
        foreach($this->validationErrors as $e) {
            if ($e)
                $c++;
        }
        return $c;
    }
    
    
    
    public function render() {
        $arr = $this->getData();
        
        // no records?
        if (count($arr) <= 1) {
            return 'No data';
        }
        
        
        $html = '';
        
        $html .= '<table class="list-response-table">' . PHP_EOL;
        
        
        // header
        $html .= '<thead>' . PHP_EOL;
        $html .= '<tr>';
        $html .= '<td><input type="checkbox" name="import_row_all" '.($this->getPostValue('import_row_all')?'checked=checked':'').' /></td>';
        $mapOptions = $this->mapOptions();
        for($colNo=0; $colNo < count($arr[0]); $colNo++) {
            $fieldName = 'col_'.$colNo;
            $fieldValue = $this->getPostValue( $fieldName );
            
            $sf = new Select2Field($fieldName, $fieldValue, $mapOptions, '');
            
            $html .= '<td>';
            $html .= $sf->render();
            $html .= '</td>';
        }
        $html .= '<td>Validation</td>';
        $html .= '</tr>';
        $html .= '</thead>' . PHP_EOL;
        
        
        
        // show data
        $html .= '<tbody>' . PHP_EOL;
        for( $rowNo=0; $rowNo < count($arr); $rowNo++ ) {
            // skip top
            if ($rowNo == 0) continue;
            
            $row = $arr[ $rowNo ];
            
            // skip empty rows
            $valueCount = 0;
            foreach($row as $c) {
                if ($c)
                    $valueCount++;
            }
            if ($valueCount == 0) continue;
            
            
            $fieldName = 'import_col_'.$rowNo;
            
            $html .= '<tr>' . PHP_EOL;
            $html .= '<td><input type="checkbox" class="import-row" name="'.$fieldName.'" '.($this->getPostValue($fieldName)?'checked=checked':'').' /></td>';
            foreach($row as $col) {
                $html .= "\t".'<td>' . esc_html($col) . '</td>' . PHP_EOL;
            }
            
            if (isset($this->validationErrors[$rowNo])) {
                $html .= "\t" . '<td>' . esc_html($this->validationErrors[$rowNo]) . '</td>' . PHP_EOL;
            }
            else {
                $html .= "\t" . '<td></td>' . PHP_EOL;
            }
            
            $html .= '</tr>' . PHP_EOL;
            
        }
        $html .= '</tbody>' . PHP_EOL;
        $html .= '</table>' . PHP_EOL;
        
        return $html;
    }
    
    
    
    
    
    
    public function mapOptions() {
        if ($this->mapOptions !== null) {
            return $this->mapOptions;
        }
        
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
                            'name'          => $w->getName() . '.' . $w2->getName()
                            , 'label'       => $w->getLabel() . ' - ' . $w2->getLabel()
                            , 'description' => $w->getLabel() . ' - ' . $w2->getLabel()
                            , 'prio'        => ($w->getPrio()+$subprio)
                            , 'bind'        => array( $w->getName(), $w2->getName() )
                            , 'list'        => true
                        );
                        
                        $subprio += 0.01;
                }
                
            }
            else if (is_a($w, ListEditWidget::class)) {
                
            }
            else {
                $map[ $w->getName() ] = array(
                    'name'          => $w->getName()
                    , 'label'       => $w->getLabel()
                    , 'description' => $w->getLabel()
                    , 'prio'        => $w->getPrio()
                    , 'bind'        => array( $w->getName() )
                    , 'list'        => false
                );
            }
        }
        
        usort($map, function($o1, $o2) {
            return $o1['prio'] - $o2['prio'];
        });
        
        $r = array();
        $r[''] = array('description' => t('Make your choice'));
        foreach($map as $m) {
            $r[ $m['name'] ] = $m;
        }
        
        $this->mapOptions = $r;
        
        return $r;
    }
    
    public function getSelectedRows() {
        $rowNos = array();
        
        foreach($this->post as $key => $val) {
            if (strpos($key, 'import_col_') === 0) {
                $no = (int)substr($key, strlen('import_col_'));
                
                $rowNos[] = $no;
            }
        }
        
        return $rowNos;
    }
    
    
    public function row2form( $rowNo ) {
        
        // get record
        $r = $this->getRow( $rowNo );
        
        // 
        $mapOptions = $this->mapOptions();
        
        // bind to array
        $arr = array();
        for($colNo=0; $colNo < count($r); $colNo++) {
            $field = $this->post['col_'.$colNo];
            if (!$field) continue;
            
            $mapping = $mapOptions[$field];
            
            if (count($mapping['bind']) == 1) {
                $k1 = $mapping['bind'][0];
                
                $arr[ $k1 ] = $r[ $colNo ];
            }
            else if (count($mapping['bind']) == 2) {
                $k1 = $mapping['bind'][0];
                $k2 = $mapping['bind'][1];
                
                if (isset($arr[ $k1 ]) == false)
                    $arr[ $k1 ] = array( [] );
                
                $arr[ $k1 ][0][ $k2 ] = $r[ $colNo ];
            }
        }
        
        // create form
        $f = object_container_create( $this->dif['formClass'] );
        $f->bind( $arr );
        
        foreach($arr as $key => $v) {
            $w = $f->getWidget( $key );
            
            if (is_a($w, SelectField::class)) {
                foreach( $w->getOptionItems() as $optKey => $optVal) {
                    if ($v == $optVal) {
                        $w->setValue( $optKey );
                        break;
                    }
                }
            }
            
            if (is_a($w, Select2Field::class)) {
                foreach( $w->getOptionItems() as $optKey => $optArr) {
                    if ($v == $optArr['description']) {
                        $w->setValue( $optKey );
                        break;
                    }
                }
            }
            
        }
        
        
        return $f;
    }
    
    
    public function validate() {
        $errorCount = 0;
        
        $rowNos = $this->getSelectedRows();
        
        foreach($rowNos as $rowNo) {
            $this->validationErrors[$rowNo] = null;
            
            // fetch form
            $f = $this->row2form( $rowNo );
            
            // validate
            if ( $f->validate() == false ) {
                foreach($f->getErrors() as $errKey => $err) {
                    foreach( $err as $errNo => $msg ) {
                        $this->validationErrors[$rowNo] = $errKey .= " - "  .$msg . "\n";
                        $errorCount++;
                    }
                }
            }
            
            // extra validation?
            $validationClass = '\\dataimport\\validator\\'.get_class_shortname( $f ).'Validator';
            if (class_exists($validationClass)) {
                $vo = new$validationClass( $f);
                if ( $vo->validate() == false ) {
                    $this->validationErrors[$rowNo] .= implode("\n", $vo->getErrors());
                    $errorCount++;
                }
            }
        }
        
        return $errorCount;
    }
    
    
    public function import() {
        
        $rowNos = $this->getSelectedRows();
        
        $service = object_container_get( $this->dif['serviceClass'] );
        $saveFunc = $this->dif['saveFunc'];
        
        $count=0;
        foreach($rowNos as $rowNo) {
            // fetch form
            $f = $this->row2form( $rowNo );
            
            @$service->$saveFunc( $f );
            $count++;
        }
        
        return $count;
    }
    
}


