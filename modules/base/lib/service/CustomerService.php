<?php

namespace base\service;

use base\model\CountryDAO;
use base\model\Customer;
use base\model\CustomerDAO;
use core\Context;
use core\ObjectContainer;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use base\model\EmailDAO;

class CustomerService extends ServiceBase {
    
    
    
    public function getCountries() {
        $cDao = new CountryDAO();
        
        return $cDao->readAsMap();
    }
    
    public function readCountries() {
        $cDao = new CountryDAO();
        
        return $cDao->readAll();
    }
    
    public function getCountryIso2($countryId) {
        if (!$countryId)
            return null;
        
        $cDao = new CountryDAO();
        $c = $cDao->read($countryId);
        
        if ($c) {
            return $c->getCountryIso2();
        } else {
            return null;
        }
    }
    
    
    
    /**
     * search() - searches in both customer__person and customer__company table
     * 
     * @param array $opts
     */
    public function search($start, $limit, $opts = array()) {
        $cDao = new CustomerDAO();
        
        $opts['companiesEnabled'] = Context::getInstance()->isCompaniesEnabled();
        $opts['personsEnabled'] = Context::getInstance()->isPersonsEnabled();
        
        $opts['start'] = $start;
        $opts['limit'] = $limit;
        $cursor = $cDao->search($opts);
        
        $cursor = $cDao->search($opts);
        $count = $cDao->searchCount($opts);
        
        $r = ListResponse::fillByCursor(0, $limit, $cursor, array('id', 'type', 'name', 'contact_person'));
        $r->setStart($start);
        $r->setRowCount($count);
        
        return $r;
        
    }
    
    
    public function readReport($start, $limit) {
        $cDao = new CustomerDAO();
        
        $opts['companiesEnabled'] = Context::getInstance()->isCompaniesEnabled();
        $opts['personsEnabled'] = Context::getInstance()->isPersonsEnabled();
        
        $cursor = $cDao->search($opts);
        
        if ($start)
           $cursor->moveTo($start);
        
        $customers = array();
        while($cursor->hasNext()) {
            $c = $cursor->next();
            
            $companyId = $c->getType() == 'company' ? $c->getId() : null;
            $personId = $c->getType() == 'person' ? $c->getId() : null;
            
            $customer = $this->readCustomerAuto( $companyId, $personId );
            
            if ($customer) {
                
                $cd = $customer->getFields(array('customer_id', 'company_id', 'company_name', 'person_id', 'firstname', 'insert_lastname', 'lastname', 'iban', 'bic', 'vat_number', 'coc_number', 'edited', 'created'));
                
                $al = $customer->getAddressList();
                if (count($al)) {
                    $a = $al[0]->getFields(array('street', 'street_no', 'zipcode', 'city'));
                    
                    $cd = array_merge($cd, $a);
                }
                
                $pl = $customer->getPhoneList();
                if (count($pl)) {
                    $p = $pl[0]->getFields(array('phonenr'));
                    
                    $cd = array_merge($cd, $p);
                }
                
                $el = $customer->getEmailList();
                if (count($el)) {
                    $e = $el[0]->getFields(array('email_address'));
                    
                    $cd = array_merge($cd, $e);
                }
                
                $customers[] = $cd;
            }
            
            if (count($customers) >= $limit) {
                break;
            }
        }
        
        $lr = new ListResponse($start, $limit, $cursor->numRows(), $customers);
        
        return $lr;
    }

    public function readCustomerStrId($strCustomerId) {
        if (strpos($strCustomerId, 'company-') === 0) {
            $cid = substr($strCustomerId, strlen('company-'));
            return $this->readCustomerAuto($cid, null);
        }
        
        if (strpos($strCustomerId, 'person-') === 0) {
            $pid = substr($strCustomerId, strlen('person-'));
            return $this->readCustomerAuto(null, $pid);
        }
        
        return null;
    }
    
    public function readCustomerAuto($companyId=null, $personId=null) {
        
        if ($companyId) {
            $companyService = ObjectContainer::getInstance()->get(CompanyService::class);
            $c = $companyService->readCompany($companyId);
            
            if ($c) {
                $customer = new Customer();
                $customer->setByCompany($c);
                
                return $customer;
            }
        }
        
        if ($personId) {
            $personService = ObjectContainer::getInstance()->get(PersonService::class);
            $p = $personService->readPerson($personId);
            
            if ($p) {
                $customer = new Customer();
                $customer->setByPerson($p);
                
                return $customer;
            }
        }
        
        return null;
    }
    
    /**
     * readCustomerByEmail() - reads first company/person by emailaddress
     */
    public function readCustomerByEmail($email) {
        $eDao = new EmailDAO();
        
        $emails = $eDao->readByEmail($email);
        
        foreach($emails as $e) {
            if ($e->getField('company_id') || $e->getField('person_id')) {
                return $this->readCustomerAuto($e->getField('company_id'), $e->getField('person_id'));
            }
        }
        
        return null;
    }
    
}
