<?php

namespace invoice\form;

use base\service\CompanyService;
use base\service\PersonService;
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

class OfferForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('offer_id');
        
        $this->addJavascript('edit-offer', '/js/invoice/editOffer.js');
        
        $this->addWidget( new HiddenField('offer_id', '', 'Id') );
        
//         $this->addWidget( new CheckboxField('accepted', '', 'Akkoord') );

        $this->addWidget( new HtmlField('offerNumberText', '', 'Offertenummer'));
        
        $this->addWidget( new DatePickerField('offer_date', '', 'Datum') );
        
        $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=base&c=customer&a=select2', 'Klant') );
        
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
    
    
    
    public function changes(DBObject $obj) {
        $c = parent::changes($obj);
        
        // customer_id-field is a special case, check it against Invoice::getCompanyId() & Invoice::getPersonId()
        if (is_a($obj, Offer::class)) {
            $customer_id = $this->getWidgetValue('customer_id');
            
            if ($obj->getCompanyId() && $customer_id != 'company-'.$obj->getCompanyId()) {
                $c[] = array('field_name' => 'customer_id', 'old' => 'company-'.$obj->getCompanyId(), 'new' => $customer_id);
            }
            else if ($obj->getPersonId() && $customer_id != 'person-'.$obj->getPersonId()) {
                $c[] = array('field_name' => 'customer_id', 'old' => 'person-'.$obj->getPersonId(), 'new' => $customer_id);
            }
            else if (!$obj->getCompanyId() && !$obj->getPersonId() && $customer_id) {
                $c[] = array('field_name' => 'customer_id', 'old' => '', 'new' => $customer_id);
            }
        }
        
        return $c;
    }
    
    public function bind($obj) {
        parent::bind($obj);
        
        $companyId = null;
        $personId = null;
        
        $customerWidget = $this->getWidget('customer_id');
        
        if (is_a($obj, Offer::class)) {
            $companyId = $obj->getCompanyId();
            $personId = $obj->getPersonId();
        }

        
        if (is_array($obj) && isset($obj['customer_id'])) {
            
            if (strpos($obj['customer_id'], 'company-') === 0) {
                $companyId = str_replace('company-', '', $obj['customer_id']);
            }
            else if (strpos($obj['customer_id'], 'person-') === 0) {
                $personId = str_replace('person-', '', $obj['customer_id']);
            }
            
        }
        
        if ($companyId) {
            $customerWidget->setValue('company-'.$companyId);
            
            $cs = ObjectContainer::getInstance()->get(CompanyService::class);
            $name = $cs->getCompanyName($companyId);
            
            $customerWidget->setDefaultText( $name );
        }
        else if ($personId) {
            $customerWidget->setValue('person-'.$personId);
            
            $ps = ObjectContainer::getInstance()->get(PersonService::class);
            $fullname = $ps->getFullname($personId);
            
            $customerWidget->setDefaultText( $fullname );
        }
        
        
    }
    
    public function fill($obj, $fields=array()) {
        parent::fill($obj, $fields);
        
        if (is_a($obj, Offer::class)) {
            $v = $this->getWidget('customer_id')->getValue();
            $obj->setCompanyId(0);
            $obj->setPersonId(0);
            
            if (strpos($v, 'company-') === 0) {
                $obj->setCompanyId( str_replace('company-', '', $v) );
            }
            
            if (strpos($v, 'person-') === 0) {
                $obj->setPersonId( str_replace('person-', '', $v) );
            }
        }
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

