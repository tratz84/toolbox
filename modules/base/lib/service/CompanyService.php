<?php


namespace base\service;

use base\forms\CompanyTypeForm;
use base\model\AddressDAO;
use base\model\Company;
use base\model\CompanyDAO;
use base\model\CompanyType;
use base\model\CompanyTypeDAO;
use base\model\EmailDAO;
use base\model\ObjectMetaDAO;
use base\model\PhoneDAO;
use base\util\ActivityUtil;
use core\container\ObjectHookable;
use core\exception\ObjectNotFoundException;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use base\forms\FormChangesHtml;
use base\forms\CompanyForm;

class CompanyService extends ServiceBase implements ObjectHookable {
    
    
    public function readCompany($id, $opts=array()) {
        
        $company = new Company($id);
        if ($company->read() == false) {
            if (isset($opts['null-if-not-found']) && $opts['null-if-not-found']) {
                return null;
            } else {
                throw new ObjectNotFoundException('Requested company not found');
            }
        }
        
//         if ($company->getDeleted())
//             throw new ObjectNotFoundException('Requested company not found');
        
        $addressDao = new AddressDAO();
        $addresses = $addressDao->readByCompany($id);
        $company->setAddressList($addresses);
        
        $emailDao = new EmailDAO();
        $emails = $emailDao->readByCompany($id);
        $company->setEmailList($emails);
        
        $phoneDao = new PhoneDAO();
        $phones = $phoneDao->readByCompany($id);
        $company->setPhoneList($phones);
        
        return $company;
    }
    
    public function save(\base\forms\CompanyForm $companyForm) {
        $companyId = $companyForm->getWidgetValue('company_id');
        if ($companyId) {
            $company = $this->oc->get(CompanyService::class)->readCompany($companyId);
        } else {
            $company = new Company();
        }
        
        $isNew = $company->isNew();
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew($companyForm);
        } else {
            $oldForm = CompanyForm::createAndBind($company);
            $fch = FormChangesHtml::formChanged($oldForm, $companyForm);
        }
        
        $companyForm->fill($company, array('company_type_id', 'company_name', 'contact_person', 'coc_number', 'vat_number', 'note', 'iban', 'bic'));
        
        if (!$company->save()) {
            // exception would also be on it's place
            return false;
        }
        
        $companyForm->getWidget('company_id')->setValue($company->getCompanyId());
        
        $addressDao = new AddressDAO();
        $newAddresses = $companyForm->getWidget('addressList')->asArray();
        $addressDao->mergeFormListMTON('customer__company_address', 'company_id', $company->getCompanyId(), $newAddresses, 'sort');
        
        
        $emailDao = new EmailDAO();
        $newEmails = $companyForm->getWidget('emailList')->asArray();
        $emailDao->mergeFormListMTON('customer__company_email', 'company_id', $company->getCompanyId(), $newEmails, 'sort');
        
        $phoneDao = new PhoneDAO();
        $newPhones = $companyForm->getWidget('phoneList')->asArray();
        $phoneDao->mergeFormListMTON('customer__company_phone', 'company_id', $company->getCompanyId(), $newPhones, 'sort');
        
        if ($isNew) {
            ActivityUtil::logActivityCompany($company->getCompanyId(), 'customer__company', null, 'company-created', 'Bedrijf aangemaakt', $fch->getHtml());
        } else {
            // TODO: check of er wijzigingen zijn
            ActivityUtil::logActivityCompany($company->getCompanyId(), 'customer__company', null, 'company-edited', 'Bedrijf aangepast', $fch->getHtml());
        }
        
        return $company->getCompanyId();
    }
    
    
    public function search($start, $limit, $opts = array()) {
        $cDao = new CompanyDAO();
        
        $cursor = $cDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('company_id', 'company_name', 'contact_person', 'coc_number', 'vat_number', 'edited', 'created', 'company_type_id'));
        
        return $r;
    }
    
    
    public function delete($companyId) {
        // TODO: check (active) contracts
        
        
        // addresses
//         $aDao = new AddressDAO();
//         $aDao->deleteMTON('customer__company_address', 'company_id', $companyId);
        
//         // phone
//         $pDao = new PhoneDAO();
//         $pDao->deleteMTON('customer__company_phone', 'company_id', $companyId);
        
//         // mail
//         $eDao = new EmailDAO();
//         $eDao->deleteMTON('customer__company_email', 'company_id', $companyId);
        
        
//         $cDao = new CompanyDAO();
//         $cDao->delete( $companyId );
        
        // delete meta
        $omDao = new ObjectMetaDAO();
        $omDao->deleteByObject(Company::class, $companyId);
        $omDao->deleteByObject('company', $companyId);              // deprecated ?
        
        
        $companyDao = $this->oc->get(CompanyDAO::class);
        $companyDao->delete( $companyId );
        
        ActivityUtil::logActivityCompany($companyId, 'customer__company', null, 'company-deleted', 'Bedrijf verwijderd');
        
        return true;
    }
    
    
    
    public function readTypes() {
        $ctDao = new CompanyTypeDAO();
        return $ctDao->readAll();
    }
    
    
    public function readReport() {
        $cDao = new CompanyDAO();
        $cursor = $cDao->search();
        
        $list = array();
        while(($c = $cursor->next())) {
            $list[] = $c;
        }
        
        return $list;
    }
    
    public function getCompanyName($id) {
        $c = $this->readCompany($id, array('null-if-not-found' => true));
        if ($c) {
            return $c->getCompanyName();
        } else {
            return null;
        }
    }
   
    
    public function readCompanyType($id) {
        $ctDao = new CompanyTypeDAO();
        
        return $ctDao->read($id);
    }
    
    public function deleteCompanyType($id) {
        $id = (int)$id;
        
        if (!$id)
            return;
        
        $companyDao = new CompanyDAO();
        $companyDao->setCompanyTypeToNULL($id);
        
        $ctDao = new CompanyTypeDAO();
        
        $ctDao->delete($id);
    }
    
    public function updateCompanyTypeSort($ids) {
        $arrIds = explode(',', $ids);
        
        $ctDao = new CompanyTypeDAO();
        $ctDao->updateSort($arrIds);
    }
    
    public function saveCompanyType(CompanyTypeForm $form) {
        $id = $form->getWidgetValue('company_type_id');
        if ($id) {
            $companyType = $this->readCompanyType($id);
        } else {
            $companyType = new CompanyType();
        }
        
        $form->fill($companyType, array('company_type_id', 'type_name', 'default_selected'));
        
        if (!$companyType->save()) {
            return false;
        }
        
        if ($companyType->getDefaultSelected()) {
            $ctDao = new CompanyTypeDAO();
            $ctDao->unsetDefaultSelected($companyType->getCompanyTypeId());
        }
    }
    
}

