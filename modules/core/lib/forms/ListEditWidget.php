<?php

namespace core\forms;

use core\db\DBObject;



abstract class ListEditWidget extends ListWidget {


    protected $objects = array();

    protected $widgets = array();

    protected $tableHeader = true;
    protected $strNewEntry = 'Add line';
    protected $sortable = true;


    public function __construct($methodObjectList) {
        $this->strNewEntry = t('Add line');
        
        $this->setName($methodObjectList);

        $this->methodObjectList = $methodObjectList;
    }

    public function getObjects() {
        $l = array();

        foreach($this->objects as $o) {
            // default set raw object values
            if (is_a($o, DBObject::class)) {
                $vals = $o->getFields();
            } else {
                $vals = $o;
            }

            // bind values to widgets & get value from there
            foreach($this->widgets as $w) {
                if ($w->bindObject($o) > 0) {
                    $vals[$w->getName()] = $w->getValue();
                }
            }

            $l[] = $vals;
        }

        return $l;
    }


    public function bind($obj) {
        $this->objects = $this->retrieveObjects($obj);
    }

    public function addObject($obj) {
        $this->objects[] = $obj;
    }

    public function asArray($opts=array()) {
        $r = array();
        
        $fields = array_keys($this->widgetNames);
        foreach($this->getObjects() as $o) {
            $record = array();
            
            foreach($fields as $f) {
                $record[$f] = $this->getValueObject($o, $f);
            }
            
            $r[] = $record;
        }
        
        return $r;
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
        } else if (is_array($obj) && isset($obj[$fieldName])) {
            $v = $obj[$fieldName];
        } else {
            $v = $this->getField($fieldName);
        }
        
        return $v;
    }


    public function renderAsText() {
        $html = '';

        $html = '<div class="widget list-edit-form-widget '.slugify(get_class($this)).' " >';

        $html .= '<table class="sublist">';

        if ($this->tableHeader) {
            $html .= $this->renderHeader('text');
        }

        $html .= '<tbody class="'.($this->sortable?'sortable-container':'').'">';
        if ($this->objects) foreach($this->objects as $o) {
            $html .= $this->renderRowAsText( $o );
        }
        $html .= '</tbody>';
        
        $html .= '<tfoot>';
        if (method_exists($this, 'renderFooterRow')) {
            $html .= $this->renderFooterRow();
        }
        $html .= '</tfoot>';
        

        $html .= '</table>';

        $html .= '</div>';

        return $html;
    }
    
    public function renderHeader($method='default') {
        $html = '';
        
        $html .= '<thead>';
        if ($this->sortable) {
            $html .= '<th></th>';
        }
        foreach($this->widgets as $w) {
            if (is_a($w, HiddenField::class)) continue;
            $html .= '<th class="th-'.slugify($w->getName()).'">'.esc_html($w->getLabel()).'</th>';
        }
        $html .= '<th></th>';
        $html .= '</thead>';
        
        return $html;
    }

    public function render() {

        $html = '';

        $html = '<div class="widget list-edit-form-widget '.slugify(get_class($this)).' " >';

        $html .= '<input type="hidden" class="method-object-list" value="'.esc_attr($this->getName()).'" />';
        $html .= '<input type="hidden" class="form-class" value="'.esc_attr(get_class($this)).'" />';

        $html .= '<table class="sublist">';

        if ($this->tableHeader) {
            $html .= $this->renderHeader();
        }

        $html .= '<tbody class="list-edit-widget-'.slugify($this->methodObjectList).' '.($this->sortable?'sortable-container':'').'">';
        if ($this->objects) foreach($this->objects as $o) {
            $html .= $this->renderRow( $o );
        }
        $html .= '</tbody>';
        
        $html .= '<tfoot></tfoot>';

        $html .= '</table>';


        if ($this->getInfoText()) {
            $html .= infopopup($this->getInfoText());
        }


        $html .= '<div class="add-entry-container action-box"><span><a class="add-record" href="javascript:void(0);">'.$this->strNewEntry.'</a></span></div>';
        $html .= '</div>';

        return $html;
    }

    public function renderRowAsText($obj=array()) {
        $html = '<tr>';

        if ($this->sortable) {
            $html .= '<td class="td-sortable"><span class="fa fa-sort handler-sortable"></span></td>';
        }

        $emptyFields = array();
        
        // bind values
        foreach($this->widgets as $w) {
            if ($w->bindObject( $obj ) == 0) {
                $emptyFields[] = $w->getName();
            }
        }

        // render record
        for($x=0; $x < count($this->widgets); $x++) {
            $w = $this->widgets[$x];
            if (is_a($w, HiddenField::class)) continue;

            $html .= '<td class="input-'.slugify($w->getName()).'">';

            if (in_array($w->getName(), $emptyFields)) {
                
            } else {
                $html .= $w->renderAsText();
            }

            $html .= '</td>';
        }

        $html .= '</tr>';

        return $html;
    }

    public function renderRow($obj=array()) {
        $html = '<tr>';

        if ($this->sortable) {
            $html .= '<td class="td-sortable"><span class="fa fa-sort handler-sortable"></span></td>';
        }

        // bind values
        foreach($this->widgets as $w) {
            $w->bindObject( $obj );
        }

        // render hidden
        $hiddenHtml = '';
        foreach($this->widgets as $sw) {
            if (is_a($sw, HiddenField::class))
                $hiddenHtml .= $sw->render();
        }

        // render record
        for($x=0; $x < count($this->widgets); $x++) {
            $w = $this->widgets[$x];
            if (is_a($w, HiddenField::class)) continue;

            $html .= '<td class="input-'.slugify($w->getName()).'">';

            // put all hidden fields in first <td>
            if ($hiddenHtml) {
                $html .= $hiddenHtml;
                $hiddenHtml = '';
            }


            $html .= $w->render();

            $html .= '</td>';
        }

        $html .= '<td class="action">';
        $html .= '<a href="javascript:void(0);"><span class="fa fa-remove row-delete"></span></a>';
        $html .= '</td>';

        $html .= '</tr>';

        return $html;
    }



    public function changes(DBObject $obj) {

        $fields = array();
        foreach($this->widgets as $w) {
            if (is_a($w, HiddenField::class)) continue;

            $fields[] = $w->getName();
        }


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
                if (isset($o[$f]))
                    $vals[$f] = $o[$f];
                else
                    $vals[$f] = null;
            }
            $new[] = $vals;
        }

        for($x=0; $x < count($old) && $x < count($new); $x++) {
            foreach($old[$x] as $f => $v) {
                $v2 = $new[$x][$f];


                // price? => compare in cents
                if (strpos($f, 'price') !== false) {
                    $v = round(strtodouble($v) * 100);
                    $v2 = round(strtodouble($v2) * 100);
                }

                if ($v == $v2) {
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
