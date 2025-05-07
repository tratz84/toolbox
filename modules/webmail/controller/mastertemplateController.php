<?php




use core\controller\BaseController;
use webmail\service\EmailTemplateService;
use webmail\form\MasterTemplateForm;
use webmail\model\MasterTemplate;

class mastertemplateController extends BaseController {
    
    
    
    public function action_index() {
        
        
        $etservice = object_container_get( EmailTemplateService::class );
        $this->mts = $etservice->listMasterTemplates();
        
        
        return $this->render();
    }
    
    
    
    public function action_edit() {
        $this->isNew = get_var('id') ? false : true;
        $etservice = object_container_get( EmailTemplateService::class );
        
        $this->form = new MasterTemplateForm();
        
        if (get_var('id')) {
            $mt = $etservice->readMasterTemplate( get_var('id') );
        }
        else {
            $mt = new MasterTemplate();
        }
        
        $this->form->bind( $mt );
        
        
        
        if (is_post()) {
            $this->form->bind( $_REQUEST );
            
            if ($this->form->validate()) {
                if (isset($_FILES['f']) && $_FILES['f']['size'] > 0) {
                    $html = file_get_contents($_FILES['f']['tmp_name']);
                    $this->form->setField('content', $html);
                }
                
                $mt = $etservice->saveMasterTemplate( $this->form );
                
                report_user_message( t('Changes saved') );
                redirect('/?m=webmail&c=mastertemplate&a=edit&id='.$mt->getMasterTemplateId());
            }
        }
        
        $this->mt = $mt;
        $this->hasTemplate = $mt->getContent() ? true : false;
        
        
        return $this->render();
    }
    
    
    public function action_view_tpl() {
        $etservice = object_container_get( EmailTemplateService::class );
        
        $this->form = new MasterTemplateForm();
        
        $mt = $etservice->readMasterTemplate( get_var('id') );
        
        print $mt->getContent();
    }
    
    
    public function action_delete() {
        $etservice = object_container_get( EmailTemplateService::class );
        
        $this->form = new MasterTemplateForm();
        
        $etservice->deleteMasterTemplate( get_var('id') );
        
        
        report_user_message( t('Master template deleted') );
        redirect( '/?m=webmail&c=mastertemplate' );
    }
    
    
    
}


