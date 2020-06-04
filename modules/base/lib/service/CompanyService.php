<?php


namespace base\service;

use base\forms\CompanyTypeForm;
use base\model\Company;
use base\model\CompanyDAO;
use base\model\CompanyType;
use base\model\CompanyTypeDAO;
use base\model\ObjectMetaDAO;
use base\util\ActivityUtil;
use core\container\ObjectHookable;
use core\exception\ObjectNotFoundException;
use core\service\ServiceBase;

class CompanyService extends ServiceBase implements ObjectHookable {
    
    
    public function readCompany($id, $opts=array()) {
        $fm = form_mapping (Company::class);
        $company = $fm->readObject( $id );
        
        if (!$company) {
            if (isset($opts['null-if-not-found']) && $opts['null-if-not-found']) {
                return null;
            } else {
                throw new ObjectNotFoundException('Requested company not found');
            }
        }
        
        return $company;
    }
    
    public function save(\base\forms\CompanyForm $companyForm) {
        $fm = form_mapping( Company::class );
        
        $obj = $fm->saveForm( $companyForm );
        
        return $obj->getCompanyId();
    }
    
    
    public function search($start, $limit, $opts = array()) {
        $fdm = form_mapping( Company::class );
        
        return $fdm->search($start, $limit, $opts);
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

