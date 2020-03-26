<?php

namespace core\controller;

use core\container\ActionContainer;
use core\event\ActionValidationEvent;
use core\event\EventBus;
use core\exception\ObjectNotFoundException;

class FormController extends BaseController {
    
    protected $varNameId = 'id';
    
    protected $formClass         = null;
    protected $objectClass       = null;
    
    protected $serviceClass      = null;
    protected $serviceFuncSearch = null;
    protected $serviceFuncRead   = null;
    protected $serviceFuncSave   = null;
    protected $serviceFuncDelete = null;
    
    
    
    public function action_index() {
        
        return $this->render();
    }
    
    public function action_search() {
        $pageNo = get_var('pageNo');
        if (is_numeric($pageNo) == false)
            $pageNo = 0;
        $limit = $this->ctx->getPageSize();
        
        $service = $this->oc->get( $this->serviceClass );
        
        $search_func = $this->serviceFuncSearch;
        $r = $service->$search_func($pageNo*$limit, $limit, $_REQUEST);
        
        $arr = array();
        $arr['listResponse'] = $r;
        
        $this->json($arr);
    }
    
    
    
    public function action_edit($opts=array()) {
        // set default options
        if (isset($opts['render']) == false) $opts['render'] = true;
        if (isset($opts['return_on_object_deleted']) == false) $opts['return_on_object_deleted'] = false;
        
        $id = get_var($this->varNameId);
        
        $service = $this->oc->get( $this->serviceClass );
        if ($id) {
            $read_func = $this->serviceFuncRead;
            $this->object = $service->$read_func($id);
            
            // some controllers generate specific error on invalid requested object
            if ($this->object == false || method_exists($this->object, 'getDeleted') && $this->object->getDeleted()) {
                return $opts['return_on_object_deleted'];
            }
        } else {
            $this->object = new $this->objectClass();
        }
        
        $form = $this->oc->create( $this->formClass );
        $form->bind( $this->object );
        
        if (is_post()) {
            $form->bind($_REQUEST);
            
            if ($form->validate()) {
                $save_func = $this->serviceFuncSave;
                $r = $service->{$save_func}($form);
                
                $objId = null;
                if (is_numeric($r)) {
                    $objId = $r;
                }
                
                if (isset($opts['stay_after_save']) && $opts['stay_after_save'] && $objId != null) {
                    report_user_message(t('Changed saved'));
                    redirect('/?m='.$this->getModuleName().'&c='.$this->getControllerPath().'&a=edit&'.$this->varNameId.'='.$objId);
                } else {
                    redirect('/?m='.$this->getModuleName().'&c='.$this->getControllerPath());
                }
            }
        }
        
        
        
        $this->isNew = $this->object->isNew();
        $this->form = $form;
        
        // 
        $this->actionContainer = new ActionContainer($this->objectClass, $this->object->getPrimaryKeyValue());
        hook_eventbus_publish($this->actionContainer, $this->getModuleName(), $this->objectClass.'-edit');
        
        // skip rendering?
        if ($opts['render'] == false)
            return;
        
        $this->render();
    }
    
    
    
    public function action_delete() {
        $id = get_var( $this->varNameId );
        
        $service = $this->oc->get( $this->serviceClass );
        $read_func = $this->serviceFuncRead;
        $object = $service->$read_func($id);
        
        if (!$object) {
            throw new ObjectNotFoundException('Object not found');
        }
        
        /**
         * @var EventBus $eventBus
         */
        $eventBus = $this->oc->get(EventBus::class);
        $evt = $eventBus->publish(new ActionValidationEvent($object, $this->getModuleName(), $this->objectClass.'-delete'));
        
        if ($evt->hasErrors()) {
            report_user_error($evt->getErrors());
        } else {
            $delete_func = $this->serviceFuncDelete;
            $service->$delete_func($id);
        }
        
        
        redirect('/?m='.$this->getModuleName().'&c='.$this->getControllerPath());
    }
    
    
}

