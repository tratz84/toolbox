<?php

namespace invoice\form;

use customer\service\CompanyService;
use customer\service\PersonService;
use core\ObjectContainer;
use core\db\DBObject;
use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\DynamicSelectField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\TextareaField;
use core\forms\validator\MinimumLinesValidator;
use core\forms\validator\NotContainsValidator;
use core\forms\validator\NotEmptyValidator;
use invoice\model\Invoice;
use invoice\model\Offer;
use invoice\service\OfferService;
use customer\forms\CustomerSelectWidget;

class OfferForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('offer_id');
        
        $this->addJavascript('edit-offer', '/js/invoice/editOffer.js');
        
        $this->addWidget( new HiddenField('offer_id', '', 'Id') );
        
//         $this->addWidget( new CheckboxField('accepted', '', 'Akkoord') );

        $this->addWidget( new HtmlField('offerNumberText', '', 'Offertenummer'));
        
        $this->addWidget( new DatePickerField('offer_date', '', 'Datum') );
        
        $this->addWidget( new CustomerSelectWidget() );
//         $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=customer&c=customer&a=select2', 'Klant') );
        
        $this->addOfferStatus();
        $this->addWidget( new TextField('subject', '', 'Betreft') );
        
        
        $this->addWidget( new ListOfferLineWidget('offerLines') );
        
//         $this->addWidget( new EuroField('deposit', '', 'Waarborgsom') );
//         $this->addWidget( new EuroField('payment_upfront', '', 'Vooraf te betalen') );
        
        $this->addWidget( new TextareaField('comment', '', 'Opmerking onder offerte') );
        $this->addWidget( new TextareaField('note', '', 'Interne notitie') );
        
        $this->addValidator('customer_id', new NotContainsValidator(array('', '0', 'Maak uw keuze')));
        $this->addValidator('subject', new NotEmptyValidator());
        $this->addValidator('offerLines', new MinimumLinesValidator());
    }
    
    protected function addOfferStatus() {
        
        $offerService = ObjectContainer::getInstance()->get(OfferService::class);
        $status = $offerService->readActiveOfferStatus();
        
        $map = array();
        $defaultSelectedId = null;
        foreach($status as $s) {
            if ($defaultSelectedId == null || $s->getDefaultSelected()) {
                $defaultSelectedId = $s->getOfferStatusId();
            }
            
            $map[$s->getOfferStatusId()] = $s->getDescription();
        }
        
        $this->addWidget( new SelectField('offer_status_id', $defaultSelectedId, $map, 'Status') );
    }
    
}

