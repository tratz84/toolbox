<?php

namespace base\form;

use base\service\UserService;
use core\forms\CheckboxField;
use core\forms\validator\NotEmptyValidator;
use base\model\UserGroup;

class UserGroupForm extends \core\forms\CodegenBaseForm {

	public function __construct() {
		
		parent::__construct();
		
		$this->codegen();
		
		
		$items = $this->generateGroupItems();
		$this->getWidget( 'parent_user_group_id' )->setOptionItems( $items );
		
		$userService = object_container_get(UserService::class);
		$capabilities = $userService->getCapabilities();
		foreach($capabilities as $c) {
	        $w = new CheckboxField('capability_' . $c['module_name'].'-'.$c['capability_code'], '', t('modulename.'.$c['module_name']) . ' - ' . $c['short_description']);
	        $w->addContainerClass('user-capability');
	        
	        $w->setInfoText($c['infotext']);
	        $w->setField('module_name', $c['module_name']);
	        $w->setField('capability_code', $c['capability_code']);
	        
	        $this->getWidget('container-permissions')->addWidget($w);
		}
		
		
		$this->addValidator('group_name', new NotEmptyValidator());
		$this->addValidator('parent_user_group_id', function($form) {
		    $pid = $form->getWidgetValue('parent_user_group_id');
		    $id = $form->getWidgetValue('user_group_id');
		    
		    // TODO: check if parent-user_group_id is not a child of user_group_id
		    if ($pid && $pid == $id) {
		        return t( 'Parent group same as current group');
		    }
		});
		
	}
	
	protected function generateGroupItems() {
	    
	    $userService = object_container_get( UserService::class );
	    $groups = $userService->readAllGroups();
	    
	    $items = array();
	    $items[''] = t('Make your choice');
	    
	    //         var_export( $groups );exit;
	    
	    $items = $this->_generateGroupList( 0, $groups, $items, '' );
	    
	    return $items;
	}
	
	protected function _generateGroupList( $currentParentGroupId, $allGroups, $items, $depth) {
	    
	    foreach($allGroups as $g) {
	        $pid = (int)$g->getParentUserGroupId();
	        
	        if ($pid == $currentParentGroupId) {
	            $items[ $g->getUserGroupId() ] = ($depth ? $depth . ' ' : ''). $g->getGroupName();
	            //                 var_export($items);exit;
	            
	            $items = $this->_generateGroupList( $g->getUserGroupId(), $allGroups, $items, $depth . '-' );
	        }
	    }
	    
	    return $items;
	}
	
	
	public function bind($obj) {
	    parent::bind( $obj );
	    
	    // bind permissions
	    if ($obj instanceof UserGroup) {
	        foreach( $obj->getCapabilities() as $cap ) {
	            $c = $cap->getModuleName() . '-' . $cap->getCapabilityCode();
	            $w = $this->getWidget('capability_'.$c);
	            if ($w) {
	                $w->setValue( 1 );
	            }
	        }
	    }
	}
	
	
	
	
	public function codegen() {
		
		
		$w1 = new \core\forms\HiddenField('user_group_id', NULL, t('Hidden field'));
		$this->addWidget( $w1 );
		$w2 = new \core\forms\SelectField('parent_user_group_id', NULL, [], t('Parent group'));
		$this->addWidget( $w2 );
		$w3 = new \core\forms\TextField('group_name', NULL, t('Group name'));
		$this->addWidget( $w3 );
		$w4 = new \core\forms\FieldSetContainer('container-permissions', t('Permissions'));
		$this->addWidget( $w4 );
		
	}




}

