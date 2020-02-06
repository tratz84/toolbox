<?php


namespace payment\form;


use core\forms\BaseForm;
use core\forms\SelectField;
use payment\service\PaymentService;
use core\forms\container\TableContainer;
use core\forms\HtmlField;

class PaymentImportMappingForm extends BaseForm {
    
    public function __construct() {
        parent::__construct();
        
        $this->addPaymentMethods();
        
        $tc = new TableContainer('import-fields');
        $tc->addRow('Bij/af',          new SelectField('debet_credit',         '', array(), 'Bij/af'),          new HtmlField('example_debet_credit'));
        $tc->addRow('Bedrag',          new SelectField('amount',               '', array(), 'Bedrag'),          new HtmlField('example_amount'));
        $tc->addRow('Rekeningnr',      new SelectField('bankaccountno',        '', array(), 'Rekeningnr'),      new HtmlField('example_bankaccountno'));
        $tc->addRow('Tegenrekening',   new SelectField('bankaccountno_contra', '', array(), 'Tegenrekening'),   new HtmlField('example_bankaccountno_extra'));
        $tc->addRow('Transactiedatum', new SelectField('payment_date',         '', array(), 'Transactiedatum'), new HtmlField('example_payment_date'));
        $tc->addRow('Naam',            new SelectField('name',                 '', array(), 'Naam'),            new HtmlField('example_name'));
        
        $tc->addRow('Omschrijving',    new SelectField('description',          '', array(), 'Omschrijving'),    new HtmlField('example_description'));
        $tc->addRow('Code',            new SelectField('code',                 '', array(), 'Code'),            new HtmlField('example_code'));
        $tc->addRow('Mutatiesoort',    new SelectField('mutation_type',        '', array(), 'Mutatiesoort'),    new HtmlField('example_mutation_type'));
        
        $this->addWidget( $tc );
        
        
    }
    
    public function setImportHeaders($arrHeaders) {
        $widgetNames = array();
        $widgetNames[] = 'debet_credit';
        $widgetNames[] = 'amount';
        $widgetNames[] = 'bankaccountno';
        $widgetNames[] = 'bankaccountno_contra';
        $widgetNames[] = 'payment_date';
        $widgetNames[] = 'name';
        $widgetNames[] = 'description';
        $widgetNames[] = 'code';
        $widgetNames[] = 'mutation_type';
        
        $map = array();
        $map[''] = t('Make your choice');
        for($x=0; $x < count($arrHeaders); $x++) {
            $map['col-'.$x] = $arrHeaders[$x];
        }
        
        foreach($widgetNames as $wn) {
            $this->getWidget( $wn )->setOptionItems( $map );
        }
        
    }
    
    protected function addPaymentMethods() {
        $defaultValue = '';
        
        $pmService = object_container_get(PaymentService::class);
        $methods = $pmService->readActiveMethods();
        
        $map = array();
        $map[''] = t('Make your choice');
        foreach($methods as $m) {
            if ($m->getDefaultSelected())
                $defaultValue = $m->getPaymentMethodId();
            
            $map[ $m->getPaymentMethodId() ] = $m->getDescription();
        }
        
        $this->addWidget(new SelectField('payment_method_id', $defaultValue, $map, 'Betaalmethode'));
    }
    
    
}

