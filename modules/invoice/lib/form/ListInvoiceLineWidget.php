<?php


namespace invoice\form;

use core\ObjectContainer;
use core\forms\DoubleField;
use core\forms\EuroField;
use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\SelectField;
use core\forms\TextField;
use invoice\model\Invoice;
use invoice\model\InvoiceLine;
use invoice\service\InvoiceService;

class ListInvoiceLineWidget extends ListEditWidget {
    
    public function __construct($methodObjectList=null) {
        parent::__construct($methodObjectList);
        
        $this->init();
    }
    
    protected function init() {
        $this->addWidget( new HiddenField('invoice_line_id', '', 'Id') );
        $this->addWidget( new HiddenField('meta', '', 'meta') );
        $this->addWidget( new HiddenField('line_type', '', 'type') );
        $this->addWidget( new HiddenField('article_id', '', 'Artikel id') );
        $this->addWidget( new TextField('short_description', '', 'Omschrijving' ));
        $this->addWidget( new DoubleField('amount', '1', 'Aantal') );
        $this->addWidget( new EuroField('price', '', 'Bedrag') );
        $this->addVatPercentages();
    }
    
    
    protected function addVatPercentages() {
        $invoiceService = ObjectContainer::getInstance()->get(InvoiceService::class);
        $vat = $invoiceService->readActiveVatTarifs();
        
        
        $defaultSelected = '';
        if (count($vat)) {
            $defaultSelected = $vat[0]->getPercentage();
        }
        $options = array();
        
        foreach($vat as $v) {
            if ($v->getDefaultSelected()) {
                $defaultSelected = $v->getPercentage();
            }
            
            $options[$v->getPercentage()] = $v->getDescription();
        }
        
        
        $this->addWidget( new SelectField('vat_percentage', $defaultSelected, $options, 'Btw', array('add-unlisted' => true)));
    }
    
    public function renderHeader($method='default') {
        if ($method == 'text') {
            $html = '';
            
            $html .= '<thead>';
            if ($this->sortable) {
                $html .= '<th></th>';
            }
            foreach($this->widgets as $w) {
                if (is_a($w, HiddenField::class)) continue;
                $html .= '<th class="th-'.slugify($w->getName()).'">'.esc_html($w->getLabel()).'</th>';
            }
            $html .= '<th class="th-price">Totaal</th>';
            $html .= '</thead>';
            
            return $html;
        } else {
            return parent::renderHeader($method);
        }
    }

    public function renderRowAsText($obj=array()) {
        $html = '<tr>';
        
        if ($this->sortable) {
            $html .= '<td class="td-sortable"><span class="fa fa-sort handler-sortable"></span></td>';
        }
        
        // bind values
        foreach($this->widgets as $w) {
            $w->bindObject( $obj );
        }
        
        
        if ($obj['line_type'] == 'text') {
            $html .= '<td>'.esc_html($obj['description']).': ' . esc_html($obj['description2']) . '</td>';
        } else {
            // render record
            for($x=0; $x < count($this->widgets); $x++) {
                $w = $this->widgets[$x];
                if (is_a($w, HiddenField::class)) continue;
                
                $html .= '<td class="input-'.slugify($w->getName()).'">';
                
                $html .= $w->renderAsText();
                
                $html .= '</td>';
            }
        
            $il = new InvoiceLine();
            $il->setFields($obj);
            
            $html .= '<td class="input-price">'.format_price($il->getTotalPriceInclVat(), true, ['thousands' => '.']).'</td>';
        }
        
        $html .= '</tr>';
        
        return $html;
    }
    
    public function renderFooterRow() {
        
        $ils = array();
        foreach($this->getObjects() as $o) {
            $il = new InvoiceLine();
            $il->setFields($o);
            $il->calculateVatAmount();
            $ils[] = $il;
        }
        $i = new Invoice();
        $i->setInvoiceLines( $ils );
        
        $html = '';
        
        $html .= '<tr><td colspan="6">Totaal excl. btw '.format_price($i->getTotalAmountExclVat(), true, ['thousands' => '.']).'</td></tr>';
        
        $vatPricePercentage = $i->getTotalVatByPercentage();
        foreach($vatPricePercentage as $vat => $price) {
            $html .= '<tr>';
            $html .= '<td colspan="6">Btw '.$vat.'% ' . format_price($price, true, ['thousands' => '.']) . '</td>';
            $html .= '<tr>';
        }
        
        $html .= '<tr><td colspan="6">Totaal '.format_price($i->getTotalAmountInclVat(), true, ['thousands' => '.']).'</td></tr>';
        
        return $html;
    }
    
    
}