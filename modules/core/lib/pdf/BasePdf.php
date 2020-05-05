<?php

namespace core\pdf;

use core\Context;

class BasePdf extends \FPDF {

    protected $lineHeight = 6;
    protected $angle=0;
    
    protected $isMultiPage = null;
    
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

        if (ctx()->pdfPrintDateFooter())
            $date = date('d-m-Y');
        else
            $date = '';

        $printPaging = true;
        if (ctx()->pdfPrintPaging() == 'never') {
            $printPaging = false;
        }
        else if (ctx()->pdfPrintPaging() == 'multi-page' && $this->isMultiPage == false) {
            $printPaging = false;
        }
        
        if ($this->DefOrientation == 'P') {
            $this->SetX(0);
            $this->SetY(280);
            $this->Cell(100, $this->lineHeight, $date, 0, 0, 'L');
            
            if ($printPaging)
                $this->Cell(95, $this->lineHeight, 'Pagina ' . $this->PageNo() . ' van {nb}', 0, 0, 'R');
        }
        
        if ($this->DefOrientation == 'L') {
            $this->SetX(2);
            $this->SetY(200);
            $this->Cell(100, $this->lineHeight, $date, 0, 0, 'L');
            
            if ($printPaging)
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
    
    function ImagickJpeg($filename, $imagick, $x=null, $y=null, $w=0, $h=0, $link='') {
        if(!isset($this->images[$filename]))
        {
            $info = array('w'=>$imagick->getimagewidth(), 'h'=>$imagick->getimageheight(), 'cs'=>'DeviceRGB', 'bpc'=>8, 'f'=>'DCTDecode', 'data'=>(string)$imagick);
            
            $info['i'] = count($this->images)+1;
            $this->images[$filename] = $info;
        }
        else
            $info = $this->images[$filename];
        
        // Automatic width and height calculation if needed
        if($w==0 && $h==0)
        {
            // Put image at 96 dpi
            $w = -96;
            $h = -96;
        }
        if($w<0)
            $w = -$info['w']*72/$w/$this->k;
        if($h<0)
            $h = -$info['h']*72/$h/$this->k;
        if($w==0)
            $w = $h*$info['w']/$info['h'];
        if($h==0)
            $h = $w*$info['h']/$info['w'];
            
        // Flowing mode
        if($y===null)
        {
            if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
            {
                // Automatic page break
                $x2 = $this->x;
                $this->AddPage($this->CurOrientation,$this->CurPageSize,$this->CurRotation);
                $this->x = $x2;
            }
            $y = $this->y;
            $this->y += $h;
        }
        
        if($x===null) {
            $x = $this->x;
        }
        
        $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
        
        if($link) {
            $this->Link($x,$y,$w,$h,$link);
        }
    }
    
    
}

