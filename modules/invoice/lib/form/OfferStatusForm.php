<?php

namespace invoice\form;


use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\TextField;
use core\forms\validator\NotEmptyValidator;
use core\ObjectContainer;
use invoice\service\OfferService;

class OfferStatusForm extends BaseForm {
    
    
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('offer_status_id');
        
        $this->addWidget( new HiddenField('offer_status_id', '', 'Id') );
        
        $this->addWidget( new CheckboxField('active', '', 'Actief'));
        $this->addWidget( new TextField('description', '', 'Omschrijving') );
        $this->addWidget( new CheckboxField('default_selected', '', 'Standaard gekozen'));

        $this->addValidator('description', new NotEmptyValidator());
        
        $this->addValidator('description', function($form) {
            $offerService = ObjectContainer::getInstance()->get(OfferService::class);
            
            $id = $form->getWidgetValue('offer_status_id');
            $desc = $form->getWidgetValue('description');
            
            $oss = $offerService->readAllOfferStatus();
            foreach($oss as $os) {
                if (strtolower($os->getDescription()) == strtolower($desc) && $os->getOfferStatusId() != $id) {
                    return 'Omschrijving bestaat reeds';
                }
            }
            
            return null;
        });
        
    }
    
}

