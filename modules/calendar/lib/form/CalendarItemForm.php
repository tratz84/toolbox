<?php

namespace calendar\form;

use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\DatePickerField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\HiddenField;
use core\forms\TimePickerField;
use core\forms\validator\NotEmptyValidator;
use core\forms\SelectField;
use calendar\model\CalendarItem;
use calendar\CalendarSettings;
use base\forms\CustomerSelectWidget;
use core\forms\InternalField;

class CalendarItemForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        /** @var CalendarSettings $calendarSettings */
        $calendarSettings = object_container_get(CalendarSettings::class);
        
        $this->addWidget(new HiddenField('edit_derived_item'));
        $this->addWidget(new HiddenField('calendar_id'));
        $this->addWidget(new HiddenField('calendar_item_id'));
        
        $this->addWidget(new HiddenField('selected_date'));                         // selected date in calendar
        
        if ($calendarSettings->calendarItemActionsEnabled()) {
            $map_itemActions = CalendarItem::getItemActions();
            $this->addWidget(new SelectField('item_action', '', $map_itemActions, t('Current action')));
        }
        
        $this->addWidget(new CustomerSelectWidget('customer_id'));
        
        $this->addWidget(new TextField('title',    '', 'Titel'));
        $this->addWidget(new TextField('location', '', 'Locatie'));
        
        $this->addWidget(new CheckboxField('all_day',   '', 'Hele dag'));
        $this->addWidget(new CheckboxField('private',   '', 'Privé'));
        $this->addWidget(new CheckboxField('cancelled', '', 'Geannuleerd'));
        
        $this->addWidget(new DatePickerField('start_date', '', 'Startdatum'));
        $this->addWidget(new TimePickerField('start_time', '', 'Starttijd'));
        $this->addWidget(new DatePickerField('end_date',   '', 'Einddatum'));
        $this->addWidget(new TimePickerField('end_time',   '', 'Eindtijd'));
        
        $this->addWidget(new ReminderInputField('recurrence_type', '', 'Herhaling'));
        
        $this->addWidget(new TextareaField('message', '', 'Bericht'));
        
        
        $this->addValidator('title', new NotEmptyValidator());
        $this->addValidator('start_date', new NotEmptyValidator());
        
        $this->addValidator('end_date', function($form) {
            $startdate = (int)format_date($form->getWidget('start_date')->getValue(), 'Ymd');
            $enddate   = (int)format_date($form->getWidget('end_date')->getValue(), 'Ymd');
            
            if ($enddate && $enddate < $startdate) {
                return 'Einddatum ligt voor startdatum';
            }
            
            return null;
        });
        
        $this->addValidator('end_time', function($form) {
            $startdate = $form->getWidgetValue('start_date');
            $enddate   = $form->getWidgetValue('end_date');
            $starttime = $form->getWidgetValue('start_time');
            $endtime   = $form->getWidgetValue('end_time');
            
            // no start or end-time? => skip check  
            if (!$starttime || !$endtime)
                return null;
            
            // end-date not same as start-date? => skip check
            if ($enddate != '' && $startdate != $enddate) {
                return null;
            }

            
            $st = (int)str_replace(':', '', $starttime);
            $et = (int)str_replace(':', '', $endtime);
            
            if ($et < $st) {
                return 'Eindtijd ligt voor starttijd';
            }
            
            
            return null;
        });
        
        
        $this->addValidator('recurrence_type', function($form) {
            
            if ($form->getWidgetValue('recurrence_type') == 'weekly') {
                $request = $form->getWidget('recurrence_type')->getRequest();
                
                if (isset($request['weekly_mo']) == false 
                    && isset($request['weekly_tu']) == false 
                    && isset($request['weekly_we']) == false 
                    && isset($request['weekly_th']) == false 
                    && isset($request['weekly_fr']) == false 
                    && isset($request['weekly_sa']) == false 
                    && isset($request['weekly_su']) == false) {
                    return 'Verplicht een dag te kiezen';
                }
            }
            
            return null;
        });
    }
    
    
}