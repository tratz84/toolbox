<?php

namespace core\forms;

class MonthField extends BaseWidget {
    
    protected $startYear;
    protected $startMonth;
    
    protected $endYear;
    protected $endMonth;
    
    
    public function __construct($name, $value=null, $label=null) {
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
        
        $this->startYear = date('Y');
        $this->startMonth = date('n');
        
        $this->endYear = date('Y', strtotime('+1 year'));
        $this->endMonth = date('n', strtotime('+1 year'));
    }
    
    public function setStartYearMonth($y, $m) {
        $this->setStartYear($y);
        $this->setStartMonth($m);
    }
    public function getStartYear() { return $this->startYear; }
    public function setStartYear($y) { $this->startYear = $y; }
    public function getStartMonth() { return $this->startMonth; }
    public function setStartMonth($m) { $this->startMonth = $m; }

    public function setEndYearMonth($y, $m) {
        $this->setEndYear($y);
        $this->setEndMonth($m);
    }
    public function getEndYear() { return $this->endYear; }
    public function setEndYear($y) { $this->endYear = $y; }
    public function getEndMonth() { return $this->endMonth; }
    public function setEndMonth($m) { $this->endMonth = $m; }
    
    
    public function render() {
        // build month-array
        $start = date('Y-m-d', mktime(0, 0, 0, $this->getStartMonth(), 15, $this->getStartYear()));
        $end = date('Y-m-d', mktime(0, 0, 0, $this->getEndMonth(), 15, $this->getEndYear()));
        
        $map_months = array();
        $next = $start;
        $ymdNext = (int)format_date($next, 'Ymd');
        $ymdEnd = (int)format_date($end, 'Ymd');
        for($x=0; $x < 500 && $ymdNext < $ymdEnd; $x++) {
            $ym = format_date($next, 'Y-m');
            
            $map_months[ $ym ] = [
                'description' => t('month.' . format_date($next, 'n')) . ' ' . format_date($next, 'Y')
            ];
            
            $next = next_month($next, 1);
            $ymdNext = (int)format_date($next, 'Ymd');
            $ymdEnd = (int)format_date($end, 'Ymd');
        }
        
        if ( $this->getValue() && preg_match('/^\\d{4}-\\d{2}$/', $this->getValue()) && isset($map_months[$this->getValue()]) == false ) {
            list($y, $m) = explode('-', $this->getValue());
            $map_months[$this->getValue()] = t('month.'.$m) . ' ' . $y;
        }
        
        $html = '';
        
        $html .= '<div class="widget month-field-widget widget-'.slugify($this->getName()).'">';
        $html .= '<label>'.esc_html($this->getLabel()).infopopup($this->getInfoText()).'</label>';
        
        $html .= '<a href="javascript:void(0);" class="fa fa-angle-left month-field-prev-option" onclick="monthField_prev_option(this);"></a>';
        
        $html .= '<select name="'.esc_attr($this->getName()).'">';
        foreach($map_months as $key => $val) {
            $html .= '<option value="'.esc_attr($key).'" '.($key == $this->getValue()?'selected="selected"':'').'>'
                    . esc_html($val['description'])
                    . '</option>';
        }
        $html .= '</select>';
        
        $html .= '<a href="javascript:void(0);" class="fa fa-angle-right month-field-next-option" onclick="monthField_next_option(this);"></a>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    
}

