<?php


namespace invoice\service;

use base\forms\FormChangesHtml;
use customer\service\CustomerService;
use base\util\ActivityUtil;
use core\ObjectContainer;
use core\container\ObjectHookable;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use function format_date;
use function next_month;
use invoice\InvoiceSettings;
use invoice\form\InvoiceForm;
use invoice\form\PriceAdjustmentForm;
use invoice\form\ToBillForm;
use invoice\form\VatForm;
use invoice\model\CompanySetting;
use invoice\model\CompanySettingDAO;
use invoice\model\Invoice;
use invoice\model\InvoiceDAO;
use invoice\model\InvoiceLine;
use invoice\model\InvoiceLineDAO;
use invoice\model\InvoiceStatus;
use invoice\model\InvoiceStatusDAO;
use invoice\model\PriceAdjustment;
use invoice\model\PriceAdjustmentDAO;
use invoice\model\ToBill;
use invoice\model\ToBillDAO;
use invoice\model\Vat;
use invoice\model\VatDAO;
use project\model\ProjectDAO;
use core\exception\LockException;

class InvoiceService extends ServiceBase implements ObjectHookable {


    protected $companyVatExcemptions = array();


    public function readAllVatTarifs() {
        $vDao = new VatDAO();

        return $vDao->readAll();
    }

    public function readActiveVatTarifs() {
        $vDao = new VatDAO();

        return $vDao->readActive();
    }

    public function readDefaultVat() {
        $vDao = new VatDAO();
        
        return $vDao->readDefault();
    }
    
    public function readVat($vatId) {
        $vDao = new VatDAO();

        return $vDao->read($vatId);
    }

    public function saveVat(VatForm $form) {
        $id = $form->getWidgetValue('vat_id');
        if ($id) {
            $vat = $this->readVat($id);
        } else {
            $vat = new Vat();
        }

        $form->fill($vat, array('vat_id', 'description', 'percentage', 'default_selected', 'visible'));

        if (!$vat->save()) {
            return false;
        }

        if ($vat->getDefaultSelected()) {
            $oDao = new VatDAO();
            $oDao->unsetDefaultSelected($vat->getVatId());
        }
        
        return $vat;
    }


    public function updateVatSort($ids) {
        $vDao = new VatDAO();
        $vDao->updateSort($ids);
    }

    public function deleteVat($id) {
        $vDao = new VatDAO();
        $vDao->delete($id);
    }





    public function readAllInvoiceStatus() {
        $iDao = new InvoiceStatusDAO();
        return $iDao->readAll();
    }

    public function readActiveInvoiceStatus() {
        $iDao = new InvoiceStatusDAO();
        return $iDao->readActive();
    }

    public function readInvoiceStatus($id) {
        $isDao = new InvoiceStatusDAO();
        return $isDao->read($id);
    }

    public function saveInvoiceStatus($form) {
        $id = $form->getWidgetValue('invoice_status_id');
        if ($id) {
            $invoiceStatus = $this->readInvoiceStatus($id);
        } else {
            $invoiceStatus = new InvoiceStatus();
        }

        $form->fill($invoiceStatus, array('invoice_status_id', 'description', 'default_selected', 'active'));

        if (!$invoiceStatus->save()) {
            return false;
        }

        if ($invoiceStatus->getDefaultSelected()) {
            $isDao = new InvoiceStatusDAO();
            $isDao->unsetDefaultSelected($invoiceStatus->getInvoiceStatusId());
        }
    }

    public function readDefaultInvoiceStatus() {
        $isDao = new InvoiceStatusDAO();

        $is = $isDao->readByDefaultStatus();
        if ($is)
            return $is;

        $is = $isDao->readFirst();
        return $is;
    }


    public function deleteInvoiceStatus($id) {
        // set invoice status to null of currently used cases
        $iDao = new InvoiceDAO();
        $iDao->invoiceStatusToNull($id);

        $isDao = new InvoiceStatusDAO();
        $isDao->delete($id);
    }

    public function updateInvoiceStatusSort($invoiceStatusIds) {
        $isDao = new InvoiceStatusDAO();
        $isDao->updateSort($invoiceStatusIds);
    }


    public function searchInvoice($start, $limit, $opts = array()) {
        $iDao = new InvoiceDAO();

        $lastInvoiceNumber = $iDao->getLastInvoiceNumber();

        $cursor = $iDao->search($opts);
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('invoice_id', 'invoice_status_id', 'credit_invoice', 'company_id', 'person_id', 'subject', 'comment', 'accepted', 'total_calculated_price', 'total_calculated_price_incl_vat', 'invoice_date', 'edited', 'created', 'firstname', 'insert_lastname', 'lastname', 'company_name', 'invoice_status_description', 'invoiceNumberText', 'invoice_number'));

