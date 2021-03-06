<?php
namespace base\forms;

use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\ListFormWidget;
use core\forms\ListWidget;
use core\forms\Select2Field;
use core\forms\SelectField;
use core\forms\WidgetContainer;
use core\forms\CheckboxField;

class FormChangesHtml
{
    protected $oldForm;
    protected $newForm;
    
    protected $html = '';
    protected $changeCount = 0;
    
    protected $isNew = false;
    
    
    public function __construct($oldForm, $newForm) {
        $this->oldForm = $oldForm;
        $this->newForm = $newForm;
    }
    
    public function getChangeCount() { return $this->changeCount; }
    public function getHtml() {
        return '<div class="form-changes-container form-changes-container-v1">' . $this->html . '</div>';
    }
    public function hasChanges() { return $this->isNew || $this->changeCount > 0; }
    
    protected function getWidgets(WidgetContainer $form) {
        
        $r = array();
        $widgets = $form->getWidgets();
        
        for($x=0; $x < count($widgets); $x++) {
            if (is_a($widgets[$x], ListWidget::class)) {
                $r[] = $widgets[$x];
            } else if (is_a($widgets[$x], WidgetContainer::class)) {
                $r = array_merge($r, self::getWidgets($widgets[$x]));
            } else {
                $r[] = $widgets[$x];
            }
        }
        
        return $r;
    }

    /**
     * @return \base\forms\FormChangesHtml
     */
    public static function formChanged(BaseForm $oldForm, BaseForm $newForm) {
        $fch = new FormChangesHtml($oldForm, $newForm);
        $fch->diffForms();
        
        return $fch;
    }
    
