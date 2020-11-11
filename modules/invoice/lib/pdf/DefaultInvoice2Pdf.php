<?php

namespace invoice\pdf;

use customer\service\CustomerService;
use core\ObjectContainer;
use core\pdf\BasePdf;
use core\Context;
use invoice\service\InvoiceService;

class DefaultInvoice2Pdf extends BasePdf {
    
    /**
     * invoice\model\Invoice
     */
    protected $invoice;
    
    protected $intracommunautair = false;
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    public function setInvoice($invoice) { $this->invoice = $invoice; }
    
    
    
    public function render() {
        $this->AddPage();
        
        $this->showLogo();
        
        $this->printCompanyDetails();
        
        if ($this->invoice->getCompanyId()) {
            $invoiceService = ObjectContainer::getInstance()->get(InvoiceService::class);
            $companySettings = $invoiceService->readCompanySettings($this->invoice->getCompanyId());
            if ($companySettings) {
                if ($companySettings->getTaxShift()) {
                    $this->intracommunautair = true;
                }
            }
        }
        
        
        $this->SetFont('Arial', 'B', '16');
        $this->Ln();
        
        if ($this->invoice->getCreditInvoice()) {
            $this->Cell(190, $this->lineHeight, 'Credit'.strtolower(strOrder(1)));
        } else {
            $this->Cell(190, $this->lineHeight, strOrder(1));
        }
        $this->SetFont('Arial', '', '12');
        $this->Ln();
        $this->Ln();
        
        $this->Cell(100, $this->lineHeight, strOrder(1).'nummer: ' . $this->invoice->getInvoiceNumberText());
        if ($this->intracommunautair) {
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(90, $this->lineHeight, 'Intracommunautaire levering', 0, 0, 'R');
            $currentY = $this->GetY();
            $this->Ln();
            $this->Cell(190, $this->lineHeight, 'artikel 138, lid 1, Richtlijn 2006/112', 0, 0, 'R');
            $this->SetFont('Arial', '', '12');
            $this->SetY($currentY);
        }
        $this->Ln();
        $this->Cell(190, $this->lineHeight, strOrder(1).'datum: ' . format_date($this->invoice->getInvoiceDate(), 'd-m-Y'));
        $this->Ln();
        $this->Ln();
        $this->renderCustomerData();
        
        $this->Ln();
        $this->Cell(190, $this->lineHeight, 'Betreft: ' . $this->invoice->getSubject());
        $this->Ln();
        $this->Ln();
        
        
        $this->renderLines();
        
        if (trim($this->invoice->hasComment())) {
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->setFont('Arial', 'B', 12);
            $this->Cell(190, $this->lineHeight, 'Opmerking');
            $this->Ln();
            $this->setFont('Arial', '', 12);
            $this->MultiCell(190, $this->lineHeight, $this->invoice->getComment());
        }
        
        $this->AliasNbPages();
    }
    
    protected function renderCustomerData() {
        
        $cs = ObjectContainer::getInstance()->get(CustomerService::class);
        $customer = $cs->readCustomerAuto($this->invoice->getCompanyId(), $this->invoice->getPersonId());
        
        if (!$customer) {
            if ($this->invoice->getCompanyId()) {
                $this->Cell(190, $this->lineHeight, 'company-'.$this->invoice->getCompanyId());
            }
            if ($this->invoice->getPersonId()) {
                $this->Cell(190, $this->lineHeight, 'person-'.$this->invoice->getPersonId());
            }
            $this->Ln();
            return;
        }
        
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
        
        if ($this->intracommunautair) {
            $this->Cell(190, $this->lineHeight, 'Btw nr: ' . $customer->getField('vat_number'));
            $this->Ln();
        }
        
    }
    
    
    
    protected function printCompanyDetails() {
        $ctx = Context::getInstance();
        
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
        
        $lines = $this->invoice->getInvoiceLines();
        
        $lh = $this->lineHeight;
        $border=0;
        
        $this->SetFont('Arial', 'B', '12');
        $this->Cell(95, $lh, 'Omschrijving', $border, 0, 'L');
        $this->Cell(15, $lh, 'Aantal', $border, 0, 'R');
        $this->Cell(30, $lh, 'Prijs', $border, 0, 'R');
        $this->Cell(20, $lh, 'Btw', $border, 0, 'R');
        $this->Cell(30, $lh, 'Totaal', $border, 0, 'R');
        $this->Ln();
        
        $this->Line($this->GetX()+1, $this->GetY()+0.2, $this->GetX()+189, $this->GetY()+0.2);
        
        $this->SetY($this->GetY()+1);

        $this->SetFont('Arial', '', '12');
        foreach($lines as $l) {
            if (trim($l->getShortDescription()) == '' && $l->getPrice() == 0) {
                $this->Ln();
                continue;
            }
            
            $this->Cell(95, $lh, $l->getShortDescription(), $border, 0, 'L');
            
            if ($l->getAmount() != 0 || $l->getPrice() != 0) {
                $this->Cell(15, $lh, $this->formatNumber($l->getAmount()), $border, 0, 'R', true);
                $this->Cell(30, $lh, $this->formatPrice($l->getPrice()), $border, 0, 'R', true);
                $this->Cell(20, $lh, $l->getVatPercentage().'%', $border, 0, 'R', true);
                $this->Cell(30, $lh, $this->formatPrice(($l->getPrice()*$l->getAmount())), $border, 0, 'R', true);
            }
            
            $this->Ln();
        }
        
        $this->Ln();
        
        $this->Cell(190, $lh, 'Totaal excl. btw ' . $this->formatPrice($this->invoice->getTotalAmountExclVat()), $border, 0, 'R');
        
        foreach($this->invoice->getTotalVatByPercentage() as $p => $v) {
            if (intval($v*100) == 0) continue;
            $this->Ln();
            $this->Cell(190, $lh, 'Btw ' . format_percentage($p) . ' ' . $this->formatPrice($v), $border, 0, 'R');
        }
        
        $this->Ln();
        $this->Cell(190, $lh, 'Totaal ' . $this->formatPrice($this->invoice->getTotalAmountInclVat()), $border, 0, 'R');
        
        
    }
    
    
    
}



