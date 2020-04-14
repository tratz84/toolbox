<?php


use core\controller\BaseController;
use calendar\service\CalendarService;

class calendarTabController extends BaseController {
    
    
    
    public function action_index() {

        if (isset($this->companyId) == false && isset($this->personId) == false) {
            return;
        }
        
        $companyId = isset($this->companyId) ? $this->companyId : null;
        $personId = isset($this->personId) ? $this->personId : null;
        
        $calendarService = object_container_get(CalendarService::class);
        $this->events = $calendarService->readEventInstancesExplodedByCustomer($companyId, $personId, null, date('Y-m-d', strtotime('+12 months')));
        
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    public function action_search() {
        
        $companyId = get_var('company_id');
        $personId = get_var('person_id');
        
        if (!$companyId && !$personId) {
            return $this->json([
                'success' => false,
                'message' => 'No company/person id set'
            ]);
        }
        
        
        
    }
    
    
}
