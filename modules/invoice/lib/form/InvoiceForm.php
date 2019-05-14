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
use DateTime;
use invoice\model\Invoice;
use invoice\service\InvoiceService;
use core\forms\CheckboxField;

class InvoiceForm extends BaseForm {

    public function __construct() {
        
        $this->addKeyField('invoice_id');
        
        $this->addJavascript('invoice', '/js/invoice/editInvoice.js');

        $this->addWidget( new HiddenField('invoice_id', '', 'Id') );
        $this->addWidget( new HiddenField('ref_invoice_id') );

//         $this->addWidget( new CheckboxField('accepted', '', 'Akkoord') );

        $this->addWidget( new HtmlField('invoiceNumberText', '', strOrder(1).'nummer'));

        $this->addWidget(new CheckboxField('credit_invoice', '', 'Creditfactuur'));
        $this->getWidget('credit_invoice')->setInfoText('Factuur markeren als een credit-factuur?');
        
        $this->addWidget( new DatePickerField('invoice_date', '', 'Datum') );

        $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=base&c=customer&a=select2', 'Klant') );

        $this->addInvoiceStatus();
        $this->addWidget( new TextField('subject', '', 'Betreft') );


        $this->addWidget( new ListInvoiceLineWidget('invoiceLines') );

//         $this->addWidget( new EuroField('deposit', '', 'Waarborgsom') );
//         $this->addWidget( new EuroField('payment_upfront', '', 'Vooraf te betalen') );

        $this->addWidget( new TextareaField('comment', '', 'Opmerking onder factuur') );
        $this->addWidget( new TextareaField('note', '', 'Interne notitie') );

        $this->addValidator('customer_id', new NotContainsValidator(array('', '0', 'Maak uw keuze')));
//         $this->addValidator('subject', new NotEmptyValidator());
        $this->addValidator('invoiceLines', new MinimumLinesValidator());

        $invoiceSettings = \core\ObjectContainer::getInstance()->get(\invoice\InvoiceSettings::class);
        if ($invoiceSettings->getOrderType() == 'invoice') {
            $this->addValidator('invoice_date', function($form) {
                $invoiceDate = $form->getWidgetValue('invoice_date');
                $invoiceId = $form->getWidgetValue('invoice_id');

                $invoiceService = \core\ObjectContainer::getInstance()->get(\invoice\service\InvoiceService::class);

                // validate invoice date
                if (valid_date($invoiceDate) == false) {
                    return 'Ongeldige datum';
                }

                if (!$invoiceService->validateInvoiceDate($invoiceId, $invoiceDate)) {
                    return 'Ongeldige factuurdatum';
                }
                
                // todo: max X-dagen in de toekomst?
                $dt1 = new DateTime( date('Y-m-d') );
                $dt2 = new DateTime( format_date($invoiceDate, 'Y-m-d') );
                
                $diff = $dt1->diff($dt2, false);
                if (!$diff->invert && $diff->days > 10) {
                    return 'Datum ligt te ver in de toekomst';
                }

                return null;
            });
        }


    }

    public function changes(DBObject $obj) {
        $c = parent::changes($obj);
        
        // customer_id-field is a special case, check it against Invoice::getCompanyId() & Invoice::getPersonId()
        if (is_a($obj, Invoice::class)) {
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

        if (is_a($obj, Invoice::class)) {
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

        if (is_a($obj, Invoice::class)) {
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

    protected function addInvoiceStatus() {

        $invoiceService = ObjectContainer::getInstance()->get(InvoiceService::class);
        $status = $invoiceService->readActiveInvoiceStatus();

        $map = array();
        $defaultSelectedId = null;
        foreach($status as $s) {
            if ($defaultSelectedId == null || $s->getDefaultSelected()) {
                $defaultSelectedId = $s->getInvoiceStatusId();
            }

            $map[$s->getInvoiceStatusId()] = $s->getDescription();
        }

        $this->addWidget( new SelectField('invoice_status_id', $defaultSelectedId, $map, 'Status') );
    }

}
