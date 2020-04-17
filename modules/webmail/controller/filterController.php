<?php



use core\controller\BaseController;
use webmail\form\FilterForm;
use webmail\model\Filter;
use webmail\service\ConnectorService;

class filterController extends BaseController {
    
    
    public function action_index() {
        
        $this->render();
    }
    
    
    public function action_search() {
        
        $connectorService = $this->oc->get(ConnectorService::class);
        
        $arr = array();
        $arr['listResponse'] = $connectorService->readFilters();
        
        $this->json($arr);
    }
    
    
    public function action_edit() {
        $id = isset($_REQUEST['filter_id'])?(int)$_REQUEST['filter_id']:0;
        
        $connectorService = $this->oc->get(ConnectorService::class);
        if ($id) {
            $filter = $connectorService->readFilter($id);
        } else {
            $filter = new Filter();
        }
        
        $filterForm = $this->oc->create(FilterForm::class);
        $filterForm->bind($filter);
        
        if (is_post()) {
            $filterForm->bind($_REQUEST);
            
            if ($filterForm->validate()) {
                $connectorService->saveFilter($filterForm);
                
                report_user_message(t('Changes saved'));
                redirect('/?m=webmail&c=filter&a=edit&filter_id='.$filterForm->getWidgetValue('filter_id'));
            }
        }
        
        $this->isNew = $filter->isNew();
        $this->form = $filterForm;
        
        $this->render();
    }
    
    
    public function action_sort() {
        $ids = explode(',', get_var('ids'));
        
        $connectorService = $this->oc->get(ConnectorService::class);
        $connectorService->updateFilterSort($ids);
        
        print 'OK';
    }
    
    
    
    public function action_delete() {
        $connectorService = $this->oc->get(ConnectorService::class);
        $connectorService->deleteFilter( get_var('filter_id') );
        
        redirect('/?m=webmail&c=filter');
    }
    
}
