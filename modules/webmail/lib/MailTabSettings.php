<?php


namespace webmail;

use base\model\Company;
use base\model\Person;
use base\service\CompanyService;
use base\service\PersonService;
use function webmail\MailTabSettings\getFilterCount as count;


class MailTabSettings {
    
    protected $companyId;
    protected $personId;
    
    protected $data = null;
    
    protected $cache_defaultFilters = null;
    
    
    public function __construct($companyId, $personId) {
        $this->companyId = $companyId;
        $this->personId = $personId;
        
        $this->load();
    }
    
    public function getCompanyId() { return $this->companyId; }
    public function getPersonId() { return $this->personId; }
    
    protected function load() {
        if (isset($this->companyId) && $this->companyId) {
            $this->data = object_meta_get(Company::class, $this->companyId, 'mailtab-settings');
        }
        else if (isset($this->personId) && $this->personId) {
            $this->data = object_meta_get(Person::class, $this->personId, 'mailtab-settings');
        }
        
        if (is_array($this->data) == false) {
            $this->data = array();
            
            if (isset($this->companyId))
                $this->data['company_id'] = $this->companyId;
            if (isset($this->personId))
                $this->data['person_id'] = $this->personId;
        }
        
        if (isset($this->data['default_filters']) == false) {
            $this->data['default_filters'] = true;
        }
        
        if (isset($this->data['filters']) == false) {
            $this->data['filters'] = array();
        }
    }
    
    public function save() {
        if (isset($this->companyId) && $this->companyId) {
            return object_meta_save(Company::class, $this->companyId, 'mailtab-settings', $this->data);
        }
        else if (isset($this->personId) && $this->personId) {
            return object_meta_save(Person::class, $this->companyId, 'mailtab-settings', $this->data);
        }
        
        return false;
    }
    
    public function getData() { return $this->data; }
    
    public function applyDefaultFilters() { return $this->data['default_filters'] ? true : false; }
    public function setApplyDefaultFilters($bln) { return $this->data['default_filters'] = $bln ? true : false; }
    
    public function getDefaultFilters() {
        // default filters disabled? => return empty result
        if ($this->applyDefaultFilters() == false) {
            return array();
        }
        
        if ($this->cache_defaultFilters === null) {
            $filters = array();
            
            if ($this->getCompanyId()) {
                $companyService = object_container_get(CompanyService::class);
                $company = $companyService->readCompany( $this->getCompanyId() );
                
                foreach($company->getEmailList() as $e) {
                    $filters[] = array(
                        'filter_type' => 'email',
                        'filter_value' => $e->getEmailAddress()
                    );
                }
            }
            if ($this->getPersonId()) {
                $personService = object_container_get(PersonService::class);
                $person = $personService->readPerson( $this->getPersonId() );
                
                foreach($person->getEmailList() as $e) {
                    $filters[] = array(
                        'filter_type' => 'email',
                        'filter_value' => $e->getEmailAddress()
                    );
                }
            }
            
            $this->cache_defaultFilters = $filters;
        }
        
        return $this->cache_defaultFilters;
    }
    
    
    public function getFilterCount() { return \count($this->data['filters']); }
    public function getFilterNo($no) { return $this->data['filters'][$no]; }
    public function clearFilters() { $this->data['filters'] = array(); }
    public function getFilters() {
        return $this->data['filters'];
    }
    public function addFilter($filter_type, $filter_value) {
        $this->data['filters'][] = array(
            'filter_type' => $filter_type,
            'filter_value' => $filter_value
        );
    }
    
    
    
    
}