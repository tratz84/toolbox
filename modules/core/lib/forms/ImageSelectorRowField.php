<?php


namespace core\forms;



class ImageSelectorRowField extends BaseWidget {
    
    protected $images = array();
    
    public function __construct($name, $value=null, $label=null, $opts=array()) {
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->options = $opts;
    }
    
    
    public function addImage($value, $url,$opts=array()) {
        $opts['url'] = $url;
        $this->images[$value] = $opts;
    }
    
    
    public function render() {
        $html = '';
        
        
        $html .= '<div class="widget image-selector-field">';
        
        $html .= '  <label>'.$this->getLabel().'</label>';
        
        $html .= '  <input type="hidden" class="hidden-value" name="'.esc_attr($this->getName()).'" value="'.esc_attr($this->getValue()).'" />';
        
        $html .= '  <ul class="clear">';
        foreach($this->images as $val => $opts) {
            $html .= '    <li onclick="" data-value="'.esc_attr( $val ).'" class="'.($val == $this->getValue() ? 'selected':'').'">';
            $html .= '      <img src="'. esc_attr($opts['url']) . '" />';
            $html .= '    </li>';
        }
        $html .= '  </ul>';
        $html .= '</div>';
        
        return $html;
    }
}


