<?php

namespace payment\pdf;

use customer\service\CustomerService;
use core\ObjectContainer;
use core\pdf\BasePdf;
use payment\service\PaymentService;



class PaymentPdf extends BasePdf {
    
    protected static $mapPaymentMethodNames = null;
    
    /**
     * payment\model\Payment
     */
    protected $payment;
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    public function setPayment($payment) { $this->payment = $payment; }
    
    
    
    public function render() {
        $this->AddPage();
        
        $this->showLogo();
        
        $this->printCompanyDetails();
        
        
        $this->SetFont('Arial', 'B', '16');
        $this->Ln();
        
        $this->Cell(190, $this->lineHeight, 'Betaling');
        $this->SetFont('Arial', '', '12');
        $this->Ln();
        $this->Ln();
        
        $this->Cell(100, $this->lineHeight, 'Betalingsnummer: ' . $this->payment->getPaymentNumberText());
        $this->Ln();
        $this->Cell(190, $this->lineHeight, 'Datum: ' . format_date($this->payment->getPaymentDate(), 'd-m-Y'));
        $this->Ln();
        $this->Ln();
        $this->renderCustomerData();
        
        $this->Ln();
        $this->Cell(190, $this->lineHeight, 'Betreft: ' . $this->payment->getDescription());
        $this->Ln();
        $this->Ln();
        
        
        $this->renderLines();
        
        if (trim($this->payment->hasNote())) {
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->setFont('Arial', 'B', 12);
            $this->Cell(190, $this->lineHeight, 'Opmerking');
            $this->Ln();
            $this->setFont('Arial', '', 12);
            $this->MultiCell(190, $this->lineHeight, $this->payment->getNote());
        }
        
        $this->AliasNbPages();
    }
    
    protected function renderCustomerData() {
        
        $cs = ObjectContainer::getInstance()->get(CustomerService::class);
        $customer = $cs->readCustomerAuto($this->payment->getCompanyId(), $this->payment->getPersonId());
        
        if (!$customer) return;
        
        $this->Cell(190, $this->lineHeight, $customer->getName());
        $this->Ln();
        foreach($customer->getAddressList() as $a) {
            
            $l = trim($a->getStreetWithNumber());
            if ($l) {
                $this->Cell(190, $this->lineHeight, $l);
                $this->Ln();
            }
            
            $l = trim($a->getZipcode() . ' ' . $a->getCity());
            if ($l) {
                $this->Cell(190, $this->lineHeight, $l);
                $this->Ln();
            }
            
            // first address only
            break;
        }
        
    }
    
    
    
    protected function printCompanyDetails() {
        $ctx = \core\Context::getInstance();
        
        $y = $this->GetY();
        
        $left = array();
        if (trim( trim($ctx->getCompanyZipcode()) . ' ' . trim($ctx->getCompanyCity()) ))
            $left[] = trim( trim($ctx->getCompanyZipcode()) . ' ' . trim($ctx->getCompanyCity()) );
        if (trim( $ctx->getCompanyStreet() ))
            $left[] = trim( $ctx->getCompanyStreet() );
        if (trim( $ctx->getCompanyCocNumber() ))
            $left[] = 'Kvknr: ' . trim( $ctx->getCompanyCocNumber() );
        if (trim( $ctx->getCompanyVat() ))
            $left[] = 'BTW: ' . trim( $ctx->getCompanyVat() );
        
        $right = array();
        if (trim($ctx->getCompanyPhone()))
            $right[] = 'Tel: ' . trim($ctx->getCompanyPhone());
        if (trim($ctx->getCompanyEmail()))
            $right[] = 'Mail: ' . trim($ctx->getCompanyEmail());
        if (trim($ctx->getCompanyIBAN()))
            $right[] = trim($ctx->getCompanyIBAN());
            
        
        $this->SetY(15);
        for($x=0; $x < max([count($left), count($right)]); $x++) {
            $l = $r = '';
            
            if (isset($left[$x])) $l = $left[$x];
            if (isset($right[$x])) $r = $right[$x];
            
            $this->Cell(95, $this->lineHeight, $l);
            $this->Cell(95.25, $this->lineHeight, $r, 0, 0, 'R');
            $this->Ln();
        }
        
        $this->SetY( $this->lineHeight * 8 );
    }
    
    
    protected function renderLines() {
        
        $lines = $this->payment->getPaymentLines();
        
        $lh = $this->lineHeight;
        $border=0;
        
        $this->SetFont('Arial', 'B', '12');
        $this->Cell(35,  $lh, 'Methode', $border, 0, 'L');
        $this->Cell(115, $lh, 'Opmerking', $border, 0, 'l');
        $this->Cell(40,  $lh, 'Bedrag', $border, 0, 'R');
        $this->Ln();
        
        $this->Line($this->GetX()+1, $this->GetY()+0.2, $this->GetX()+189, $this->GetY()+0.2);
        
        $this->SetY($this->GetY()+1);
        
        $this->SetFont('Arial', '', '12');
        foreach($lines as $l) {
            
            $pm_id = $l->getField('payment_method_id');
            $pm_name = self::getPaymentMethodName( $pm_id );
            
            $this->Cell(35,  $lh, $pm_name, $border, 0, 'L');
            $this->Cell(115, $lh, $l->getDescription1(), $border, 0, 'L', true);
            $this->Cell(40,  $lh, $this->formatPrice($l->getAmount()), $border, 0, 'R', true);
            
            $this->Ln();
        }
        
        $this->Ln();
        
        $this->Ln();
        $this->Cell(190, $lh, 'Totaal ' . $this->formatPrice($this->payment->getAmount()), $border, 0, 'R');
    }
    
    
    
    protected static function getPaymentMethodName($paymentMethodId) {
        if (self::$mapPaymentMethodNames === null) {
            $ps = object_container_get(PaymentService::class);
            
            self::$mapPaymentMethodNames = array();
            
            $pms = $ps->readAllMethods();
            foreach($pms as $pm) {
                self::$mapPaymentMethodNames[ $pm->getPaymentMethodId() ] = $pm->getDescription();
            }
        }
        
        
        if (isset(self::$mapPaymentMethodNames[$paymentMethodId])) {
            return self::$mapPaymentMethodNames[$paymentMethodId];
        } else {
            return $paymentMethodId;
        }
    }
    
}

