<?php


namespace invoice\form;

use core\ObjectContainer;
use core\forms\DoubleField;
use core\forms\EuroField;
use core\forms\HiddenField;
use core\forms\ListEditWidget;
use core\forms\SelectField;
use core\forms\TextField;
use invoice\model\Order;
use invoice\model\OrderLine;
use invoice\service\InvoiceService;

class ListOrderLineWidget extends ListEditWidget {
    
    public function __construct($methodObjectList=null) {
        parent::__construct($methodObjectList);
        
        $this->init();
        
        $this->setMobileListHeader( "Order regels" );
        $this->setMobileTemplate('
                <div class="price-sum">{{ record.price_sum_text }}</div>
                <div class="title" ez-if="record.line_type == \'text\'">{{ record.short_description }} - {{ record.short_description2 }}</div>
                <div class="" ez-if="record.line_type != \'text\'">
                    <div class="title">{{ record.short_description }}</div>
                    <div class="amount">
                        {{ record.amount }}x {{ format_price(record.price, true) }}
                        - {{ record.vat_text }}
                    </div>
                </div>');
    }
    
    protected function init() {
        $this->addWidget( new HiddenField('order_line_id', '', 'Id') );
        $this->addWidget( new HiddenField('line_type', 'price', 'type') );
        $this->addWidget( new HiddenField('article_id', '', 'Artikel id') );
        $this->addWidget( new TextField('short_description', '', 'Omschrijving' ));
        $this->getWidget('short_description')->showPlaceholder();
        $this->addWidget( new TextField('short_description2', '', 'Omschrijving 2' ));
        $this->getWidget('short_description2')->showPlaceholder();
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
        
        
        $this->addWidget( new SelectField('vat', $defaultSelected, $options, 'Btw', array('add-unlisted' => true)));
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
        $html = '<tr class="line-type-'.$obj['line_type'].'">';
        
        if ($this->sortable) {
            $html .= '<td class="td-sortable"><span class="fa fa-sort handler-sortable"></span></td>';
        }
        
        $skipFields = array();
        
        // bind values
        foreach($this->widgets as $w) {
            if ($w->bindObject( $obj ) == 0) {
                $skipFields[] = $w->getName();
            }
        }
        
        if ($obj['line_type'] == 'text') {
            
            $html .= '<td colspan="6">' . $obj['short_description'] . ': ' . $obj['short_description2'] . '</td>';
            
        } else {
            // render record
            for($x=0; $x < count($this->widgets); $x++) {
                $w = $this->widgets[$x];
                if (is_a($w, HiddenField::class)) continue;
                
                $html .= '<td class="input-'.slugify($w->getName()).'">';
                
                if (in_array($w->getName(), $skipFields)) {
                } else {
                    $html .= $w->renderAsText();
                }
                
                $html .= '</td>';
            }
        }
        
        $ol = new OrderLine();
        $ol->setFields($obj);
        
        $html .= '<td class="input-price">'.format_price($ol->getTotalPriceInclVat(), true, ['thousands' => '.']).'</td>';
        
        $html .= '</tr>';
        
        return $html;
    }
    
    public function renderFooterRow() {
        
        $ols = array();
        foreach($this->getObjects() as $o) {
            $ol = new OrderLine();
            $ol->setFields($o);
            $ols[] = $ol;
        }
        $o = new Order();
        $o->setOrderLines( $ols );
        
        $html = '';
        
        $html .= '<tr><td colspan="6">Totaal excl. btw '.format_price($o->getTotalAmountExclVat(), true, ['thousands' => '.']).'</td></tr>';
        
        $vatPricePercentage = $o->getTotalVatByPercentage();
        foreach($vatPricePercentage as $vat => $price) {
            $html .= '<tr>';
            $html .= '<td colspan="6">Btw '.$vat.'% ' . format_price($price, true, ['thousands' => '.']) . '</td>';
            $html .= '<tr>';
        }
        
        $html .= '<tr><td colspan="6">Totaal '.format_price($o->getTotalAmountInclVat(), true, ['thousands' => '.']).'</td></tr>';
        
        return $html;
    }
    
    
}