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

class FormChangesHtml
{
    protected $oldForm;
    protected $newForm;
    
    protected $html = '';
    protected $changeCount = 0;
    
    protected $isNew = false;
    
    protected $changes = array();
    
    
    public function __construct($oldForm, $newForm) {
        $this->oldForm = $oldForm;
        $this->newForm = $newForm;
    }
    
    public function getChanges() { return $this->changes; }
    public function setChanges($c) { $this->changes = $c; }
    
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
            
            if ($w->getValue() && (is_a($w, SelectField::class) || is_a($w, Select2Field::class))) {
                $val = $w->getValueLabel();
            } else {
                $val = $w->getValue();
            }

            $w_old = $this->oldForm->getWidget($w->getName());
            if ($w_old->getValue() && (is_a($w_old, SelectField::class) || is_a($w_old, Select2Field::class))) {
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
                
                // log change
                $this->changes[] = array(
                    'action' => 'changed',
                    'name' => $w->getName(),
                    'key' => $w->getName(),
                    'label' => $w->getLabel(),
                    'old' => $oldVal,
                    'new' => $val
                );
                
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
                if ($w2->getValue() && (is_a($w2, SelectField::class) || is_a($w2, Select2Field::class))) {
                    $vNew = $w2->getValueLabel();
                } else {
                    $vNew = $w2->getValue();
                }
                
                $w2->setValue('');
                $w2->bindObject($objsOld[$x]);
                if ($w2->getValue() && (is_a($w2, SelectField::class) || is_a($w2, Select2Field::class))) {
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
                    
                    $this->changes[] = array(
                        'action' => 'changed',
                        'name' => $w2->getName(),
                        'key' => $w2->getName(),
                        'label' => $widgetNewForm->getLabel().' - '.$w2->getLabel(),
                        'old' => $vOld,
                        'new' => $vNew
                    );
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
                    
                    
                    // new
                    $this->changes[] = array(
                        'action' => 'new',
                        'name' => $w2->getName(),
                        'key' => $w2->getName(),
                        'label' => $widgetNewForm->getLabel().' - '.$w2->getLabel(),
                        'old' => null,
                        'new' => $vNew
                    );
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
                    
                    
                    $this->changes[] = array(
                        'action' => 'removed',
                        'name' => $w2->getName(),
                        'key' => $w2->getName(),
                        'label' => $widgetNewForm->getLabel().' - '.$w2->getLabel(),
                        'old' => $vOld,
                        'new' => null
                    );
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
                    
                    
                    
                    $this->changes[] = array(
                        'action' => 'changed',
                        'name' => $w2->getName(),
                        'key' => $w2->getName(),
                        'label' => $widgetNewForm->getLabel().' - '.$w2->getLabel(),
                        'old' => $vOld,
                        'new' => $vNew
                    );
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
                    
                    
                    $this->changes[] = array(
                        'action' => 'new',
                        'name' => $w2->getName(),
                        'key' => $w2->getName(),
                        'label' => $widgetNewForm->getLabel().' - '.$w2->getLabel(),
                        'old' => null,
                        'new' => $vNew
                    );
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
                    
                    
                    $this->changes[] = array(
                        'action' => 'removed',
                        'name' => $w2->getName(),
                        'key' => $w2->getName(),
                        'label' => $widgetNewForm->getLabel().' - '.$w2->getLabel(),
                        'old' => $vOld,
                        'new' => null
                    );
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
        
        // mark all as new
        $c = $fch->getChanges();
        for($x=0; $x < count($c); $x++) {
            $c[$x]['action'] = 'new';
        }
        $fch->setChanges($c);
        
        
        return $fch;
    }
    
    
    public function form2html() {
        $this->isNew = true;
        
        $widgets = $this->getWidgets($this->newForm);

        $html = '<table class="form-changes list-widget-changes">';
        $html .= '<thead><tr><th>Veldnaam</th><th>Waarde</th></tr></thead>' . "\n";

        $html .= '<tbody>';
        foreach ($widgets as $w) {
            if (is_a($w, ListWidget::class))
                continue;
            
            if ($w->getLabel() == '')
                continue;
            
            if ($w->getValue() && (is_a($w, SelectField::class) || is_a($w, Select2Field::class)))
                $val = $w->getValueLabel();
            else
                $val = $w->getValue();
            
            if (is_a($w, DatePickerField::class))
                $val = format_date($val, 'd-m-Y');

            $html .= '<tr><td>' . esc_html($w->getLabel()) . '</td><td>' . esc_html($val) . '</td></tr>' . "\n";
            
            $this->changes[] = array(
                'action' => 'new',
                'name' => $w->getName(),
                'key' => $w->getName(),
                'label' => $w->getLabel(),
                'old' => null,
                'new' => $val
            );
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
        $html .= '<thead><tr><th>Veldnaam</th><th>Oude waarde</th><th>Nieuwe waarde</th></tr></thead>' . "\n";
        
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
        
        // mark all as removed
        $c = $fch->getChanges();
        for($x=0; $x < count($c); $x++) {
            $c[$x]['action'] = 'removed';
        }
        $fch->setChanges($c);
        
        
        return $fch;
    }
}


