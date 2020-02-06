<?php

namespace core\forms;

use core\db\DBObject;

class ListFormWidget extends ListWidget {
    
    
    protected $objects = array();
    
    protected $formClass;
    protected $fields = array();                    // fields are the fields that are shown in the table
    protected $publicFields = array();              // public fields are the fields that may be visible to the user (ie through json API's in the future)
    protected $fieldLabels = array();
    protected $sortable = false;
    protected $readonly = false;
    
    /**
     * @param $formClass            - full classname of form (incl. namespace)
     * @param $methodObjectList     - variable name or getter (without the 'get') for fetching list
     */
    public function __construct($formClass, $methodObjectList, $fields = null) {
        $this->setName($methodObjectList);
        
        $this->formClass = $formClass;
        $this->methodObjectList = $methodObjectList;
        $this->fields = $fields;
        
        $this->initDefaultRenderFunctions();
    }
    
    public function setReadonly($bln) { $this->readonly = $bln ? true : false; }
    
    public function getFormClass() { return $this->formClass; }
    
    protected function initDefaultRenderFunctions() {
        
        $this->arrRenderFunctions['start'] = function($record) {
            $t = date2unix( $record->getField('start') );
            if ($t)
                return date('d-m-Y', $t);
            return '';
        };
        $this->arrRenderFunctions['end'] = function($record) {
            $t = date2unix( $record->getField('end') );
            if ($t)
                return date('d-m-Y', $t);
            return '';
        };
        $this->arrRenderFunctions['price'] = function($record) {
            return format_price( $record->getField('price'), true, array('thousands' => '.') );
        };
        
    }
    
    public function setFieldLabels($l) { $this->fieldLabels = $l; }
    public function setFields($arr) { $this->fields = $arr; }
    public function setPublicFields($arr) { $this->publicFields = $arr; }
    public function getObjects() { return $this->objects; }
    
    public function getHiddenFields() { return array_unique( array_merge($this->fields, $this->publicFields) ); }
    
    public function setSortable($bln) { $this->sortable = $bln ? true : false; }
    public function getSortable() { return $this->sortable; }
    
    
    public function bindList($arrObjects) {
        $this->objects = $arrObjects;
    }
    
    public function bind($obj) {
        $this->objects = $this->retrieveObjects($obj);
    }
    
    
    protected function getValueObject($obj, $fieldName) {
        
        if (isset($this->arrRenderFunctions[$fieldName])) {
            return $this->arrRenderFunctions[$fieldName]($obj);
        }
        
        $func = 'get'.dbCamelCase($fieldName);
        if (method_exists($obj, $func)) {
            $v = $obj->$func();
        } else if (is_a($obj, DBObject::class)) {
            $v = $obj->getField($fieldName);
        } else if (is_array($obj)) {
            $v = $obj[$fieldName];
        } else {
            
        }
        
        return $v;
    }
    
    
    public function asArray($opts=array()) {
        $r = array();
        
        $fields = array_merge( $this->fields, $this->publicFields );
        $fields = array_unique($fields);
        foreach($this->getObjects() as $o) {
            $record = array();
            
            foreach($fields as $f) {
                $record[$f] = $this->getValueObject($o, $f);
            }
            
            $r[] = $record;
        }
        return $r;
    }

    
    public function renderAsText() {
        $this->setReadonly(true);
        
        return $this->render();
    }
    
