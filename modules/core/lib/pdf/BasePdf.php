<?php

namespace core\pdf;

use core\Context;

class BasePdf extends \FPDF {

    protected $lineHeight = 6;
    protected $angle=0;
    
    
    function __construct($orientation='P', $unit='mm', $size='A4') {
        parent::__construct($orientation, $unit, $size);
        
        $this->SetAutoPageBreak(true, 30);
        $this->SetFont('Arial', '', '12');
        
        $this->SetFillColor(255, 255, 255);
    }
    
    // TODO: some helper functions..
    
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
        
        $iw = 60;
        $ih = $h / $w * $iw;
        
        $posy = 15;
        
        $this->Image($f, 210/2-($iw/2), $posy, $iw, $ih);
        
        $this->SetY($posy + $ih + 12.5);
    }
    
    
    function Footer() {
//         $this->SetX(0);
//         $this->SetY(285);
//         $this->Cell(0, 3.5, 'Pagina ' . $this->PageNo() . ' van {nb}', 0, 0, 'R');
        
//         $this->SetX(10);
//         $this->Cell(0, 3.5, date('d-m-Y'), 0, 0, 'L');

        $date = date('d-m-Y');
//         $date = '';
        
        if ($this->DefOrientation == 'P') {
            $this->SetX(0);
            $this->SetY(280);
            $this->Cell(100, $this->lineHeight, $date, 0, 0, 'L');
            $this->Cell(95, $this->lineHeight, 'Pagina ' . $this->PageNo() . ' van {nb}', 0, 0, 'R');
        }
        
        if ($this->DefOrientation == 'L') {
            $this->SetX(2);
            $this->SetY(200);
            $this->Cell(100, $this->lineHeight, $date, 0, 0, 'L');
            $this->Cell(180, $this->lineHeight, 'Pagina ' . $this->PageNo() . ' van {nb}', 0, 0, 'R');
        }
    }
    
    
    function formatPrice($p) {
        return format_price($p, true, array('thousands' => '.'));
    }

    function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false) {
        $txt = str_replace('–', '-', $txt);
        $txt = iconv('UTF-8', 'CP1252', $txt);
        
        return parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }
    
    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        $txt = iconv('UTF-8', 'CP1252', $txt);
        
        return parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }
    
    function Text($x, $y, $txt) {
        $txt = iconv('UTF-8', 'CP1252', $txt);
        
        parent::Text($x, $y, $txt);
    }
    
    function RotatedText($x,$y,$txt,$angle) {
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate( 0 );
    }
    
    
    function Rotate($angle, $x=-1, $y=-1) {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle != 0)
            $this->_out('Q');
        
        $this->angle = $angle;
        
        if( $angle != 0) {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }
    
    
}

