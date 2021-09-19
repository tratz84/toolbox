<?php


namespace core\forms;

class DivContenteditableField extends BaseWidget {
    
    
    public function __construct($name, $value=null, $label=null) {
        
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
    }
    
    public function getValue() {
        $v = parent::getValue();
        $v = strip_tags($v, array('div', 'span', 'br', 'hr', 'b', 'i', 'em'));
        
        return $v;
    }
    
    public function render() {
        $html = '';
        
        $extraClass = $this->hasError() ? 'error' : '';
        $extraClass .= ' widget-'.slugify($this->getName());
        
        $html .= '<div class="widget span-textarea-field ' . slugify(get_class($this)) . '-widget ' . $extraClass . '">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        $html .= '<input type="hidden"  name="'.esc_attr($this->getName()).'" value="'.esc_attr($this->getValue()).'" />';
        $html .= '<div
                      class="input" 
                      role="textbox" 
                      id="'.esc_attr('ref-'.$this->getName()).'"
                      contenteditable>'.$this->getValue().'</div>';
        $html .= '</div>';
        // set text in input
        $html .= '<script> $('.json_encode("#ref-".$this->getName()).').on(\'keyup\', function() { $('.json_encode('[name='.$this->getName().']').').val( $(this).html() ); }); </script>';
        
        return $html;
    }
    
    public function renderAsText() {
        $html = '';
        
        $html .= '<div class="widget textarea-field-widget widget-'.slugify($this->getLabel()).'">';
        $html .= '<label>'.esc_html($this->getLabel()) . infopopup($this->getInfoText()) . '</label>';
        $html .= '<div>'.nl2br(esc_html($this->getValue())).'</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    
}

