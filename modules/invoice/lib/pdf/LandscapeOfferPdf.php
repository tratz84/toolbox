<?php

namespace invoice\pdf;

use core\Context;
use core\pdf\BasePdf;

class LandscapeOfferPdf extends BasePdf {
    
    
    protected $colorHeader = array(255, 0, 0);
    protected $colorCellBg1 = array(232, 208, 208);
    protected $colorCellBg2 = array(244, 233, 233);
    protected $colorCellBg3 = array(255, 255, 255);
    
    /**
     * invoice\model\Offer
     */
    protected $offer;
    
    
    
    public function __construct() {
        parent::__construct('L');
        
        $pdfsettings = object_meta_get('invoice-pdfsettings', 0, 'color');
        if ($pdfsettings) {
            if (isset($pdfsettings['color_frame'])) {
                $c = hex2rgb($pdfsettings['color_frame']);
                if ($c) $this->colorHeader = $c;
            }
            
            if (isset($pdfsettings['color_row1'])) {
                $c = hex2rgb($pdfsettings['color_row1']);
                if ($c) $this->colorCellBg1 = $c;
            }
            
            if (isset($pdfsettings['color_row2'])) {
                $c = hex2rgb($pdfsettings['color_row2']);
                if ($c) $this->colorCellBg2 = $c;
            }
        }
    }
    
    public function setOffer($offer) { $this->offer = $offer; }
    
    
    
    public function render() {
        $this->AddPage();
        
        $this->renderHeader();
        
        $this->renderLines();
        
        $this->renderNote();
        
        $this->AliasNbPages();
    }
    
    
    protected function renderHeader() {
        $this->renderCompanyData();
        
        $this->renderCustomerData();
    }
    
    protected function renderCompanyData() {
        $ctx = Context::getInstance();
        
        $this->Cell(150, $this->lineHeight, 'OFFERTE');
        $this->Ln();
        
        $height = $this->showLogo();
        
        $this->SetFont('Arial', '', '8');
        $lh = 3.5;
        
        if ($ctx->getCompanyStreet()) {
            $this->Cell(150, $lh, $ctx->getCompanyStreet());
            $this->Ln();
        }
        if ($ctx->getCompanyZipcode() || $ctx->getCompanyCity()) {
            $this->Cell(150, $lh, trim($ctx->getCompanyZipcode() . ' ' . $ctx->getCompanyCity()));
            $this->Ln();
        }
        if ($ctx->getCompanyPhone()) {
            $this->Cell(150, $lh, $ctx->getCompanyPhone());
            $this->Ln();
        }
        if ($ctx->getCompanyEmail()) {
            $this->Cell(150, $lh, $ctx->getCompanyEmail());
            $this->Ln();
        }
        $this->Ln();
        if ($ctx->getCompanyCocNumber()) {
            $this->Cell(150, $lh, 'KvK '.$ctx->getCompanyCocNumber());
            $this->Ln();
        }
        if ($ctx->getCompanyVat()) {
            $this->Cell(150, $lh, 'BTW '.$ctx->getCompanyVat());
            $this->Ln();
        }
        if ($ctx->getCompanyIBAN()) {
            $this->Cell(150, $lh, 'IBAN '.$ctx->getCompanyIBAN());
            $this->Ln();
        }
        
    }
    
    protected function showLogo() {
        $ctx = Context::getInstance();
        
        $f = get_data_file( $ctx->getLogoFile() );
        if (!$f) {
            $this->SetY(38.5);
            return;
        }
        
        $imageinfo=array();
        $x = getimagesize($f, $imageinfo);
        
        $w = $x[0];
        $h = $x[1];
        
        
        // page = 210 x 297
        
        $iw = 40;
        $ih = $h / $w * $iw;
        
//         $posy = 15;
        
        $this->Image($f, $this->GetX()+1.2, $this->GetY(), $iw, $ih);
        
        $this->SetY($this->GetY() + $ih );
        
        return $ih;
    }
    
