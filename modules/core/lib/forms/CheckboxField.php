<?php

namespace core\forms;

class CheckboxField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    
    public function bindObject($obj) {
        $fieldCount = parent::bindObject($obj);
        // TVW - 2025-05-21
        // checkbox-fields are kinda problematic, in that they don't post anything if not checked
        // therefore, the field is not set, and no value is set..
        // for now, if not set => setValue(0)
        if ($fieldCount == 0 && is_a($this, CheckboxField::class)) {
            $this->setValue(0);
            $fieldCount++;
        }
        
        return $fieldCount;
    }
    
    
    public function getValue() {
        
        $v = parent::getValue();
        
        if ($v && ($v == 'on' || $v == '1'))
            return 1;
        else
            return 0;
        
    }
    
    public function renderTag() {
        $tag = parent::renderTag();
        $tag .= '<label class="checkbox-ui-placeholder" for="'.esc_attr($this->getName()).'"></label>';
        
        return $tag;
    }
    
    
    public function render() {
        $this->setAttribute('type', 'checkbox');
        $this->addContainerClass('checkbox-ui-container');
        
        
        if ($this->hasError()) {
            $this->addContainerClass('error');
        }
        
        $this->setAttribute('id', $this->getName());
        
        if ($this->getValue()) {
           $this->setAttribute('checked', 'checked');
        }
        // unset. This widget is re-used in ListEditWidget's
        else {
            $this->unsetAttribute( 'checked' );
        }
        
        return parent::render();
    }
 
    public function renderAsText() {
        $html = '';
        
        $html .= '<div class="widget checkbox-field-widget widget-'.slugify($this->getLabel()).'">';
        $html .= '<label>'.esc_html($this->getLabel()) . infopopup($this->getInfoText()) . '</label>';
        $html .= '<span>'.($this->getValue()?t('Yes'):t('No')).'</span>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}