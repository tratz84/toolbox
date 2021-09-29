<?php

use core\controller\BaseController;
use fail2ban\service\Fail2banService;
use fail2ban\form\IpSettingForm;
use fail2ban\model\IpSetting;
use core\forms\lists\ListResponse;
use fail2ban\form\IpSettingIndexTable;


class ipsettingsController extends BaseController {

	public function action_index() {
	   
	    $f2bService = object_container_get( Fail2banService::class );
	    $ips = $f2bService->readIpSettings();
	    
	    $this->indexTable = new IpSettingIndexTable();
	    
        
		$this->render();

	}

	public function action_search() {
	   
	    $f2bService = object_container_get( Fail2banService::class );
	    $ips = $f2bService->readIpSettings();
	    
	    $lr = ListResponse::fillByDBObjects(0, count($ips), $ips, ['ip_setting_id', 'active', 'ip', 'description', 'type']);
	    
        
	    $this->json(array(
	        'listResponse' => $lr
	    ));
	}

	public function action_edit() {
        
        $f2bService = object_container_get( Fail2banService::class );
        
	    $this->form = new IpSettingForm();
	    
	    $id = get_var('id');
	    if ($id) {
	        $ipSetting = $f2bService->readIpSetting( $id );
	    }
	    else {
	        $ipSetting = new IpSetting();
	    }
	    
	    
	    $this->form->bind( $ipSetting );
	    
	    
	    if (is_post()) {
	        $this->form->bind( $_REQUEST );
	        
	        
	        if ($this->form->validate()) {
	            
	            $is = $f2bService->saveIpSetting( $this->form );
	            
	            report_user_message( t('Changes saved') );
	            
	            redirect( '/?m=fail2ban&c=ipsettings&a=edit&id=' . $is->getIpSettingId() );
	        }
	        
	    }
	    
	    
	    $this->isNew = $ipSetting->isNew();
	    
	    

		$this->render();

	}
	
	public function action_save_sort() {
	    $f2bService = object_container_get( Fail2banService::class );
	    
	    $f2bService->updateSortIpSetting( get_var('ids') );
	    
	    print 'OK';
	}

	public function action_delete() {
	


	}


}

