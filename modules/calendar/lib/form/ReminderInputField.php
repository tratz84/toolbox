<?php

namespace calendar\form;


use core\forms\BaseWidget;
use calendar\model\CalendarItem;

class ReminderInputField extends BaseWidget {
    
    protected $recurrenceRule = null;
    protected $request = null;
    protected $recurrenceValueMap = array();
    
    public function __construct($name, $value=null, $label=null) {
        $this->setName($name);
        $this->setLabel($label);
        $this->setValue($value);
    }
    
    
    public function resetValues() {
        $this->recurrenceRule = null;
        $this->request = null;
        $this->recurrenceValueMap = array();
        $this->setValue('');
    }
    
    
    public function getRequest() { return $this->request; }
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        if (is_a($obj, CalendarItem::class)) {
            $this->recurrenceRule = $obj->getRecurrenceRule();
            
            // parse
            $this->recurrenceValueMap = array();
            if ($this->recurrenceRule && strpos($this->recurrenceRule, '=') !== false) {
                $tokens = explode(';', $this->recurrenceRule);
                foreach($tokens as $t) {
                    list($key, $val) = explode('=', $t, 2);
                    $this->recurrenceValueMap[$key] = $val;
                }
            }
        }
        
        if (is_array($obj)) {
            $this->request = $obj;
            
            if ((isset($obj['calendar_item_id']) == false || !$obj['calendar_item_id']) && (isset($obj['startDate']) && valid_date($obj['startDate']))) {
                $this->recurrenceValueMap['BYMONTHDAY'] = format_date($obj['startDate'], 'j');
                $this->recurrenceValueMap['BYMONTH'] = format_date($obj['startDate'], 'n');
            }
        }
    }
    
    public function getRecurrenceValue($key, $defaultVal='') {
        if (isset($this->recurrenceValueMap[$key])) {
            return $this->recurrenceValueMap[$key];
        } else {
            return $defaultVal;
        }
    }
    
    public function byDayChecked($day) {
        $byday = $this->getRecurrenceValue('BYDAY');
        if (!$byday) return '';
        
        $days = explode(',', $byday);
        
        return in_array(strtoupper($day), $days) ? 'checked=checked' : '';
    }
    
    
    public function getRecurrenceRule() {
        if ($this->request === null)
            return '';
        
        $r = '';
        
        if ($this->getValue() == 'daily') {
            $interval = (int)$this->request['daily_days'];
            if ($interval < 1 || $interval > 366) $interval=1;
            $r = 'FREQ=DAILY;INTERVAL='.$interval;
        }
        if ($this->getValue() == 'weekly') {
            $interval = (int)$this->request['weekly_weeks'];
            if ($interval < 1 || $interval > 52) $interval = 1;
            
            $days = array();
            if (isset($this->request['weekly_mo'])) $days[] = 'MO';
            if (isset($this->request['weekly_tu'])) $days[] = 'TU';
            if (isset($this->request['weekly_we'])) $days[] = 'WE';
            if (isset($this->request['weekly_th'])) $days[] = 'TH';
            if (isset($this->request['weekly_fr'])) $days[] = 'FR';
            if (isset($this->request['weekly_sa'])) $days[] = 'SA';
            if (isset($this->request['weekly_su'])) $days[] = 'SU';
            
            $r = 'FREQ=WEEKLY;INTERVAL='.$interval.';BYDAY='.implode(',', $days);
        }
        if ($this->getValue() == 'monthly-sameday') {
            $interval = (int)$this->request['monthly_sameday_interval'];
            if ($interval < 1 || $interval > 12) $interval = 1;
            
            $day = (int)$this->request['monthly_sameday_day'];
            
            $r = 'FREQ=MONTHLY;INTERVAL='.$interval.';BYMONTHDAY='.$day;
        }
        if ($this->getValue() == 'monthly-sameweek') {
            $interval = (int)$this->request['monthly_sameweek_interval'];
            if ($interval < 1 || $interval > 12) $interval = 1;
            
            $bysetpos = (int)$this->request['monthly_sameweek_bysetpos'];
            $day = strtoupper($this->request['monthly_sameweek_day']);
            
            $r = 'FREQ=MONTHLY;INTERVAL='.$interval.';BYDAY='.$day.';BYSETPOS='.$bysetpos;
        }
        if ($this->getValue() == 'yearly-sameday') {
            $dayno = (int)$this->request['yearly_sameday_day'];
            $month = (int)$this->request['yearly_sameday_month'];
            
            $r = 'FREQ=YEARLY;BYMONTH='.$month.';BYMONTHDAY='.$dayno;
        }
        if ($this->getValue() == 'yearly-sameweek') {
            $bysetpos = $this->request['yearly_sameweek_bysetpos'];
            $day = strtoupper( $this->request['yearly_sameweek_day'] );
            $month = $this->request['yearly_sameweek_month'];
            
            $r = 'FREQ=YEARLY;BYMONTH='.$month.';BYDAY='.$day.';BYSETPOS='.$bysetpos;
        }
        
        return $r;
    }
    
    
    public function render() {
        
        $recurrence_type = $this->getValue();
        
        $html = '';
        
        $html .= '<div class="widget reminder-input-field-widget">';
        $html .= '<label style="padding-bottom: 30px;">Herhaling</label>';
        
        $html .= '<select name="recurrence_type" onchange="calendarReminder_Change(this);">';
            $html .= '<option value="">Geen</option>';
            $html .= '<option value="daily" '           . ($recurrence_type=='daily'           ?'selected=selected':'').'>Dagelijks</option>';
            $html .= '<option value="weekly" '          . ($recurrence_type=='weekly'          ?'selected=selected':'').'>Wekelijks</option>';
            $html .= '<option value="monthly-sameday" ' . ($recurrence_type=='monthly-sameday' ?'selected=selected':'').'>Maandelijks op dezelfde dag</option>';
            $html .= '<option value="monthly-sameweek" '. ($recurrence_type=='monthly-sameweek'?'selected=selected':'').'>Maandelijks in dezelfde week</option>';
            $html .= '<option value="yearly-sameday" '  . ($recurrence_type=='yearly-sameday'  ?'selected=selected':'').'>Jaarlijks op dezelfde dag</option>';
            $html .= '<option value="yearly-sameweek" ' . ($recurrence_type=='yearly-sameweek' ?'selected=selected':'').'>Jaarlijks in dezelfde week</option>';
        $html .= '</select>';
        
        // Dagelijks
        $html .= '<div id="reminder-daily" style="'.($recurrence_type=='daily'?'':'display:none;').'">';
            $html .= 'Iedere <input type="number" min="1" max="31" name="daily_days" value="'.$this->getRecurrenceValue('INTERVAL', 1).'" /> dagen';
        $html .= '</div>';
        
        // Wekelijks
        $html .= '<div id="reminder-weekly" style="'.($recurrence_type=='weekly'?'':'display:none;').'">';
            $html .= '<div>';
            $html .= 'Iedere <input type="number" min="1" max="52" name="weekly_weeks" value="'.$this->getRecurrenceValue('INTERVAL', 1).'" /> weken';
            $html .= '<br/>';
                $html .= '<input type="checkbox" name="weekly_mo" value="mo" '.$this->byDayChecked('mo').' /> ma &nbsp; ';
                $html .= '<input type="checkbox" name="weekly_tu" value="tu" '.$this->byDayChecked('tu').' /> di &nbsp; ';
                $html .= '<input type="checkbox" name="weekly_we" value="we" '.$this->byDayChecked('we').' /> wo &nbsp; ';
                $html .= '<input type="checkbox" name="weekly_th" value="th" '.$this->byDayChecked('th').' /> do &nbsp; ';
                $html .= '<input type="checkbox" name="weekly_fr" value="fr" '.$this->byDayChecked('fr').' /> fr &nbsp; ';
                $html .= '<input type="checkbox" name="weekly_sa" value="sa" '.$this->byDayChecked('sa').' /> za &nbsp; ';
                $html .= '<input type="checkbox" name="weekly_su" value="su" '.$this->byDayChecked('su').' /> zo &nbsp; ';
            $html .= '</div>';
        $html .= '</div>';
        
        // Maandelijks op dezelfde dag
        $html .= '<div id="reminder-monthly-sameday" style="'.($recurrence_type=='monthly-sameday'?'':'display:none;').'">';
            $html .= 'Dag <input type="number" min="1" max="31" name="monthly_sameday_day" value="'.$this->getRecurrenceValue('BYMONTHDAY', 1).'" />, iedere de <input type="number" min="1" max="12" name="monthly_sameday_interval" value="'.$this->getRecurrenceValue('INTERVAL', 1).'" /> maand(en)';
        $html .= '</div>';
        
        
        // Maandelijks in dezelfde week
        $html .= '<div id="reminder-monthly-sameweek" style="'.($recurrence_type=='monthly-sameweek'?'':'display:none;').'">';
            $html .= 'Iedere <input type="number" min="1" max="12" name="monthly_sameweek_interval" value="'.$this->getRecurrenceValue('INTERVAL', 1).'" /> maand(en) ';
            $html .= 'op de ';
            $html .= '<select name="monthly_sameweek_bysetpos">';
                $html .= '<option value="1"  '.($this->getRecurrenceValue('BYSETPOS')== 1?'selected=selected':'').'>Eerste</option>';
                $html .= '<option value="2"  '.($this->getRecurrenceValue('BYSETPOS')== 2?'selected=selected':'').'>Tweede</option>';
                $html .= '<option value="3"  '.($this->getRecurrenceValue('BYSETPOS')== 3?'selected=selected':'').'>Derde</option>';
                $html .= '<option value="4"  '.($this->getRecurrenceValue('BYSETPOS')== 4?'selected=selected':'').'>Vierde</option>';
                $html .= '<option value="-1" '.($this->getRecurrenceValue('BYSETPOS')==-1?'selected=selected':'').'>Laatste</option>';
            $html .= '</select>';
            
            $html .= '<select name="monthly_sameweek_day">';
                $html .= '<option value="mo" '.($this->getRecurrenceValue('BYDAY')=='MO'?'selected=selected':'').'>Maandag</option>';
                $html .= '<option value="tu" '.($this->getRecurrenceValue('BYDAY')=='TU'?'selected=selected':'').'>Dinsdag</option>';
                $html .= '<option value="we" '.($this->getRecurrenceValue('BYDAY')=='WE'?'selected=selected':'').'>Woensdag</option>';
                $html .= '<option value="th" '.($this->getRecurrenceValue('BYDAY')=='TH'?'selected=selected':'').'>Donderdag</option>';
                $html .= '<option value="fr" '.($this->getRecurrenceValue('BYDAY')=='FR'?'selected=selected':'').'>Vrijdag</option>';
                $html .= '<option value="sa" '.($this->getRecurrenceValue('BYDAY')=='SA'?'selected=selected':'').'>Zaterdag</option>';
                $html .= '<option value="su" '.($this->getRecurrenceValue('BYDAY')=='SU'?'selected=selected':'').'>Zondag</option>';
            $html .= '</select>';
            
        $html .= '</div>';
        
        
        // Jaarlijks op dezelfde dag
        $html .= '<div id="reminder-yearly-sameday" style="'.($recurrence_type=='yearly-sameday'?'':'display:none;').'">';
            $html .= '<input type="number" min="1" max="31" name="yearly_sameday_day" placeholder="Dag nr" value="'.$this->getRecurrenceValue('BYMONTHDAY', 1).'" /> ';
            $html .= '<select name="yearly_sameday_month">';
                $html .= '<option value="1"  '.($this->getRecurrenceValue('BYMONTH') ==  1?'selected=selected':'').'>Januari</option>';
                $html .= '<option value="2"  '.($this->getRecurrenceValue('BYMONTH') ==  2?'selected=selected':'').'>Februari</option>';
                $html .= '<option value="3"  '.($this->getRecurrenceValue('BYMONTH') ==  3?'selected=selected':'').'>Maart</option>';
                $html .= '<option value="4"  '.($this->getRecurrenceValue('BYMONTH') ==  4?'selected=selected':'').'>April</option>';
                $html .= '<option value="5"  '.($this->getRecurrenceValue('BYMONTH') ==  5?'selected=selected':'').'>Mei</option>';
                $html .= '<option value="6"  '.($this->getRecurrenceValue('BYMONTH') ==  6?'selected=selected':'').'>Juni</option>';
                $html .= '<option value="7"  '.($this->getRecurrenceValue('BYMONTH') ==  7?'selected=selected':'').'>Juli</option>';
                $html .= '<option value="8"  '.($this->getRecurrenceValue('BYMONTH') ==  8?'selected=selected':'').'>Augustus</option>';
                $html .= '<option value="9"  '.($this->getRecurrenceValue('BYMONTH') ==  9?'selected=selected':'').'>September</option>';
                $html .= '<option value="10" '.($this->getRecurrenceValue('BYMONTH') == 10?'selected=selected':'').'>Oktober</option>';
                $html .= '<option value="11" '.($this->getRecurrenceValue('BYMONTH') == 11?'selected=selected':'').'>November</option>';
                $html .= '<option value="12" '.($this->getRecurrenceValue('BYMONTH') == 12?'selected=selected':'').'>December</option>';
            $html .= '</select>';
        $html .= '</div>';
        
        
        // Jaarlijks in dezelfde week
        $html .= '<div id="reminder-yearly-sameweek" style="'.($recurrence_type=='yearly-sameweek'?'':'display:none;').'">';
            $html .= 'Op de ';
            $html .= '<select name="yearly_sameweek_bysetpos">';
                $html .= '<option value="1" '.($this->getRecurrenceValue('BYSETPOS') == '1'?'selected=selected':'').'>Eerste</option>';
                $html .= '<option value="2" '.($this->getRecurrenceValue('BYSETPOS') == '2'?'selected=selected':'').'>Tweede</option>';
                $html .= '<option value="3" '.($this->getRecurrenceValue('BYSETPOS') == '3'?'selected=selected':'').'>Derde</option>';
                $html .= '<option value="4" '.($this->getRecurrenceValue('BYSETPOS') == '4'?'selected=selected':'').'>Vierde</option>';
                $html .= '<option value="-1" '.($this->getRecurrenceValue('BYSETPOS') == '-1'?'selected=selected':'').'>Laatste</option>';
            $html .= '</select>';
            
            $html .= '<select name="yearly_sameweek_day">';
                $html .= '<option value="mo" '.($this->getRecurrenceValue('BYDAY') == 'MO'?'selected=selected':'').'>Maandag</option>';
                $html .= '<option value="tu" '.($this->getRecurrenceValue('BYDAY') == 'TU'?'selected=selected':'').'>Dinsdag</option>';
                $html .= '<option value="we" '.($this->getRecurrenceValue('BYDAY') == 'WE'?'selected=selected':'').'>Woensdag</option>';
                $html .= '<option value="th" '.($this->getRecurrenceValue('BYDAY') == 'TH'?'selected=selected':'').'>Donderdag</option>';
                $html .= '<option value="fr" '.($this->getRecurrenceValue('BYDAY') == 'FR'?'selected=selected':'').'>Vrijdag</option>';
                $html .= '<option value="sa" '.($this->getRecurrenceValue('BYDAY') == 'SA'?'selected=selected':'').'>Zaterdag</option>';
                $html .= '<option value="su" '.($this->getRecurrenceValue('BYDAY') == 'SU'?'selected=selected':'').'>Zondag</option>';
            $html .= '</select>';
            $html .= ' van ';
            $html .= '<select name="yearly_sameweek_month">';
                $html .= '<option value="1"  '.($this->getRecurrenceValue('BYMONTH') ==  1?'selected=selected':'').'>Januari</option>';
                $html .= '<option value="2"  '.($this->getRecurrenceValue('BYMONTH') ==  2?'selected=selected':'').'>Februari</option>';
                $html .= '<option value="3"  '.($this->getRecurrenceValue('BYMONTH') ==  3?'selected=selected':'').'>Maart</option>';
                $html .= '<option value="4"  '.($this->getRecurrenceValue('BYMONTH') ==  4?'selected=selected':'').'>April</option>';
                $html .= '<option value="5"  '.($this->getRecurrenceValue('BYMONTH') ==  5?'selected=selected':'').'>Mei</option>';
                $html .= '<option value="6"  '.($this->getRecurrenceValue('BYMONTH') ==  6?'selected=selected':'').'>Juni</option>';
                $html .= '<option value="7"  '.($this->getRecurrenceValue('BYMONTH') ==  7?'selected=selected':'').'>Juli</option>';
                $html .= '<option value="8"  '.($this->getRecurrenceValue('BYMONTH') ==  8?'selected=selected':'').'>Augustus</option>';
                $html .= '<option value="9"  '.($this->getRecurrenceValue('BYMONTH') ==  9?'selected=selected':'').'>September</option>';
                $html .= '<option value="10" '.($this->getRecurrenceValue('BYMONTH') == 10?'selected=selected':'').'>Oktober</option>';
                $html .= '<option value="11" '.($this->getRecurrenceValue('BYMONTH') == 11?'selected=selected':'').'>November</option>';
                $html .= '<option value="12" '.($this->getRecurrenceValue('BYMONTH') == 12?'selected=selected':'').'>December</option>';
            $html .= '</select>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        
        return $html;
    }
    
}