    public function render() {
        
        // list of publicFields (data used for form row editor)
        $publicFields = array_merge($this->fields, $this->publicFields);
        $publicFields = array_unique($publicFields);
        
        
        $html = '<div class="widget list-form-widget '.($this->readonly?'list-form-widget-readonly':'').' '.slugify($this->formClass).'-list-form-widget">';
        
        $html .= '<input type="hidden" class="fields" value="'.esc_attr(json_encode($this->fields)).'" />';
        $html .= '<input type="hidden" class="public-fields" value="'.esc_attr(json_encode($this->publicFields)).'" />';
        $html .= '<input type="hidden" class="method-object-list" value="'.esc_attr($this->methodObjectList).'" />';
        $html .= '<input type="hidden" class="form-class" value="'.esc_attr($this->formClass).'" />';
        
        if ($this->getLabel())
            $html .= '<h2 class="clearfix">'.esc_html($this->getLabel()).'</h2>';
        
        $html .= '<table class="sublist">';
        
        if (count($this->fieldLabels)) {
            $html .= '<thead>';
            
            if ($this->getSortable()) {
                $html .= '<th class="th-sortable sortable-cell"></th>';
            }
            
            foreach($this->fieldLabels as $l) {
                $html .= '<th>'.esc_html($l).'</th>';
            }
            
            $html .= '<th class="actions"></th>';
            
            $html .= '</thead>';
        }
        
        $html .= '<tbody class="list-form-widget-'.slugify($this->methodObjectList).' '.($this->getSortable()?'sortable-container':'').'">';
        if (count($this->objects)) {
            for($rowCount=0; $rowCount < count($this->objects); $rowCount++) {
                $o = $this->objects[$rowCount];
                
                if ($this->fields == null || count($this->fields) == 0) {
                    if (is_a($o, DBObject::class)) {
                        $this->fields = array_keys( $o->getFields() );
                    } else {
                        $this->fields = array_keys( $o );
                    }
                }
                
                $html .= '<tr class="'.($this->readonly?'':'clickable').'">';
                if ($this->getSortable()) {
                    $html .= '<td class="th-sortable sortable-cell"><span class="fa fa-sort handler-sortable"></span></td>';
                }
                if ($this->fields && count($this->fields)) {
                    
                    for($fieldNo=0; $fieldNo < count($this->fields); $fieldNo++) {
                        $f = $this->fields[$fieldNo];
                        
                        $v = $this->getValueObject($o, $f);
                        
                        
                        $html .= '<td class="list-field">';
                        
                        if ($fieldNo == 0) {
                            foreach($this->getHiddenFields() as $pb) {
                                $html .= '<input type="hidden" name="'.$this->methodObjectList.'['.$rowCount.']['.$pb.']" value="'.esc_attr($this->getValueObject($o, $pb)).'" />';
                            }
                        }
                        
                        
                        $html .= '<span class="field-value" data-fieldname="'.esc_attr($f).'">'.$v.'</span>';
                        
                        $html .= '</td>';
                    }
                }
                
                $html .= '<td class="actions">';
                $html .= '<a class="fa fa-pencil row-edit" href="javascript:void(0);"></a>';
                $html .= '<a class="fa fa-trash row-delete" href="javascript:void(0);"></a>';
                $html .= '</td>';
                
                $html .= '</tr>';
            }
            
        } else {
            $html .= '<tr class="empty-list"><td colspan="'.(count($this->fieldLabels)+2).'">'.t('Empty').'</td></tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        
        $html .= '<div class="add-entry-container"><a class="add-record" href="javascript:void(0);">'.t('Add line').'</a></div>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    
    public function changes(DBObject $obj) {

        $fields = array_merge( $this->fields, $this->publicFields );
        $fields = array_unique($fields);
        
        
        $objects = $this->retrieveObjects($obj);
        
        $changes = array();
        
        $old = array();
        foreach($objects as $o) {
            $vals = array();
            foreach($fields as $f) {
                $vals[$f] = $o->getField($f); 
            }
            $old[] = $vals;
        }
        $new = array();
        foreach($this->objects as $o) {
            $vals = array();
            foreach($fields as $f) {
                $vals[$f] = $this->getValueObject($o, $f);
            }
            $new[] = $vals;
        }
        
        for($x=0; $x < count($old) && $x < count($new); $x++) {
            foreach($old[$x] as $f => $v) {
                if ($new[$x][$f] == $v) {
                    unset($old[$x][$f]);
                    unset($new[$x][$f]);
                }
            }
        }
        
        $changes = array();
        for($x=0; $x < max(count($old), count($new)); $x++) {
            if (($x < count($old) && count($old[$x])) || ($x < count($new) && count($new[$x]))) {
                $changes[] = array(
                    'old' => isset($old[$x]) ? $old[$x] : array(),
                    'new' => isset($new[$x]) ? $new[$x] : array()
                );
            }
        }
        
        
        if (count($changes)) {
            return array($this->methodObjectList => $changes);
        } else {
            return array();
        }
    }
    
    
}
