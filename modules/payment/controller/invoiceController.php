<?php


use core\controller\BaseController;
use core\exception\InvalidStateException;
use invoice\model\Invoice;
use invoice\service\InvoiceService;
use payment\form\PaymentForm;
use payment\model\Payment;
use payment\model\PaymentLine;
use payment\service\PaymentService;

class invoiceController extends BaseController {
    
    
    
    public function action_create_payment() {
        
        $is = object_container_get(InvoiceService::class);
        $invoice = $is->readInvoice( get_var('invoice_id') );
        
        if (!$invoice) {
            throw new InvalidStateException('Invoice not found');
        }
        
        
        $ps = object_container_get(PaymentService::class);
        $paymentMethod = $ps->readDefaultSelectedPaymentMethod();
        
        $p = new Payment();
        $p->setCompanyId( $invoice->getCompanyId() );
        $p->setPersonId( $invoice->getPersonId() );
        $p->setDescription('Betaling factuur ' . $invoice->getInvoiceNumberText());
        $p->setPaymentDate(date('Y-m-d'));
//         $p->setAmount( $invoice->getTotalAmountInclVat() );      // set by PaymentService

        $pl = new PaymentLine();
        if ($paymentMethod) {
            $pl->setPaymentMethodId( $paymentMethod->getPaymentMethodId() );
        }
        $pl->setAmount( $invoice->getTotalAmountInclVat() );
        $p->setPaymentLines(array( $pl ));
        
        $form = new PaymentForm();
        $form->bind($p);
        
        $payment_id = $ps->savePayment($form);
        
        
        object_meta_save(Invoice::class, $invoice->getInvoiceId(), 'payment_created', true);
        
        report_user_message('Betaling aangemaakt');
        
        redirect('/?m=payment&c=payment&id='.$payment_id);
    }
    
    
}
