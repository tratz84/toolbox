<?php

namespace core\forms;

class YearField extends BaseWidget {
    
    protected $startYear;
    
    protected $endYear;
    
    protected $thisYear = null;
    
    
    public function __construct($name, $value=null, $label=null) {
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->startYear = date('Y');
        $this->startWeek = date('Y');
        
        $this->endYear = date('Y');
        
    }
    
    
    public function getStartYear() { return $this->startYear; }
    public function setStartYear($y) { $this->startYear = $y; }
    
    public function getEndYear() { return $this->endYear; }
    public function setEndYear($y) { $this->endYear = $y; }
    
    
    public function getValue() {
        $v = parent::getValue();
        
        // format
        return $v;
    }
    
    
    
    public function render() {
        
        // get selected val
        $val = $this->getValue();
        
        
        
        $map_years = array();
        $start = $this->getStartYear();
        $end = $this->getEndYear();
        if ($end < $start)
            $end = $start;
        
        for($x=$this->getStartYear(), $infLoopDetect=0; $x <= $this->getEndYear() && $infLoopDetect < 1000; $x++, $infLoopDetect++) {
            
            $map_years[ $x ] = [
                'description' => $x
            ];
        }
        
        // shouldn't happen
        if ( $val && isset($map_years[$val]) == false ) {
            $map_years[ $val ] = ['description' => $val];
        }
        
        
        // render widget
        $html = '';
        $html .= '<div class="widget year-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        
        $html .= '<a href="javascript:void(0);" class="fa fa-angle-left year-field-prev-option" onclick="yearField_prev_option(this);"></a>';
        
        $html .= '<select name="'.esc_attr($this->getName()).'">';
        foreach($map_years as $key => $props) {
            
            $html .= '<option value="'.esc_attr($key).'" '.($key == $val?'selected="selected"':'').'
                        class="' . ($key == $this->thisYear?'current-year':'') . '">'
                            . esc_html($props['description'])
                            . '</option>';
        }
        $html .= '</select>';
        
        $html .= '<a href="javascript:void(0);" class="fa fa-angle-right year-field-next-option" onclick="yearField_next_option(this);"></a>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    
}

