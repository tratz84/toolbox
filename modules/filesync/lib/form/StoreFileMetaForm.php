<?php

namespace filesync\form;



use base\service\CompanyService;
use base\service\PersonService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\DatePickerField;
use core\forms\DynamicSelectField;
use core\forms\HiddenField;
use core\forms\HtmlField;
use core\forms\TextField;
use core\forms\TextareaField;
use invoice\model\Offer;
use filesync\model\StoreFileMeta;

class StoreFileMetaForm extends BaseForm {
    
    
    public function __construct() {
        
        $this->addKeyField('store_file_id');
        
        $this->addWidget(new HiddenField('store_id'));
        $this->addWidget(new HiddenField('store_file_id'));
        $this->addWidget(new HtmlField('filename', '', 'Bestandsnaam'));
        $this->addWidget(new DatePickerField('document_date', '', 'Document datum'));
        $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=base&c=customer&a=select2', 'Klant') );
        $this->addWidget(new TextField('subject', '', 'Onderwerp'));
        $this->addWidget(new TextareaField('long_description', '', 'Lange omschrijving'));
        
        
    }
    
    
    
    public function bind($obj) {
        parent::bind($obj);
        
        $companyId = null;
        $personId = null;
        
        $customerWidget = $this->getWidget('customer_id');
        
        if (is_a($obj, StoreFileMeta::class)) {
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
    
}

