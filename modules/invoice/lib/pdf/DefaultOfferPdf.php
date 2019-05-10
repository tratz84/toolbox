<?php

namespace invoice\pdf;

use base\service\CustomerService;
use core\ObjectContainer;
use core\pdf\BasePdf;

class DefaultOfferPdf extends DefaultInvoicePdf {
    
    /**
     * invoice\model\Offer
     */
    protected $offer;
    
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    public function setOffer($offer) { $this->offer = $offer; }
    
    
    
    public function render() {
        $this->AddPage();
        
        $this->showLogo();
        
        $this->printCompanyDetails();
        
        $this->renderCustomerData();

        $this->SetFont('Arial', 'B', '16');
        $this->Cell(190, $this->lineHeight, 'Offerte');
        $this->Ln();
        $this->Ln();
        
        $this->SetFont('Arial', '', '12');
        
        if (valid_date($this->offer->getOfferDate())) {
            $this->Cell(190, $this->lineHeight, 'Datum: ' . format_date($this->offer->getOfferDate(), 'd-m-Y'));
            $this->Ln();
        }
        $this->Cell(190, $this->lineHeight, 'Betreft: ' . $this->offer->getSubject());
        $this->Ln();
        $this->Ln();
        
        $this->renderLines();

        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Cell(190, $this->lineHeight, 'Handtekening:');
        $this->Ln();
        
        if (trim($this->offer->getComment())) {
            $this->SetFont('Arial', 'I', '8');
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->Ln();
//             $this->Cell(190, $this->lineHeight, 'Notitie:');
            $this->Ln();
            $this->MultiCell(190, $this->lineHeight, $this->offer->getComment());
            
            $this->SetFont('Arial', '', '12');
        }
        
        $this->SetTextColor(255, 255, 255);
        $this->Cell(190, $this->lineHeight, '[[ s|1 ]]', 0, 0, '', true);
        $this->SetTextColor(0, 0, 0);
        
        $this->AliasNbPages();
    }
    
    protected function renderCustomerData() {
        
        $cs = ObjectContainer::getInstance()->get(CustomerService::class);
        $customer = $cs->readCustomerAuto($this->offer->getCompanyId(), $this->offer->getPersonId());
        
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
        }
        
        $this->Ln();
        $this->Ln();
        
    }
    
    
    protected function renderLines() {
        
        $lines = $this->offer->getOfferLines();
        
        $lh = $this->lineHeight;
        $border=0;
        
        $prevLineType = null;

        $this->SetFont('Arial', 'B', '12');
        $this->Cell(80, $lh, 'Omschrijving', $border, 0, 'L');
        $this->Cell(20, $lh, 'Aantal', $border, 0, 'R');
        $this->Cell(30, $lh, 'Prijs', $border, 0, 'R');
        $this->Cell(30, $lh, 'Btw', $border, 0, 'R');
        $this->Cell(30, $lh, 'Totaal', $border, 0, 'R');
        $this->Ln();
        $this->Line($this->GetX()+1, $this->GetY()+0.2, $this->GetX()+189, $this->GetY()+0.2);
        $this->SetY($this->GetY()+1);
        
        $this->SetFont('Arial', '', '12');
        
        foreach($lines as $l) {
            if ($prevLineType != null && $prevLineType != $l->getLineType()) {
//                 $this->Ln();
            }
            
            if (trim($l->getShortDescription2())) {
                $this->Cell(65, $lh, $l->getShortDescription(), $border, 0, 'L');
                $this->Cell(65, $lh, $l->getShortDescription2(), $border, 0, 'L');
            } else {
                $this->Cell(80, $lh, $l->getShortDescription(), $border, 0, 'L');
            }
            
            if ($l->getLineType() != 'text') {
                $this->Cell(20, $lh, $l->getAmount(), $border, 0, 'R');
                $this->Cell(30, $lh, $this->formatPrice($l->getPrice()), $border, 0, 'R');
                $this->Cell(30, $lh, $this->formatPrice($l->getVatAmount()), $border, 0, 'R');
                
                $this->Cell(30, $lh, $this->formatPrice($l->getTotalPriceInclVat()), $border, 0, 'R');
            }
            
            $prevLineType = $l->getLineType();
            
            $this->Ln();
        }
        
        $this->Ln();
        
        $this->Cell(190, $lh, 'Totaal excl. btw ' . $this->formatPrice($this->offer->getTotalAmountExclVat()), $border, 0, 'R');
        
        foreach($this->offer->getTotalVatByPercentage() as $p => $v) {
            if (intval($v*100) == 0) continue;
            $this->Ln();
            $this->Cell(190, $lh, 'Btw ' . format_percentage($p) . ' ' . $this->formatPrice($v), $border, 0, 'R');
        }
        
        $this->Ln();
        $this->Cell(190, $lh, 'Totaal ' . $this->formatPrice($this->offer->getTotalAmountInclVat()), $border, 0, 'R');
        
        
    }
    
    
    
}



