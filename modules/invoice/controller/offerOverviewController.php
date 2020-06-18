<?php


use customer\forms\CompanyForm;
use customer\forms\PersonForm;
use core\controller\BaseController;
use invoice\InvoiceSettings;
use invoice\service\OfferService;

class offerOverviewController extends BaseController {



    public function action_index() {

        if (isset($this->form) == false) {
            die('form not set');
        }

        $offerService = $this->oc->get(OfferService::class);

        if (is_a($this->form, CompanyForm::class)) {
            if ($this->form->getWidget('company_id')->getValue()) {
                $this->listResponse = $offerService->searchOffer(0, 100, array('company_id' => $this->form->getWidget('company_id')->getValue()));
            }
        }
        if (is_a($this->form, PersonForm::class)) {
            if ($this->form->getWidget('person_id')->getValue()) {
                $this->listResponse = $offerService->searchOffer(0, 100, array('person_id' => $this->form->getWidget('person_id')->getValue()));
            }
        }

        $this->invoiceSettings = $this->oc->get(InvoiceSettings::class);
        
        if (isset($this->listResponse) && count($this->listResponse->getObjects()) > 0) {
            $this->setShowDecorator(false);
            $this->render();
        }
    }

}
