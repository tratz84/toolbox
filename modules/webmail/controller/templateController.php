<?php


use core\controller\BaseController;
use core\forms\lists\ListResponse;
use webmail\form\TemplateForm;
use webmail\model\Template;
use webmail\service\EmailService;
use webmail\service\EmailTemplateService;
use core\exception\InvalidStateException;

class templateController extends BaseController {
    
    
    public function action_index() {
        
        $this->render();
    }
    
    public function action_search() {
        
        $templateService = $this->oc->get(EmailTemplateService::class);
        
        $templates = $templateService->readAllTemplates();
        
        $list = array();
        foreach($templates as $t) {
            $list[] = $t->getFields(array('template_id', 'template_code', 'name', 'subject', 'content', 'active'));
        }
        
        
        $lr = new ListResponse(0, count($list), count($list), $list);
        
        $arr = array();
        $arr['listResponse'] = $lr;
        
        $this->json($arr);
        
    }
    
    public function action_edit() {
        
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $templateService = $this->oc->get(EmailTemplateService::class);
        if ($id) {
            $template = $templateService->readTemplate($id);
        } else {
            $template = new Template();
        }
        
        
        $templateForm = new TemplateForm();
        $templateForm->bind($template);
        
        if (is_post()) {
            $templateForm->bind($_REQUEST);
            
            if ($templateForm->validate()) {
                $templateService->saveTemplate( $templateForm );
                
                redirect('/?m=webmail&c=template');
            }
        }
        
        $this->isNew = $template->isNew();
        $this->form = $templateForm;
        
        
        $this->render();
    }
    
    public function action_createOrEdit() {
        
        if (!get_var('code')) {
            throw new InvalidStateException('No valid code set');
        }
        
        $templateService = $this->oc->get(EmailTemplateService::class);
        
        $t = $templateService->readByTemplateCode(get_var('code'));
        if ($t) {
            redirect('/?m=webmail&c=template&a=edit&id='.$t->getTemplateId());
        }
        
        $f = new TemplateForm();
        $f->getWidget('active')->setValue('1');
        $f->getWidget('template_code')->setValue(get_var('code'));
        $templateService->saveTemplate($f);
        
        redirect('/?m=webmail&c=template&a=edit&id='.$f->getWidgetValue('template_id'));
    }
    
    
    public function action_delete() {
        $id = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
        
        $templateService = $this->oc->get(EmailTemplateService::class);
        
        $templateService->deleteTemplate( $id );
        
        redirect('/?m=webmail&c=template');
    }
    
    
    public function action_sort() {
        
        $ids = $_REQUEST['ids'];
        
        $templateService = $this->oc->get(EmailTemplateService::class);
        
        $templateService->updateTemplateSort($ids);
        
        print 'OK';
    }
    
    
    
}
