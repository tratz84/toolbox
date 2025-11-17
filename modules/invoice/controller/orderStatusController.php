<?php



use core\controller\BaseController;
use core\forms\lists\ListResponse;
use invoice\form\OrderStatusForm;
use invoice\model\OrderStatus;
use invoice\service\OfferService;
use invoice\service\OrderService;

class orderStatusController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
        
        $this->addTitle( t('Order states') );
    }
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_edit() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $orderService = object_container_get(OrderService::class);
        if ($id) {
            $orderStatus = $orderService->readOrderStatus($id);
            
            $this->addTitle( t('Edit order status') . ' ' . $orderStatus->getDescription() );
        } else {
            $orderStatus = new OrderStatus();
            
            $this->addTitle( t('New order status') );
        }
        
        
        $orderStatusForm = new OrderStatusForm();
        $orderStatusForm->bind($orderStatus);
        
        if (is_post()) {
            $orderStatusForm->bind($_REQUEST);
            
            if ($orderStatusForm->validate()) {
                $orderStatus = $orderService->saveOrderStatus($orderStatusForm);
                
                report_user_message( t('Changes saved') );
                
                redirect('/?m=invoice&c=orderStatus&a=edit&id='.$orderStatus->getOrderStatusId());
            }
            
        }
        
        
        
        $this->isNew = $orderStatus->isNew();
        $this->form = $orderStatusForm;
        
        
        $this->render();
        
    }
    
    
    
    public function action_search() {
        $orderService = object_container_get(OrderService::class);
        
        $orderStatus = $orderService->readAllOrderStatus();
        
        $list = array();
        foreach($orderStatus as $os) {
            $list[] = $os->getFields(array('order_status_id', 'description', 'active', 'default_selected'));
        }
        
        
        $lr = new ListResponse(0, count($orderStatus), count($orderStatus), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
    }
    
    public function action_sort() {
        if (isset($_REQUEST['ids'])) {
            $ids = explode(',', $_REQUEST['ids']);
            
            $os = object_container_get(OrderService::class);
            $os->updateOrderStatusSort($ids);
            
        }
        
        print 'OK';
    }
    
    
    public function action_delete() {
        
        $orderService = object_container_get(OrderService::class);
        $orderService->deleteOrderStatus($_REQUEST['id']);
        
        redirect('/?m=invoice&c=orderStatus');
    }
    
    
}


