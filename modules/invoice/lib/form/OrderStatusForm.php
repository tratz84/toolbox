<?php

namespace invoice\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;
use invoice\service\OrderService;

class OrderStatusForm extends BaseForm {
    
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('order_status_id');
        
        $this->addWidget( new HiddenField('order_status_id', '', 'Id') );
        
        $this->addWidget( new CheckboxField('active', '', 'Actief'));
        $this->addWidget( new TextField('description', '', 'Omschrijving') );
        $this->addWidget( new CheckboxField('default_selected', '', 'Standaard gekozen'));
        
        $this->addValidator('description', new NotEmptyValidator());
        
        $this->addValidator('description', function($form) {
            $orderService = object_container_get( OrderService::class );
            
            $id = $form->getWidgetValue('order_status_id');
            $desc = $form->getWidgetValue('description');
            
            $oss = $orderService->readAllOrderStatus();
            foreach($oss as $os) {
                if (strtolower($os->getDescription()) == strtolower($desc) && $os->getOrderStatusId() != $id) {
                    return 'Omschrijving bestaat reeds';
                }
            }
            
            return null;
        });
            
    }
    
}