    public function diffForms() {
        $this->isNew = false;
        
        $this->changeCount = 0;
        
        $widgetsNewForm = self::getWidgets($this->newForm);

        $htmlBase = array('html' => '', 'changes' => 0);
        $htmlBase['html'] = '<table class="form-changes">';
        $htmlBase['html'] .= '<thead><tr><th>'.t('Fieldname').'</th><th>'.t('Old value').'</th><th>'.t('New value').'</th></tr></thead>' . "\n";

        $htmlBase['html'] .= '<tbody>';
        foreach ($widgetsNewForm as $w) {
            if (is_a($w, ListWidget::class))
                continue;
            
            if ($w->getLabel() == '')
                continue;
            
            if ($w->getName() == 'edited' || $w->getName() == 'created') {
                continue;
            }
            
            if (is_a($w, CheckboxField::class)) {
                $val = $w->getValue() ? t('Yes') : t('No');
            } else if ($w->getValue() && (is_a($w, SelectField::class) || is_a($w, Select2Field::class))) {
                $val = $w->getValueLabel();
            } else {
                $val = $w->getValue();
            }

            $w_old = $this->oldForm->getWidget($w->getName());
            
            // widget not found? => skip. Might happen if confirmation-checkbox or other temp-widgets are added to new-form
            if (!$w_old) {
                continue;
            }
            
            if (is_a($w_old, CheckboxField::class)) {
                $oldVal = $w_old->getValue() ? t('Yes') : t('No');
            } else if ($w_old->getValue() && (is_a($w_old, SelectField::class) || is_a($w_old, Select2Field::class))) {
                $oldVal = $w_old->getValueLabel();
            } else {
                $oldVal = $w_old->getValue();
            }

            
            if (is_a($w, DatePickerField::class)) {
                $val    = format_date($val, 'Y-m-d');
                $oldVal = format_date($oldVal, 'Y-m-d');
            }
            
            if ((string)$oldVal != (string)$val) {
                if (is_a($w, DatePickerField::class)) {
                    $oldVal = format_date($oldVal, 'd-m-Y');
                    $val = format_date($val, 'd-m-Y');
                }
                
                $this->changeCount++;
                $htmlBase['html'] .= '<tr><td>' . esc_html($w->getLabel()) . '</td><td>' . esc_html($oldVal) . '</td><td>' . esc_html($val) . '</td></tr>' . "\n";
                $htmlBase['changes']++;
            }
        }
        $htmlBase['html'] .= '</tbody>';
        $htmlBase['html'] .= '</table>';
        
        $htmlListWidgets = array();
        
        // loop through ListWidget's
        foreach ($widgetsNewForm as $w) {
            if (is_a($w, ListWidget::class) == false)
                continue;
            
            if (is_a($w, ListFormWidget::class)) {
                $lfw = $this->parseListFormWidget( $w );
                
                $htmlListWidgets[] = $lfw;
            }
            
            if (is_a($w, ListEditWidget::class)) {
                $hlw = $this->parseListEditWidget($w);
                
                $htmlListWidgets[] = $hlw;
            }
        }
        

        if ($this->changeCount == 0) {
            $this->html = '';
        } else {
            $html = '';
            
            // base
            if ($htmlBase['changes']) {
                $html .= $htmlBase['html'];
            }
            
            // list widgets
            foreach ($htmlListWidgets as $hlw) {
                if ($hlw['changes']) {
                    $html .= $hlw['html'];
                }
            }
            
            $this->html = $html;
        }
    }
    
    
    protected function parseListFormWidget($widgetNewForm) {
        $htmlList = array('html' => '', 'changes' => 0);
        
        if ($this->oldForm == null) {
            $objsOld = array();
        } else {
            $oldWidget = $this->oldForm->getWidget($widgetNewForm->getMethodObjectList());
            $objsOld = $oldWidget->getObjects();
        }
        
        // instantiate subform for widgetlist
        $clazzSubForm = $widgetNewForm->getFormClass();
        $subform = new $clazzSubForm();
        $widgetsList = $subform->getWidgets();
        
        
        $htmlList['html'] = '<table class="list-widget-changes">';
        $htmlList['html'] .= '<thead>';
        $htmlList['html'] .= '<tr>';
        $htmlList['html'] .= '<th>#</th>';
        foreach($widgetsList as $w2) {
            if (is_a($w2, HiddenField::class)) continue;
            
            $htmlList['html'] .= '<th>'.$w2->getLabel().'</th>';
        }
        $htmlList['html'] .= '</tr>';
        $htmlList['html'] .= '</thead>';
        
        $htmlList['html'] .= '<tbody>';
        
        $objsNew = $widgetNewForm->getObjects();
        for($x=0; $x < count($objsNew) && $x < count($objsOld); $x++) {
            $changed = false;
            
            $htmlTr = '<td>'.($x+1).'</td>';
            foreach($widgetsList as $w2) {
                if (is_a($w2, HiddenField::class)) continue;
                
                $w2->setValue('');
                $w2->bindObject($objsNew[$x]);
                if (is_a($w2, CheckboxField::class)) {
                    $vNew = $w2->getValue() ? t('Yes') : t('No');
                } else if ($w2->getValue() && (is_a($w2, SelectField::class) || is_a($w2, Select2Field::class))) {
                    $vNew = $w2->getValueLabel();
                } else {
                    $vNew = $w2->getValue();
                }
                
                $w2->setValue('');
                $w2->bindObject($objsOld[$x]);
                if (is_a($w2, CheckboxField::class)) {
                    $vOld = $w2->getValue() ? t('Yes') : t('No');
                } else if ($w2->getValue() && (is_a($w2, SelectField::class) || is_a($w2, Select2Field::class))) {
                    $vOld = $w2->getValueLabel();
                } else {
                    $vOld = $w2->getValue();
                }
                
                
                if ($vNew == $vOld) {
                    $htmlTr .= '<td>'.esc_html($vNew).'</td>';
                } else {
                    $changed = true;
                    $htmlTr.= '<td class="changed"><span class="old-value">'.esc_html($vOld).'</span> =&gt; <span class="new-value">' . esc_html($vNew) . '</span></td>';
                    $htmlList['changes']++;
                }
            }
            
            if ($changed) {
                $htmlList['html'] .= '<tr class="'.($changed?'changed':'').'">'.$htmlTr.'</tr>';
            }
            
            // treat row as 1 change
            if ($changed)
                $this->changeCount++;
        }
        
        
        // new lines
        if (count($objsNew) > count($objsOld)) {
            for($x=count($objsOld); $x < count($objsNew); $x++) {
                
                $htmlTr = '<td>'.($x+1).'</td>';
                foreach($widgetsList as $w2) {
                    if (is_a($w2, HiddenField::class)) continue;
                    
                    $w2->bindObject($objsNew[$x]);
                    $vNew = $w2->getValue();
                    
                    $htmlTr.= '<td>'.esc_html($vNew).'</td>';
                }
                
                $htmlList['html'] .= '<tr class="new">'.$htmlTr.'</tr>';
                $htmlList['changes']++;
                
                $this->changeCount++;
            }
        }
        
        // removed lines
        if (count($objsNew) < count($objsOld)) {
            for($x=count($objsNew); $x < count($objsOld); $x++) {
                
                $htmlTr = '<td>'.($x+1).'</td>';
                foreach($widgetsList as $w2) {
                    if (is_a($w2, HiddenField::class)) continue;
                    
                    $w2->bindObject($objsOld[$x]);
                    $vOld = $w2->getValue();
                    
                    $htmlTr.= '<td>'.esc_html($vOld).'</td>';
                }
                
                $htmlList['html'] .= '<tr class="removed">'.$htmlTr.'</tr>';
                $htmlList['changes']++;
                
                $this->changeCount++;
            }
        }
        
        $htmlList['html'] .= '</tbody>';
        $htmlList['html'] .= '</table>';
        
        
        
        return $htmlList;
    }
    
