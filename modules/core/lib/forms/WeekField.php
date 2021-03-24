<?php

namespace core\forms;

class WeekField extends BaseWidget {
    
    protected $startYear;
    protected $startWeek;
    
    protected $endYear;
    protected $endWeek;
    
    protected $thisWeek = null;
    
    
    public function __construct($name, $value=null, $label=null) {
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $dt = new \DateTime('now', new \DateTimeZone(date_default_timezone_get()));
        
        $this->startYear = $dt->format('Y');
        $this->startWeek = $dt->format('W');
        $this->thisWeek = $dt->format('Y-W');
        
        $this->endYear = $dt->format('Y')+1;
        $this->endWeek = weeks_in_year($this->endYear);
        
    }
    
    public function setStartYearWeek($y, $w) {
        $this->setStartYear($y);
        $this->setStartWeek($w);
    }
    public function getStartYear() { return $this->startYear; }
    public function setStartYear($y) { $this->startYear = $y; }
    public function getStartWeek() { return $this->startWeek; }
    public function setStartWeek($w) { $this->startWeek = $w; }
    
    public function setEndYearWeek($y, $w) {
        $this->setEndYear($y);
        $this->setEndWeek($w);
    }
    public function getEndYear() { return $this->endYear; }
    public function setEndYear($y) { $this->endYear = $y; }
    public function getEndWeek() { return $this->endWeek; }
    public function setEndWeek($w) { $this->endWeek= $w; }
    
    
    protected function formatWeek($val) {
        // invalid value?
        if (preg_match('/^\\d{4}-\\d{1,2}$/', $val) == false) {
            return null;
        }
        
        // format value
        if (preg_match('/^\\d{4}-\\d{1}$/', $val)) {
            list($y, $w) = explode('-', $val);
            return $y . '-0' . $w;
        }
        
        // validate value
        list($y, $w) = explode('-', $val);
        $y = (int)$y;
        $w = (int)$w;
        if ($y < 1000 || $y > 3000) {
            return null;
        }
        if ($w < 1 || $w > weeks_in_year($y)) {
            return null;
        }
        
        
        return $val;
    }
    
    public function getValue() {
        $v = parent::getValue();
        
        // format
        return $this->formatWeek( $v );
    }
    
    
    public function render() {
        
        // get selected val
        $val = $this->getValue();
        
        
        // $val before startYearWeek? => set startYearWeek back
        if ($val) {
            $intThisWeek = (int)str_replace('-', '', $val);
            $intStartWeek = (int)sprintf('%d%02d', $this->startYear, $this->startWeek);
            if ($intThisWeek < $intStartWeek) {
                list ($sy, $sw) = explode('-', $val);
                // maybe -10 for scrolling/spacing?
                $this->setStartYearWeek($sy, $sw);
            }
            
            // $val after endYearWeek? => set endYearWeek 
            $intEndWeek = (int)sprintf('%d%02d', $this->endYear, $this->endWeek);
            if ($intThisWeek > $intEndWeek) {
                list ($sy, $sw) = explode('-', $val);
                $this->setEndYearWeek($sy, $sw);
            }
        }
        

        // create option map
        $endYW = (int)sprintf('%04d%02d', $this->endYear, $this->endWeek);
        $curYW = (int)sprintf('%04d%02d', $this->startYear, $this->startWeek);
        
        $map_weeks = array();
        for($x=0; $x < 500 && $curYW <= $endYW ; $x++) {
            $curYear = (int)substr($curYW, 0, 4);
            $curWeek = (int)substr($curYW, 4, 2);
            
            $key = sprintf('%d-%02d', $curYear, $curWeek);
            $map_weeks[ $key ] = [
                'description' => t('Week') . ' ' . $curWeek . ' - ' . $curYear
            ];
            
            // determine next week
            $curYW = next_week_no($curYear, $curWeek);
            $curYW = (int)str_replace('-', '', $curYW);
        }
        
        // shouldn't happen
        if ( $val && isset($map_weeks[$val]) == false ) {
            list($y, $w) = explode('-', $val);
            $w = intval($w);
            $map_weeks[ $y.'-'.$w ] = ['description' => t('Week') . ' ' . $w . ' - ' . $y];
        }
        
        
        // render widget
        $html = '';
        $html .= '<div class="widget week-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        
        $html .= '<a href="javascript:void(0);" class="fa fa-angle-left week-field-prev-option" onclick="weekField_prev_option(this);"></a>';
        
        $html .= '<select name="'.esc_attr($this->getName()).'">';
        foreach($map_weeks as $key => $props) {
            $key = $this->formatWeek($key);
            
            $html .= '<option value="'.esc_attr($key).'" '.($key == $val?'selected="selected"':'').' 
                        class="' . ($key == $this->thisWeek?'current-week':'') . '">'
                . esc_html($props['description'])
                . '</option>';
        }
        $html .= '</select>';
        
        $html .= '<a href="javascript:void(0);" class="fa fa-angle-right week-field-next-option" onclick="weekField_next_option(this);"></a>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    
}

