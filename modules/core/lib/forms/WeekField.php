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
    
    
    public function render() {
        // build week array
        
        
        // get selected val
        $val = $this->getValue();
        if ($this->getValue() && preg_match('/^\\d{4}-\\d{1,2}$/', $this->getValue())) {
            $val = $this->getValue();
        } else {
            $val = $this->thisWeek;
        }
        
        // entered startWeek after $val> => set $startWeek to $val
        if ($val) {
            $intThisWeek = (int)str_replace('-', '', $val);
            $intStartWeek = (int)str_replace('-', '', $this->startWeek);
            if ($intThisWeek > $intStartWeek) {
                list ($sy, $sw) = explode('-', $val);
                // maybe -10 for scrolling/spacing?
                $this->setStartYearWeek($sy, $sw);
            }
        }
        

        $map_weeks = array();
        $curYear = $this->startYear;
        $curWeek = $this->startWeek;
        $weeks_in_year = weeks_in_year( $curYear );
        
        
        for($x=0; $x < 500 && ($curYear < $this->endYear) || ($curYear == $this->endYear && $curWeek <= $this->endWeek) ; $x++) {
            
            $map_weeks[$curYear . '-' . $curWeek ] = [
                'description' => t('Week') . ' ' . $curWeek . ' - ' . $curYear
            ];
            
            // determine next week
            $curWeek++;
            if ($curWeek > $weeks_in_year) {
                $curWeek = 1;
                $weeks_in_year = weeks_in_year( $curYear + 1 );
                $curYear++;
            }
        }
        
        
        if ( isset($map_weeks[$val]) == false ) {
            list($y, $w) = explode('-', $val);
            $map_weeks[ $val ] = t('Week') . ' ' . $w . ' - ' . $y;
        }
        
        
        $html = '';
        
        $html .= '<div class="widget week-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        
        $html .= '<a href="javascript:void(0);" class="fa fa-angle-left week-field-prev-option" onclick="weekField_prev_option(this);"></a>';
        
        $html .= '<select name="'.esc_attr($this->getName()).'">';
        foreach($map_weeks as $key => $props) {
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