    protected function parseListEditWidget($widgetNewForm) {
        $htmlList = array('html' => '', 'changes' => 0);
        
        if ($this->oldForm == null) {
            $objsOld = array();
        } else {
            $oldWidget = $this->oldForm->getWidget($widgetNewForm->getMethodObjectList());
            $objsOld = $oldWidget->getObjects();
        }
        
        $widgetsList = $widgetNewForm->getWidgets();
        
        $htmlList['html'] = '<table class="list-widget-changes">';
        $htmlList['html'] .= '<thead>';
        $htmlList['html'] .= '<tr>';
        $htmlList['html'] .= '<th>#</th>';
        foreach($widgetsList as $w2) {
            if (is_a($w2, HiddenField::class)) continue;
            
            $htmlList['html'] .= '<th>'.$w2->getLabel().'</th>';
        }
        $htmlList['html'] .= '</tr>';
        $htmlList['html'] .= '</thead>';
        
        $htmlList['html'] .= '<tbody>';
        
        $objsNew = $widgetNewForm->getObjects();
        for($x=0; $x < count($objsNew) && $x < count($objsOld); $x++) {
            $changed = false;
            
            $htmlTr = '<td>'.($x+1).'</td>';
            foreach($widgetsList as $w2) {
                if (is_a($w2, HiddenField::class)) continue;
                
                $w2->setValue('');
                $w2->bindObject($objsNew[$x]);
                $vNew = $w2->getValue();
                
                $w2->setValue('');
                $w2->bindObject($objsOld[$x]);
                $vOld = $w2->getValue();
                
                if ($vNew == $vOld) {
                    $htmlTr .= '<td>'.esc_html($vNew).'</td>';
                } else {
                    $changed = true;
                    $htmlTr.= '<td class="changed"><span class="old-value">'.esc_html($vOld).'</span> =&gt; <span class="new-value">' . esc_html($vNew) . '</span></td>';
                    $htmlList['changes']++;
                }
            }
            
            if ($changed) {
                $htmlList['html'] .= '<tr class="'.($changed?'changed':'').'">'.$htmlTr.'</tr>';
            }
            
            // treat row as 1 change
            if ($changed)
                $this->changeCount++;
        }
        
        
        // new lines
        if (count($objsNew) > count($objsOld)) {
            for($x=count($objsOld); $x < count($objsNew); $x++) {
                
                $htmlTr = '<td>'.($x+1).'</td>';
                foreach($widgetsList as $w2) {
                    if (is_a($w2, HiddenField::class)) continue;
                    
                    $w2->bindObject($objsNew[$x]);
                    $vNew = $w2->getValue();
                    
                    $htmlTr.= '<td>'.esc_html($vNew).'</td>';
                }
                
                $htmlList['html'] .= '<tr class="new">'.$htmlTr.'</tr>';
                $htmlList['changes']++;
                
                $this->changeCount++;
            }
        }
        
        // removed lines
        if (count($objsNew) < count($objsOld)) {
            for($x=count($objsNew); $x < count($objsOld); $x++) {
                
                $htmlTr = '<td>'.($x+1).'</td>';
                foreach($widgetsList as $w2) {
                    if (is_a($w2, HiddenField::class)) continue;
                    
                    $w2->bindObject($objsOld[$x]);
                    $vOld = $w2->getValue();
                    
                    $htmlTr.= '<td>'.esc_html($vOld).'</td>';
                }
                
                $htmlList['html'] .= '<tr class="removed">'.$htmlTr.'</tr>';
                $htmlList['changes']++;
                
                $this->changeCount++;
            }
        }
        
        $htmlList['html'] .= '</tbody>';
        $htmlList['html'] .= '</table>';
        
        return $htmlList;
    }
    
    
    

