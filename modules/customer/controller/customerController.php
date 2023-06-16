<?php


use customer\service\CustomerService;
use core\controller\BaseController;
use customer\service\CompanyService;
use customer\service\PersonService;
use customer\forms\lists\CustomerIndexTable;

class customerController extends BaseController {
    
    
    public function action_index() {
        
        $this->cit = object_container_create( CustomerIndexTable::class );
        
        $this->render();
    }
    
    
    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();
        
        $customerService = $this->oc->get(CustomerService::class);
        
        $r = $customerService->search($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        
        $this->json($arr);
    }
    
    public function action_select2() {
        $customerService = $this->oc->get(CustomerService::class);
        
        $r = $customerService->search(0, 20, $_REQUEST);
        
        
        $arr = array();
        
        if (isset($_REQUEST['name']) == false || trim($_REQUEST['name']) == '') {
            $arr[] = array(
                'id' => '0',
                'text' => 'Maak uw keuze'
            );
        }
        foreach($r->getObjects() as $customer) {
            $arr[] = array(
                'id' => $customer['type'] . '-' . $customer['id'], 
                'text' => $customer['name']
            );
        }
        
        
        $result = array();
        $result['results'] = $arr;
        
        $this->json($result);
    }
    
    public function action_select_table() {
        $customerService = object_container_get(CustomerService::class);
        
        $opts = array();
        $opts['fetch_addresses'] = true;
        $opts['q'] = trim( get_var('q', '') );
        
        
        $arr = array();
        
        $result = array();
        $result['header_fields'] = array(
            'type'           => t('Type'),
            'name'           => t('Name'),
            'contact_person' => t('Contact person'),
            'adres1'         => t('Adres'),
        );
        
        if (trim($opts['q']) == '') {
            $opts['customer_id'] = get_var('value');
        }
        
        $r = $customerService->search(0, 8, $opts);
        foreach($r->getObjects() as $customer) {
            $i = array(
                'id'             => $customer['type'] . '-' . $customer['id'],
                'type'           => t('customer_type.'.$customer['type']),
                'name'           => $customer['name'],
                'contact_person' => $customer['contact_person'] ?? '',
                'adres1'         => '',
                'default_text'   => $customer['name']
            );
            
            if (count($customer['addresses']) > 0)
                $i['adres1'] = trim( $customer['addresses'][0]['street'] . ' ' . $customer['addresses'][0]['street_no'] . ' ' . $customer['addresses'][0]['city'] );
            
            $arr[] = $i;
        }
        
        
        $result['results'] = $arr;
        
        $this->json($result);
        
    }
    
    
    public function action_new() {
        
        return $this->render();
    }
    
    
    
    
    public function action_emailaddresses() {
        
        $companyId = null;
        $personId = null;
        
        if (get_var('customer_id')) {
            if (strpos(get_var('customer_id'), 'company-') === 0) {
                $companyId = (int)substr(get_var('customer_id'), strlen('company-'));
            }
            if (strpos(get_var('customer_id'), 'person-') === 0) {
                $personId = (int)substr(get_var('customer_id'), strlen('person-'));
            }
        }
        
        if (!$companyId && get_var('company_id')) {
            $companyId = (int)get_var('company_id');
        }
        if (!$personId && get_var('person_id')) {
            $personId = (int)get_var('person_id');
        }
        
        $addresses = array();
        
        if ($companyId) {
            /** @var CompanyService $companyService */
            $companyService = object_container_get(CompanyService::class);
            
            $company = $companyService->readCompany($companyId, ['null-if-not-found' => true]);
            
            if ($company) foreach($company->getEmailList() as $e) {
                $addresses[] = array(
                    'name' => $e->getDescription(),
                    'email' => $e->getEmailAddress()
                );
            }
        }
        if ($personId) {
            /** @var PersonService $personService */
            $personService = object_container_get(PersonService::class);
            
            $person = $personService->readPerson($personId);
            
            if ($person) foreach($person->getEmailList() as $e) {
                $addresses[] = array(
                    'name' => $e->getDescription(),
                    'email' => $e->getEmailAddress()
                );
            }
        }
        
        return $this->json([
            'success' => true,
            'addresses' => $addresses
        ]);
    }
    
    
    public function action_all_customers() {
        $customerService = object_container_get( CustomerService::class );
        
        $customers = $customerService->readAllCustomers();
        
        $result = array();
        $result['success'] = true;
        $result['customers'] = array();
        foreach($customers as $c) {
            // this is probably called because there is a new customer added with a popup
            // so skip deleted customers
            if ($c['deleted'])
                continue;
            
            $result['customers'][] = array(
                'customer_id' => $c['customer_id']
                , 'name' => $c['name']
                , 'deleted' => $c['deleted']
            );
        }
        
        $this->json($result);
    }
    
    
}


