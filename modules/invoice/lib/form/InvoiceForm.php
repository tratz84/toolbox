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
use DateTime;
use invoice\model\Invoice;
use invoice\service\InvoiceService;
use core\forms\CheckboxField;
use customer\forms\CustomerSelectWidget;

class InvoiceForm extends BaseForm {

    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('invoice_id');
        
        $this->addJavascript('invoice', '/js/invoice/editInvoice.js');

        $this->addWidget( new HiddenField('invoice_id', '', 'Id') );
        $this->addWidget( new HiddenField('ref_invoice_id') );

//         $this->addWidget( new CheckboxField('accepted', '', 'Akkoord') );

        $this->addWidget( new HtmlField('invoiceNumberText', '', strOrder(1).'nummer'));

        $this->addWidget(new CheckboxField('credit_invoice', '', 'Creditfactuur'));
        $this->getWidget('credit_invoice')->setInfoText('Factuur markeren als een credit-factuur?');
        
        $this->addWidget( new DatePickerField('invoice_date', '', 'Datum') );

        $this->addWidget( new CustomerSelectWidget() );
//         $this->addWidget( new DynamicSelectField('customer_id', '', 'Maak uw keuze', '/?m=customer&c=customer&a=select2', 'Klant') );

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