    protected function renderCustomerData() {
        
        $customer = $this->offer->getCustomer();
        
        $this->lineHeight = 7;
        $captionWidth = 35;
        $fieldWidth = 57;
        
        $this->SetFont('Arial', '', '10');

        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor($this->colorHeader[0], $this->colorHeader[1], $this->colorHeader[2]);
        $this->SetXY(100, 10);
        $this->Cell($captionWidth, $this->lineHeight, 'Klantdetails', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $this->Cell($fieldWidth, $this->lineHeight, 'Offertenummer: '.$this->offer->getOfferNumberText(), 0, 0, '', true);


        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg1[0], $this->colorCellBg1[1], $this->colorCellBg1[2]);
        $this->SetXY(100, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'Naam bedrijf', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $customerName = $this->offer->getCustomer() ? $this->offer->getCustomer()->getName() : '';
        $this->Cell($fieldWidth, $this->lineHeight, $customerName, 0, 0, '', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg2[0], $this->colorCellBg2[1], $this->colorCellBg2[2]);
        $this->SetXY(100, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'KvK nummer', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $cocNumber = $this->offer->getCustomer() ? $this->offer->getCustomer()->getField('coc_number') : '';
        $this->Cell($fieldWidth, $this->lineHeight, $cocNumber, 0, 0, '', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg1[0], $this->colorCellBg1[1], $this->colorCellBg1[2]);
        $this->SetXY(100, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'Contactpersoon', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $contactPerson = $this->offer->getCustomer() ? $this->offer->getCustomer()->getField('contact_person') : '';
        $this->Cell($fieldWidth, $this->lineHeight, $contactPerson, 0, 0, '', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg2[0], $this->colorCellBg2[1], $this->colorCellBg2[2]);
        $this->SetXY(100, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'Adres', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $address = '';
        $postcodePlaats = '';
        if ($this->offer->getCustomer() && count($this->offer->getCustomer()->getAddressList())) {
            $addressList = $this->offer->getCustomer()->getAddressList();
            $address = trim($addressList[0]->getStreet() . ' ' . $addressList[0]->getStreetNo());
            
            $postcodePlaats = trim($addressList[0]->getZipcode() . ' ' . $addressList[0]->getCity());
        }
        $this->Cell($fieldWidth, $this->lineHeight, $address, 0, 0, '', true);
        

        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg1[0], $this->colorCellBg1[1], $this->colorCellBg1[2]);
        $this->SetXY(100, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'Postcode, Plaats', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $this->Cell($fieldWidth, $this->lineHeight, $postcodePlaats, 0, 0, '', true);
        
        
        
        $startX = 194;
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg1[0], $this->colorCellBg1[1], $this->colorCellBg1[2]);
        $this->SetXY($startX, 10);
        $this->Cell($captionWidth, $this->lineHeight, 'IBAN nummer', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $iban = $this->offer->getCustomer() ? $this->offer->getCustomer()->getField('iban') : '';
        $this->Cell($fieldWidth, $this->lineHeight, $iban, 0, 0, '', true);
        
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg2[0], $this->colorCellBg2[1], $this->colorCellBg2[2]);
        $this->SetXY($startX, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'E-mail', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $email = '';
        if ($this->offer->getCustomer() && count($this->offer->getCustomer()->getEmailList())) {
            $el = $this->offer->getCustomer()->getEmailList();
            $email = $el[0]->getEmailAddress();
        }
        $this->Cell($fieldWidth, $this->lineHeight, $email, 0, 0, '', true);
        

        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg1[0], $this->colorCellBg1[1], $this->colorCellBg1[2]);
        $this->SetXY($startX, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'Telefoon', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $phonenr = '';
        if ($this->offer->getCustomer() && count($this->offer->getCustomer()->getPhoneList())) {
            $pl = $this->offer->getCustomer()->getPhoneList();
            $phonenr = $pl[0]->getPhonenr();
        }
        $this->Cell($fieldWidth, $this->lineHeight, $phonenr, 0, 0, '', true);

        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg2[0], $this->colorCellBg2[1], $this->colorCellBg2[2]);
        $this->SetXY($startX, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'Periode', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $period = '';
        $this->Cell($fieldWidth, $this->lineHeight, $period, 0, 0, '', true);

        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg1[0], $this->colorCellBg1[1], $this->colorCellBg1[2]);
        $this->SetXY($startX, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'Datum', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        $datum = format_date($this->offer->getOfferDate(), 'd-m-Y');
        $this->Cell($fieldWidth, $this->lineHeight, $datum, 0, 0, '', true);

        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($this->colorCellBg2[0], $this->colorCellBg2[1], $this->colorCellBg2[2]);
        $this->SetXY($startX, $this->GetY() + $this->lineHeight + 0.5);
        $this->Cell($captionWidth, $this->lineHeight, 'Handtekening', 0, 0, '', true);
        $this->SetX($this->GetX()+1);
        
        $this->SetTextColor($this->colorCellBg2[0], $this->colorCellBg2[1], $this->colorCellBg2[2]);
        $this->Cell($fieldWidth, $this->lineHeight, '[[ s|1 ]]', 0, 0, '', true);
        $this->SetTextColor(0, 0, 0);
        
    }
    
    
    protected function renderLines() {
        $this->SetFont('Arial', '', '12');
        
        $this->SetXY(10, 59);
        
        $lines = $this->offer->getOfferLines();
        
        $lh = $this->lineHeight-0.2;
        $border=0;
        
        $this->SetFillColor($this->colorHeader[0], $this->colorHeader[1], $this->colorHeader[2]);
        $this->SetTextColor(255, 255, 255);
        
        $this->Cell(172, $lh, 'Omschrijving',  0, 0, '', true);
        $this->Cell(15, $lh, 'Aantal',  0, 0, 'R', true);
        $this->Cell(30, $lh, 'Prijs',  0, 0, 'R', true);
        $this->Cell(30, $lh, 'Btw',  0, 0, 'R', true);
        $this->Cell(30, $lh, 'Totaal',  0, 0, 'R', true);
        $this->Ln();
        
        $rowColors = array();
        $rowColors[] = $this->colorCellBg2;
        $rowColors[] = array(255, 255, 255);
        
        $this->SetFont('Arial', '', '9');
        $this->SetTextColor(0, 0, 0);
        $this->SetX($this->GetX()+0.1);
        
        
        $linesToDraw = array();
        $prevLineType = null;
        foreach($lines as $l) {
            if ($prevLineType != null && $l->getLineType() != $prevLineType) {
//                 $linesToDraw[] = new PriceLine('text');
            }
            
            $pl = new PriceLine($l->getLineType(), $l->getShortDescription(), $l->getAmount(), format_price($l->getPrice(), true, array('thousands' => '.')), $l->getShortDescription2());
            $pl->setVatAmount($this->formatPrice($l->getVatAmount()));
            $pl->setTotalPrice($this->formatPrice($l->getTotalPriceInclVat()));
            $linesToDraw[] = $pl;
            
            $prevLineType = $l->getLineType();
        }


        $linesToDraw[] = new PriceLine('text');
        
         
        $plTotaal = new PriceLine('price', 'Totaalbedrag', '');
        $plTotaal->setTotalPrice( $this->formatPrice($this->offer->getTotalAmountExclVat()) );
        $linesToDraw[] = $plTotaal;
        
        
        // TODO: loop vat
        $totalByVat = array();
        foreach($this->offer->getOfferLines() as $ol) {
            if (isset($totalByVat[$ol->getVat()]) == false) {
                $totalByVat[$ol->getVat()] = 0;
            }
            
            $totalByVat[$ol->getVat()] += $ol->getVatAmount();
        }
        
        foreach($totalByVat as $vatPercentage => $vatAmount) {
            if (intval($vatAmount*100) == 0) continue;
            
            $pl = new PriceLine('price', 'BTW ' . myround($vatPercentage, 2) . '%', '');
            $pl->setTotalPrice( $this->formatPrice($vatAmount) );
            $linesToDraw[] = $pl;
        }
        
        $pl = new PriceLine('price', 'Factuurbedrag', '');
        $pl->setTotalPrice($this->formatPrice($this->offer->getTotalAmountInclVat()));
        $linesToDraw[] = $pl;
        
        
        if ($this->offer->getDeposit() || $this->offer->getPaymentUpFront()) {
//             $linesToDraw[] = new PriceLine('text');
        }
        
        
        if ($this->offer->getDeposit()) {
            $linesToDraw[] = new PriceLine('price', 'Waarborgsom', '', format_price($this->offer->getDeposit(), true, array('thousands' => '.')));
        }
        
        if ($this->offer->getPaymentUpfront()) {
            $linesToDraw[] = new PriceLine('price', 'Totaal vooraf te betalen', '', format_price($this->offer->getPaymentUpfront(), true, array('thousands' => '.')));
        }
        
        
        for($x=0; $x < count($linesToDraw); $x++) {
            $l = $linesToDraw[$x];
            
            $nr = $x % count($rowColors);
            $this->SetFillColor($rowColors[$nr][0], $rowColors[$nr][1], $rowColors[$nr][2]);
            
            $border = 1;
            
            $aantal = '';
//             if (is_numeric($linesToDraw[$x][1]) && $linesToDraw[$x][1] != 0)
//                 $aantal = $linesToDraw[$x][1];

                if (trim($l->getDescription2())) {
                    $this->Cell(171.9/4,  $lh, $l->getDescription(), 'LTB', 0, 'L', true);
                    $this->Cell(171.9-(171.9/4),  $lh, $l->getDescription2(), 'RTB', 0, 'L', true);
                } else {
                    $this->Cell(171.9,  $lh, $l->getDescription(), $border, 0, 'L', true);
                }
            if ($l->getType() != 'text') {
                $this->Cell(15,     $lh, $this->formatNumber($l->getAmount()), $border, 0, 'R', true);
                $this->Cell(30-0.1, $lh, $l->getPrice(), $border, 0, 'R', true);
                $this->Cell(30-0.1, $lh, $l->getVatAmount(), $border, 0, 'R', true);
                $this->Cell(30-0.1, $lh, $l->getTotalPrice(), $border, 0, 'R', true);
            } else {
                $this->Cell(15,     $lh, '', $border, 0, 'R', true);
                $this->Cell(30-0.1, $lh, '', $border, 0, 'R', true);
                $this->Cell(30-0.1, $lh, '', $border, 0, 'R', true);
                $this->Cell(30-0.1, $lh, '', $border, 0, 'R', true);
            }
            
            
            $this->Ln();
        }
        
        
        $this->SetY($this->GetY() + 5);
    }
    
    
    public function renderNote() {
        
        if (trim($this->offer->getComment())) {
            
            $this->Multicell(276.8, 5, "Opmerkingen:\n".$this->offer->getComment(), 1);
            
        }
    }
    
    
    function Footer() {
        parent::Footer();
        
        $this->RotatedText(293.5, 205, 'Op onze diensten zijn onze algemene voorwaarden van toepassing. Deze zijn overhandigd, gelezen en akkoord.', 90);
        
    }
    
}



