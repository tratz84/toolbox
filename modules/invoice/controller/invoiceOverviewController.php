<?php


use core\controller\BaseController;
use invoice\InvoiceSettings;
use invoice\service\InvoiceService;

class invoiceOverviewController extends BaseController {



    public function action_index() {

        if (isset($this->companyId) == false)
            $this->companyId = 0;
        if (isset($this->personId) == false)
            $this->personId = 0;

        $invoiceService = $this->oc->get(InvoiceService::class);

        if ($this->companyId) {
            $this->listResponse = $invoiceService->searchInvoice(0, 100, array('company_id' => $this->companyId));
        }
        
        if ($this->personId) {
            $this->listResponse = $invoiceService->searchInvoice(0, 100, array('person_id' => $this->personId));
        }

        $this->invoiceSettings = $this->oc->get(InvoiceSettings::class);

        if (isset($this->listResponse)) {
            $this->setShowDecorator(false);
            $this->render();
        }
    }

}