        // orderType == 'invoice'? => only last item deletable
        $invoiceSettings = $this->oc->get(InvoiceSettings::class);
        $orderType = $invoiceSettings->getOrderType();

        $objects = $r->getObjects();
        foreach($objects as &$obj) {
            if ($orderType == 'invoice') {
                $obj['deletable'] = $obj['invoice_number'] == $lastInvoiceNumber ? true : false;;
            } else {
                $obj['deletable'] = true;
            }
        }
        $r->setObjects($objects);

        return $r;

    }

    public function readInvoice($id) {
        $iDao = new InvoiceDAO();

        $invoice = $iDao->read($id);

        if (!$invoice)
            return null;

        $ilDao = new InvoiceLineDAO();
        $lines = $ilDao->readByInvoice($id);
        $invoice->setInvoiceLines( $lines );

        $customerService = ObjectContainer::getInstance()->get(CustomerService::class);
        $customer = $customerService->readCustomerAuto($invoice->getCompanyId(), $invoice->getPersonId());
        $invoice->setCustomer($customer);         // might be null

        return $invoice;
    }
    
    
    
    public function readInvoiceByNumber($invoiceNumber, $fullObject=true) {
        $iDao = new InvoiceDAO();
        
        $invoice = $iDao->readByInvoiceNumber($invoiceNumber);
        if ($invoice && $fullObject) {
            return $this->readInvoice( $invoice->getInvoiceId() );
        }
        
        return $invoice;
    }
    
    
    
    /**
     * lookupCreditInvoiceId() - looks up the credit invoice by given invoice
     */
    public function lookupCreditInvoiceId($invoiceId) {
        $invoiceDao = new InvoiceDAO();
        $cursor = $invoiceDao->search(['ref_invoice_id' => $invoiceId]);
        
        if ($cursor->hasNext()) {
            $i = $cursor->next();
            return $i->getInvoiceId();
        } else {
            return null;
        }
    }

    public function deleteInvoice($id) {
        $iDao = new InvoiceDAO();
        $invoice = $this->readInvoice($id);

        $invoiceSettings = $this->oc->get(InvoiceSettings::class);
        if ($invoiceSettings->getOrderType() == 'invoice') {
            $lastInvoiceNumber = $iDao->getLastInvoiceNumber();

            if ($lastInvoiceNumber != $invoice->getInvoiceNumber()) {
                throw new \core\exception\InvalidStateException('Unable to delete invoice');
            }
            
            /** @var InvoiceSettings $invoiceSettings */
            $invoiceSettings = object_container_get(InvoiceSettings::class);
            // locked? => throw LockException
            if ($invoiceSettings->invoiceLocked( $invoice )) {
                throw new LockException('Invoice locked');
            }
        }

        $ilDao = new InvoiceLineDAO();
        $ilDao->deleteByInvoice($id);

        $iDao->delete($id);

        $f = new InvoiceForm();
        $changes = $f->changes($invoice);

        ActivityUtil::logActivity($invoice->getCompanyId(), $invoice->getPersonId(), 'invoice__invoice', $invoice->getInvoiceId(), 'invoice-deleted', strOrder(1).' verwijderd '.$invoice->getInvoiceNumberText(), null, $changes);
    }


    public function saveInvoice($form) {
        $id = $form->getWidgetValue('invoice_id');
        if ($id) {
            $invoice = $this->readInvoice($id);
        } else {
            $invoice = new Invoice();
        }

        $iDao = new InvoiceDAO();

        $isNew = $invoice->isNew();
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew($form);
        } else {
            $oldForm = new InvoiceForm();
            $oldForm->bind($invoice);
            
            $fch = FormChangesHtml::formChanged($oldForm, $form);
        }

        $invoiceSettings = object_container_get(InvoiceSettings::class);
        if ($isNew == false && $invoiceSettings->invoiceLocked( $invoice )) {
            throw new InvalidStateException('Object auto-locked');
        }

        // no changes? => skip (saveInvoice is called when printing or sending email)
        if ($fch->hasChanges() == false) {
            return $invoice;
        }

        $form->fill($invoice, array('invoice_id', 'ref_invoice_id', 'invoice_status_id', 'credit_invoice', 'invoice_date', 'deposit', 'payment_upfront', 'subject', 'comment', 'note'));

        if ($invoice->getInvoiceStatusId() == 0)
            $invoice->setInvoiceStatusId(null);

        if ($isNew) {
            $invoice->setInvoiceNumber( $iDao->generateInvoiceNumber() );
        }

        //
        $totalCalculatedAmount = 0;
        $totalCalculatedAmountInclVat = 0;
        $newInvoiceLines = $form->getWidget('invoiceLines')->getObjects();
        for($x=0; $x < count($newInvoiceLines); $x++) {
            if (isset($newInvoiceLines[$x]['price'])) {
                $price = strtodouble( $newInvoiceLines[$x]['price'] );
                $vatAmount = myround( $price * strtodouble($newInvoiceLines[$x]['amount']) * $newInvoiceLines[$x]['vat_percentage'] / 100, 2 );
                
                $totalCalculatedAmount += myround( $price * $newInvoiceLines[$x]['amount'], 2 );
                $totalCalculatedAmountInclVat += myround( $price * $newInvoiceLines[$x]['amount'], 2 ) + $vatAmount;
                
                $newInvoiceLines[$x]['price'] = $price;
                $newInvoiceLines[$x]['vat_amount'] = $vatAmount;
            }
        }

        $invoice->setTotalCalculatedPrice( myround($totalCalculatedAmount, 2) );
        $invoice->setTotalCalculatedPriceInclVat( $totalCalculatedAmountInclVat );

        if (!$invoice->save()) {
            return false;
        }

        $form->getWidget('invoice_id')->setValue($invoice->getInvoiceId());


        $ilDao = new InvoiceLineDAO();
        $ilDao->mergeFormListMTO1('invoice_id', $invoice->getInvoiceId(), $newInvoiceLines);

        // .. ? $newInvoiceLines contains an array, invoice\model\InvoiceLine-objects expected..
        // maybe reload invoice-object? => $invoice = $this->readInvoice($invoice->getInvoiceId());
        $newIls = array();
        foreach($newInvoiceLines as $nil) {
            if (is_a($nil, InvoiceLine::class)) {
                $newIls[] = $nil;
            } else {
                $il = new InvoiceLine();
                $il->setFields($nil);
                $newIls[] = $il;
            }
        }
        $invoice->setInvoiceLines($newIls);

        if ($isNew) {
            ActivityUtil::logActivity($invoice->getCompanyId(), $invoice->getPersonId(), 'invoice__invoice', $invoice->getInvoiceId(), 'invoice-created', 'Factuur aangemaakt '.$invoice->getInvoiceNumberText(), $fch->getHtml());
        } else {
            ActivityUtil::logActivity($invoice->getCompanyId(), $invoice->getPersonId(), 'invoice__invoice', $invoice->getInvoiceId(), 'invoice-edited', 'Factuur aangepast '.$invoice->getInvoiceNumberText(), $fch->getHtml());
        }

        return $invoice;
    }


    public function readInvoiceTotals($opts) {
        $iDao = new InvoiceDAO();

        return $iDao->readTotals($opts);
    }



    public function updateInvoiceStatus($invoiceId, $invoiceStatusId) {
        $isNew = $this->readInvoiceStatus($invoiceStatusId);
        if ($isNew == null)
            throw new ObjectNotFoundException('InvoiceStatus not found');

        $invoice = $this->readInvoice($invoiceId);
        if ($invoice == null)
            throw new ObjectNotFoundException('Invoice not found');

        $isOld = $this->readInvoiceStatus($invoice->getInvoiceStatusId());


        $iDao = new InvoiceDAO();
        $iDao->updateStatus($invoiceId, $invoiceStatusId);

        $html = FormChangesHtml::tableFromArray([
            ['label' => 'Status', 'old' => $isOld?$isOld->getDescription():'', 'new' => $isNew->getDescription()]
        ]);
        

        ActivityUtil::logActivity($invoice->getCompanyId(), $invoice->getPersonId(), 'invoice__invoice', $invoice->getInvoiceId(), 'invoice-update-status', 'Factuur status '.$invoice->getInvoiceNumberText() . ': ' . $isNew->getDescription(), $html);
    }

    public function validateInvoiceDate($invoiceId, $invoiceDate) {
        //
        if (valid_date($invoiceDate) == false) {
            return false;
        }

        $ymdInvoiceDate = (int)format_date($invoiceDate, 'Ymd');

        $iDao = new InvoiceDAO();

        // check if invoice-date is not after next invoice & not before previous invoice
        if ($invoiceId) {
            // fetch next & prev invoice
            $invoice = $iDao->read($invoiceId);
            if (!$invoice) {
                throw new \core\exception\ObjectNotFoundException('Invoice not found');
            }

            $prevInvoiceNumber = $invoice->getInvoiceNumber() - 1;
            $nextInvoiceNumber = $invoice->getInvoiceNumber() + 1;

            $prevInvoice = $iDao->readByInvoiceNumber($prevInvoiceNumber);
            if ($prevInvoice) {
                $ymd = (int)format_date($prevInvoice->getInvoiceDate(), 'Ymd');
                if ($ymd > $ymdInvoiceDate) {
                    return false;
                }
            }

            $nextInvoice = $iDao->readByInvoiceNumber($nextInvoiceNumber);
            if ($nextInvoice) {
                $ymd = (int)format_date($nextInvoice->getInvoiceDate(), 'Ymd');
                if ($ymd < $ymdInvoiceDate) {
                    return false;
                }
            }
        }
        // new invoice? => check if invoice-date is not before last invoice
        else {
            $lid = $iDao->getLastInvoiceDate();
            $d2 = (int)format_date($lid, 'Ymd');

            if ($ymdInvoiceDate < $d2) {
                return false;
            }
        }

        return true;
    }
    
    
    public function getInvoiceNumberLengths() {
        $iDao = new InvoiceDAO();
        
        return $iDao->getInvoiceNumberLengths();
    }
    


    public function deletePriceAdjustment($priceAdjustmentId) {
        $paDao = new PriceAdjustmentDAO();
        
        $pa = $paDao->read($priceAdjustmentId);
        
        // log changes
        $paForm = new PriceAdjustmentForm();
        $paForm->bind($pa);
        $fch = FormChangesHtml::formDeleted($paForm);

        $pa->delete();
        
        ActivityUtil::logActivity($pa->getCompanyId(), $pa->getPersonId(), $pa->getRefObject(), $pa->getRefId(), 'price-adjustment', 'Prijswijziging verwijderd '.$pa->getStartDateFormat('d-m-Y'), $fch->getHtml());
    }

    public function readPriceAdjustments($refObject, $refId, $peildatum=null) {
        $paDao = new PriceAdjustmentDAO();

        $objs = $paDao->readByRef($refObject, $refId);
        
        $peildatum = $peildatum ? (int)format_date($peildatum, 'Ymd') : (int)date('Ymd');

        // set 'active-period' field to 'true'
        for($x=0; $x < count($objs); $x++) {
            $pa = $objs[$x];
            if ($pa->getStartDateFormat('Ymd') <= $peildatum) {
                if ($x+1 == count($objs) || $objs[$x+1]->getStartDateFormat('Ymd') > $peildatum) {
                    $pa->setField('active-period', true);
                }
            }
            
        }
        
        
        return $objs;
    }

    public function searchPriceAdjustments($opts) {
        $paDao = new PriceAdjustmentDAO();

        return $paDao->search($opts);
    }

    /**
     * 
     * @param unknown $companyId
     * @param unknown $personId
     * @param unknown $refObject
     * @param unknown $refId
     * @param unknown $price            - price
     * @param unknown $discountPrice
     * @param unknown $startDate
     * @return \invoice\model\PriceAdjustment|\core\db\unknown|NULL
     */
    public function savePriceAdjustment($companyId, $personId, $refObject, $refId, $price, $discountPrice, $startDate) {
        $paDao = new PriceAdjustmentDAO();
        $pa = $paDao->readByStart($refObject, $refId, $startDate);
        
        $isNew = $pa ? false : true;
        
        $oldForm = null;
        if (!$isNew) {
            $oldForm = PriceAdjustmentForm::createAndBind( $pa );
        }
        
        if (!$pa) {
            $pa = new PriceAdjustment();
            $pa->setCompanyId($companyId);
            $pa->setPersonId($personId);
            $pa->setRefObject($refObject);
            $pa->setRefId($refId);
            $pa->setStartDate($startDate);
        }
        
        $pa->setNewPrice($price);
        $pa->setNewDiscount($discountPrice);
        $pa->save();
        
        $form = PriceAdjustmentForm::createAndBind($pa);
        if ($isNew) {
            $fch = FormChangesHtml::formNew($form);
        } else {
            $fch = FormChangesHtml::formChanged( $oldForm, $form );
        }
        
        if ($isNew) {
            ActivityUtil::logActivity($companyId, $personId, $refObject, $refId, 'price-adjustment', 'Prijswijziging ingepland voor '.format_date($startDate, 'd-m-Y') . ', ' . format_price($price, true, ['thousands' => '.']), $fch->getHtml());
        } else {
            ActivityUtil::logActivity($companyId, $personId, $refObject, $refId, 'price-adjustment', 'Prijswijziging gewijzigd voor '.format_date($startDate, 'd-m-Y') . ', ' . format_price($price, true, ['thousands' => '.']), $fch->getHtml());
        }
        

        return $pa;
    }



    public function createPdf($invoiceId) {
        $invoice = $this->readInvoice($invoiceId);

        $invoiceSettings = $this->oc->get(InvoiceSettings::class);

        $invoicePdf = @$this->oc->create( $invoiceSettings->getInvoicePdfClass() );

        $invoicePdf->setInvoice($invoice);
        $invoicePdf->render();

        return $invoicePdf;
    }

    public function readCompanySettings($companyId) {
        $csDao = new CompanySettingDAO();
        return $csDao->readByCompany($companyId);
    }

    public function saveCompanySettings(CompanySetting $cs) {
        $cs->save();
    }

    public function hasCompanyTaxExcemption($companyId) {
        if (array_key_exists($companyId, $this->companyVatExcemptions) == false) {
            $csDao = new CompanySettingDAO();
            $this->companyVatExcemptions[$companyId] = $csDao->hasTaxExcemption($companyId);
        }

        return $this->companyVatExcemptions[$companyId];
    }




    public function searchBillable($start, $limit, $opts = array()) {
        $tbDao = new ToBillDAO();


        $cursor = $tbDao->search($opts);
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('to_bill_id', 'type', 'company_id', 'company_name', 'firstname', 'insert_lastname', 'lastname', 'user_id', 'short_description', 'amount', 'price', 'paid', 'edited', 'created'));

        return $r;
    }

    public function readToBill($toBillId) {
        $tbDao = new ToBillDAO();

        return $tbDao->read($toBillId);
    }

    public function saveToBill(ToBillForm $form) {
        $id = $form->getWidgetValue('to_bill_id');
        if ($id) {
            $tobill = $this->readToBill($id);
        } else {
            $tobill = new ToBill();
        }

        $isNew = $tobill->isNew();

        $changes = $form->changes($tobill);

        $form->fill($tobill, array('to_bill_id', 'customer_id', 'type', 'firstname', 'insert_lastname', 'lastname', 'short_description', 'long_description', 'paid', 'amount', 'price'));

        if (!$tobill->save()) {
            return false;
        }

        if ($isNew) {
            ActivityUtil::logActivity($tobill->getCompanyId(), $tobill->getPersonId(), 'to_bill', $tobill->getToBillId(), 'to-bill-created', 'Billable aangemaakt', null, $changes);
        } else {
            ActivityUtil::logActivity($tobill->getCompanyId(), $tobill->getPersonId(), 'to_bill', $tobill->getToBillId(), 'to-bill-changed', 'Billable aangepast', null, $changes);
        }
    }

    public function deleteToBill($toBillId) {
        $tobill = $this->readToBill($toBillId);

        if ($tobill == null) {
            throw new ObjectNotFoundException('Billable not found');
        }

        $form = new ToBillForm();
        $changes = $form->changes($tobill);

        $tbDao = new ToBillDAO();
        $tbDao->delete($toBillId);

        ActivityUtil::logActivity($tobill->getCompanyId(), $tobill->getPersonId(), 'to_bill', $tobill->getToBillId(), 'to-bill-deleted', 'Billable verwijderd', null, $changes);
    }
    
    
    public function totalsPerMonth($startPeriod, $endPeriod) {
        if (preg_match('/^\\d{4}-\\d{2}$/', $startPeriod) == false) {
            throw new InvalidStateException('Startperiod not valid');
        }
        if (preg_match('/^\\d{4}-\\d{2}$/', $endPeriod) == false) {
            throw new InvalidStateException('Endperiod not valid');
        }
        
        $iDao = new InvoiceDAO();
        
        $totals = $iDao->totalsPerMonth($startPeriod, $endPeriod);
        
        $list = array();
        $start = format_date($startPeriod.'-15', 'Y-m-15');
        $end = format_date($endPeriod.'-15', 'Y-m-15');
        
        $ymStart = (int)format_date($start, 'Ym');
        $ymEnd = (int)format_date($end, 'Ym');
        while($ymStart <= $ymEnd) {
            $month = format_date($start, 'Y-m');
            $sum_excl_vat=0;
            $sum_incl_vat = 0;
            
            foreach($totals as $t) {
                if ($t['month'] == $month) {
                    $sum_excl_vat = $t['sum_excl_vat'];
                    $sum_incl_vat = $t['sum_incl_vat'];
                    break;
                }
            }
            
            $list[] = array(
                'month' => $month,
                'amount' => $sum_excl_vat,
                'sum_excl_vat' => $sum_excl_vat,
                'sum_incl_vat' => $sum_incl_vat
            );
            
            $start = next_month($start);
            $ymStart = (int)format_date($start, 'Ym');
        }
        
        return $list;
    }


}
