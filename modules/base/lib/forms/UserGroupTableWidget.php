<?php


namespace base\forms;


use core\ObjectContainer;
use core\exception\InvalidStateException;
use core\forms\BaseWidget;
use customer\service\CompanyService;
use customer\service\PersonService;
use base\service\UserService;

class UserGroupTableWidget extends BaseWidget {
    
    
    protected $searchType = null;
    
    protected $defaultText;
    
    
    
    public function __construct($name='customer_id', $defaultValue=null) {
        
        $defaultText = t('Make your choice');
        $endpoint = '/?m=base&c=group&a=select_table';
        $label = t('User or group');
        
        //         parent::__construct();//$name, $defaultValue, $defaultText, $endpoint, $label);
        
        $this->setName($name);
        $this->setLabel($label);
        
        $this->containerClasses[] = 'widget';
        $this->containerClasses[] = 'table-select-widget';
        $this->containerClasses[] = 'user-group-table-select-widget';
        
        hook_htmlscriptloader_enableGroup('iban');
        hook_htmlscriptloader_enableGroup('select-company-list-edit');
        hook_htmlscriptloader_enableGroup('select-person-list-edit');
        
        //
        hook_htmlscriptloader_enableGroup('customer-select-widget');
        hook_htmlscriptloader_enableGroup( 'table-select-widget' );
    }
    
    
    public function setDefaultText($t) { $this->defaultText = $t; }
    public function getDefaultText() { return $this->defaultText; }
    
    
    
    public function setSearchType( $ct ) {
        if ($ct != 'user' && $ct != 'group') {
            throw new InvalidStateException('Invalid searchType');
        }
        
        $this->searchType = $ct;
        
        $this->setEndpoint('/?m=base&c=group&a=select_table&search_type='.$this->customerType);
    }
    
    
    public function bindObject($obj) {
        parent::bindObject($obj);
        
        $userId = null;
        $userGroupId = null;
        
        $fieldFound = false;
        
        if (is_object($obj) && method_exists($obj, 'getUserId')) {
            $userId = $obj->getUserId();
            $fieldFound = true;
        }
        
        if (is_object($obj) && method_exists($obj, 'getUserGroupId')) {
            $userGroupId = $obj->getUserGroupId();
            $fieldFound = true;
        }
        
        if (is_array($obj) && isset($obj[ $this->getName() ])) {
            $val = $obj[ $this->getName() ];
            
            if (strpos($val, 'user-') === 0) {
                $userId = str_replace('user-', '', $val);
                $fieldFound = true;
            }
            else if (strpos($val, 'group-') === 0) {
                $userGroupId = str_replace('group-', '', $val);
                $fieldFound = true;
            }
        }
        
        // no person_id or company_id in $obj? => skip
        if ($fieldFound == false) {
            return;
        }
        
        // gets kinda messy ;) when widget name is 'user_id' => ignore userGroupId & visa versa
        if ($this->name == 'user_id') $userGroupId = null;
        if ($this->name == 'user_group_id') $userId = null;
        
        
        if ($userId) {
            $this->setValue('user-'.$userId);
            
            $us = object_container_get( UserService::class );
            $user = $us->readUser( $userId, ['record-only' => true] );
            
            if ($user) {
                $this->setDefaultText( $user->getUsername() );
            }
            else {
                $this->setDefaultText( 'user-'.$userId );
            }
        }
        else if ($userGroupId) {
            $this->setValue('group-'.$userGroupId);
            
            $us = object_container_get( UserService::class );
            $group = $us->readGroup( $userGroupId, ['null-not-found' => true] );
            
            if ($group) {
                $n = $us->fullGroupName( $group->getUserGroupId() );
                $this->setDefaultText( $n );
            }
            else {
                $this->setDefaultText( 'group-'.$userGroupId );
            }
        } else {
            $this->setDefaultText( t('Make your choice') );
        }
    }
    
    
    
    public function fill($obj, $fields=array()) {
        $v = $this->getValue();
        if ($v === null) $v = '';
        
        if (method_exists($obj, 'setUserId')) {
            $obj->setUserId(0);
            if (strpos($v, 'user-') === 0) {
                $obj->setUserId( str_replace('user-', '', $v) );
            }
        }
        
        if (method_exists($obj, 'setUserGroupId')) {
            $obj->setUserGroupId(0);
            if (strpos($v, 'group-') === 0) {
                $obj->setUserGroupId( str_replace('group-', '', $v) );
            }
        }
    }
    
    
    
    public function render() {
        $htmlLabel = esc_html($this->getLabel());
        
        $html = '';
        $html .= '<div class="'.implode(' ', $this->containerClasses).'">';
        $html .= '<label>'.$htmlLabel.infopopup($this->getInfoText()).'</label>';
        
        $html .= '<toolbox-table-selector name="'.esc_attr($this->getName()).'"
                         value="'.esc_attr($this->getValue()).'"
                         default-text="'.esc_attr($this->getDefaultText()).'"
                         url="'.appUrl('/?m=base&c=group&a=select_table').'"></toolbox-table-selector>';
        
        $html .= '</div>';
        
        
        return $html;
    }
    
    
    
}