    /**
     * @return \base\forms\FormChangesHtml
     */
    public static function formNew(BaseForm $form) {
        $fch = new FormChangesHtml(null, $form);
        $fch->form2html();
        
        return $fch;
    }
    
    
    public function form2html() {
        $this->isNew = true;
        
        $widgets = $this->getWidgets($this->newForm);

        $html = '<table class="form-changes list-widget-changes">';
        $html .= '<thead><tr><th class="th-fieldname">'.t('Fieldname').'</th><th class="th-value">'.t('Value').'</th></tr></thead>' . "\n";

        $html .= '<tbody>';
        foreach ($widgets as $w) {
            if (is_a($w, ListWidget::class))
                continue;
            
            if ($w->getLabel() == '')
                continue;
            if ($w->getName() == 'edited' || $w->getName() == 'created') {
                continue;
            }
            
            
            if (is_a($w, CheckboxField::class)) {
                $val = $w->getValue() ? t('Yes') : t('No');
            } else if ($w->getValue() && (is_a($w, SelectField::class) || is_a($w, Select2Field::class)))
                $val = $w->getValueLabel();
            else
                $val = $w->getValue();
            
            if (is_a($w, DatePickerField::class))
                $val = format_date($val, 'd-m-Y');

            $html .= '<tr><td>' . esc_html($w->getLabel()) . '</td><td>' . esc_html($val) . '</td></tr>' . "\n";
        }
        $html .= '</tbody>';
        $html .= '</table>';
        
        
        // loop through ListWidgets
        foreach ($widgets as $w) {
            if (is_a($w, ListWidget::class) == false)
                continue;
            
            if (is_a($w, ListFormWidget::class)) {
                $lfw = $this->parseListFormWidget( $w );
                
                $html .= $lfw['html'];
            }
            
            if (is_a($w, ListEditWidget::class)) {
                $lew = $this->parseListEditWidget($w);
                
                $html .= $lew['html'];
            }
            
        }
        
        $this->html = $html;
    }
    
    
    public static function tableFromArray($arr) {
        $html = '<div class="form-changes-container">';
        $html .= '<table class="form-changes">';
        $html .= '<thead><tr><th class="th-fieldname">'.t('Fieldname').'</th><th class="th-old-value">'.t('Old value').'</th><th class="th-new-value">'.t('New value').'</th></tr></thead>' . "\n";
        
        $html .= '<tbody>';
        foreach($arr as $row) {
            $html .= '<tr><td>'.esc_html($row['label']).'</td><td>'.esc_html($row['old']).'</td><td>'.esc_html($row['new']).'</td></tr>';
        }
        $html .= '</tbody>';
        
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * @return \base\forms\FormChangesHtml
     */
    public static function formDeleted(BaseForm $form)
    {
        $fch = new FormChangesHtml(null, $form);
        $fch->form2html();
        
        return $fch;
    }
}


