<?php


namespace base\service;

use base\form\UserGroupForm;
use base\forms\FormChangesHtml;
use base\forms\UserForm;
use base\model\ResetPassword;
use base\model\ResetPasswordDAO;
use base\model\User;
use base\model\UserCapability;
use base\model\UserCapabilityDAO;
use base\model\UserDAO;
use base\model\UserGroup;
use base\model\UserGroupCapability;
use base\model\UserGroupCapabilityDAO;
use base\model\UserGroupDAO;
use base\model\UserGroupUserDAO;
use base\model\UserIpDAO;
use base\user\UserCapabilityContainer;
use base\util\ActivityUtil;
use core\Context;
use core\ObjectContainer;
use core\event\EventBus;
use core\exception\ObjectNotFoundException;
use core\exception\SecurityException;
use core\service\ServiceBase;
use function ctx;
use function remote_addr;
use webmail\mail\SendMail;
use webmail\model\Email;
use webmail\model\EmailTo;
use webmail\service\EmailService;



class UserService extends ServiceBase {
    
    
    protected $cache_mapGroupsFlat = null;
    
    
    public function __construct() {
        
    }
    
    
    public function search($start, $limit, $opts=array()) {
        $fields = array('user_id', 'username', 'email', 'edited', 'created', 'user_type', 'firstname', 'lastname', 'fullname');
        
        return $this->daoSearch(UserDAO::class, $opts, $fields, $start, $limit);
    }
    
    
    public function readUser($userId, $opts=array()) {
        $uDao = new UserDAO();
        
        $user = $uDao->read($userId);
        if (!$user)
            return null;
        
        if (isset($opts['record-only']) && $opts['record-only'])
            return $user;
        
        $ugDao = new UserGroupDAO();
        $ugs = $ugDao->readByUser($userId);
        $user->setGroups( $ugs );
        
        $ucDao = new UserCapabilityDAO();
        $capabilities = $ucDao->readByUser($user->getUserId());
        $user->setCapabilities($capabilities);
        
        $upDao = new UserIpDAO();
        $ips = $upDao->readByUser($user->getUserId());
        $user->setIps($ips);
        
        return $user;
    }
    
    public function readUserGroups( $userId ) {
        $ugDao = new UserGroupDAO();
        $ugs = $ugDao->readByUser($userId);
        
        return $ugs;
    }
    
    
    public function saveUser(UserForm $form) {
        $userId = $form->getWidget('user_id')->getValue();
        
        if ($userId) {
            $user = $this->readUser($userId);
        } else {
            $user = new User();
        }
        
        // handle..
        $form->fill($user, array('username', 'password', 'user_type', 'email', 'firstname', 'lastname'));
        
        $user->save();
        
        
        
        // save groups
        if (ctx()->isUserGroupsEnabled()) {
            $ugWidgets = $form->getWidget('user-groups')->getWidgets();
            $ugs = array();
            foreach($ugWidgets as $w) {
                if ( $w->getField('user_group_id') && $w->getValue() ) {
                    $ugs[] = array(
                        'user_id' => $user->getUserId(),
                        'user_group_id' => $w->getField('user_group_id')
                    );
                }
            }
            $uguDao = new UserGroupUserDAO();
            $uguDao->mergeFormListMTO1( 'user_id', $user->getUserId(), $ugs );
        }
        
        
        
        // save capabilities
        $ucDao = new UserCapabilityDAO();
        
        $oldUserCapabilities = $ucDao->readByUser($user->getUserId());
        $userCapabilities = array();
        
        $capabilities = $form->getWidget('user-capabilities');
        $capabilityWidgets = $capabilities->getWidgets();
        $cnt=0;
        for($x=0; $x < count($capabilityWidgets); $x++) {
            $w = $capabilityWidgets[$x];
            
            $module_name = $w->getField('module_name');
            $capability_code = $w->getField('capability_code');
            
            if ($module_name == null || $capability_code == null) continue;
            
            if ($w->getValue()) {
                // reuse old objects
                if (isset($oldUserCapabilities[$cnt])) {
                    $c = $oldUserCapabilities[$cnt];
                    $cnt++;
                } else {
                    $c = new UserCapability();
                }
                
                $c->setUserId($user->getUserId());
                $c->setModuleName($module_name);
                $c->setCapabilityCode($capability_code);
                
                $userCapabilities[] = $c;
            }
        }
        
        $ucDao = new UserCapabilityDAO();
        $ucDao->mergeFormListMTO1('user_id', $user->getUserId(), $userCapabilities);
        
        
        $upDao = new UserIpDAO();
        $newIps = $form->getWidget('ips')->getObjects();
        $upDao->mergeFormListMTO1('user_id', $user->getUserId(), $newIps);
        
        return $user;
    }
    
    public function deleteUser($userId) {
        $user = $this->readUser($userId);
        
        $ucDao = new UserCapabilityDAO();
        $ucDao->deleteByUser($userId);
        
        $uiDao = new UserIpDAO();
        $uiDao->deleteByUser($userId);
        
        $uDao = new UserDAO();
        $uDao->delete($userId);
        
        $uguDao = new UserGroupUserDAO();
        $uguDao->deleteByUser($userId);
        
        return $user;
    }

    
    public function readByAutologinToken($t) {
        $uDao = new UserDAO();
        $users = $uDao->readByAutologinToken($t);

        if (count($users) == 0)
            return null;
        
        $uDao->resetAutologinToken($t);
        
        list ($token, $time) = explode('$', $t);
        
        if (preg_match('/^\\d{14}$/', $time) == false) {
            return null;
        }
        
        if (((int)date('YmdHis') - (int)$time) > 5) {
            return null;
        }
        
        return $users[0];
    }
    
    
    public function readByUsername($u) {
        $uDao = new UserDAO();
        $user = $uDao->readByUsername($u);
        
        if ($user == null) {
            return null;
        }
        
        return $this->readUser( $user->getUserId() );
    }
    
    
    public function readAllUsers() {
        $uDao = new UserDAO();
        
        $users = $uDao->readAll();
        
        return $users;
    }
    
    
    public function readUsersInGroup( $userGroupId ) {
        $uDao = new UserDAO();
        
        return $uDao->readByGroup($userGroupId);
    }
    
    
    
    public function generateAutologinToken($user_id, $prefix=null) {
        
        $token = md5(uniqid().uniqid().uniqid().uniqid().uniqid().uniqid().uniqid().uniqid());
        // set timestamp, token may only be used for 10 seconds..
        $token .= '$' . date('YmdHis');
        
        if ($prefix) {
            if (strlen($prefix) > 8)
                $prefix = substr($prefix, 0, 8);
            $token = $prefix . $token;
        }
        
        $uDao = new UserDAO();
        $uDao->setAutologinToken($user_id, $token);
        
        return $token;
    }
    
    
    public function applyResetPassword($resetPassword, $newPassword) {
        
        // check if user still exists
        $user = $this->readUser( $resetPassword->getUserId() );
        if ($user == null) {
            throw new ObjectNotFoundException('User not found');
        }
        
        // mark link as used
        $resetPassword->setUsed( date('Y-m-d H:i:s') );
        $resetPassword->setUsedIp( remote_addr() );
        $resetPassword->save();
        
        // update password
        $pw = User::encryptPassword($newPassword);
        
        $uDao = object_container_get(UserDAO::class);
        $uDao->setPassword($user->getUserId(), $pw);
    }
    
    
    public function getCapabilities( $opts = array() ) {
        
        /**
         * @var EventBus $eventBus
         */
        $eventBus = ObjectContainer::getInstance()->get(EventBus::class);
        
        $ucc = new UserCapabilityContainer();
        
        $eventBus->publishEvent($ucc, 'base', 'user-capabilities');
        
        $caps = $ucc->getCapabilities();
        
        if (isset($opts['as_map']) && $opts['as_map']) {
            $map = array();
            
            foreach($caps as $c) {
                $map[ $c['module_name'] . '_' . $c['capability_code'] ] = $c;
            }
            return $map;
        }
        
        
        return $caps;
    }
    
    /**
     * 
     * @param int $userId
     * @param array $capabilities - [ ['module_name' => ..., 'capability_code' => ....], ... ]
     */
    public function setUserCapabilities( $userId, $capabilities=array() ) {
        
        $user = $this->readUser( $userId );
        
        $caps = $user->getCapabilities();
        
        // delete old
        foreach( $caps as $c ) {
            $found = false;
            
            foreach($capabilities as $newCap) {
                if ($c->getModuleName() == $newCap['module_name'] && $c->getCapabilityCode() == $newCap['capability_code']) {
                    $found = true;
                    break;
                }
            }
            
            if ($found == false)
                $c->delete();
        }
        
        
        // add new
        foreach($capabilities as $newCap) {
            $found = false;
            
            foreach( $caps as $c ) {
                if ($c->getModuleName() == $newCap['module_name'] && $c->getCapabilityCode() == $newCap['capability_code']) {
                    $found = true;
                    break;
                }
            }
            
            if ($found == false) {
                $uc = new UserCapability();
                $uc->setUserId( $userId );
                $uc->setModuleName( $newCap['module_name'] );
                $uc->setCapabilityCode( $newCap['capability_code'] );
                $uc->save();
            }
        }
        
    }
    
    
    
    public function readResetPassword($resetPasswordId, $securityString=null) {
        $rpDao = object_container_get(ResetPasswordDAO::class);
        
        $r = $rpDao->read( $resetPasswordId, $securityString );
        
        return $r;
    }
    
    
    public function resetPassword($userId) {
        
        $user = $this->readUser( $userId );
        if (!$user) {
            throw new ObjectNotFoundException('User not found');
        }
        
        $rpDao = object_container_get(ResetPasswordDAO::class);
        $cnt = $rpDao->resetPasswordCount( remote_addr(), 60 * 5 );
//         print $cnt;exit;
        if ($cnt > 5) {
            throw new SecurityException('Too many requests');
        }
        
        // can only send a pw request if user has a valid e-mailadres
        if (validate_email( $user->getEmail() ) == false) {
            ActivityUtil::logActivityUser($user->getUserId(), $user->getUsername(), 'password-request', 'Password requested, FAILED: no e-mail set', 'IP: ' . remote_addr());
            return;
        }
        
        // create reset-entry
        $rp = new ResetPassword();
        $rp->setUserId( $user->getUserId() );
        $rp->setUsername( $user->getUsername() );
        $rp->setSecurityString(md5(uniqid()).md5(uniqid()).md5(uniqid()).md5(uniqid()));
        $rp->setRequestIp( remote_addr() );
        $rp->save();
        
        // fetch system identity
        $emailService = object_container_get(EmailService::class);
        $systemIdentity = $emailService->readSystemMessagesIdentity();
        
        // send e-mail
        $email = new Email();
        $email->setStatus(Email::STATUS_SENT);
        $email->setConfidential( true );
        
        $email->setIdentityId( $systemIdentity->getIdentityId() );
        $email->setFromName( $systemIdentity->getFromName() );
        $email->setFromEmail( $systemIdentity->getFromEmail() );
        
        $email->setSubject( t('Password reset requested for') . ' '  . $user->getUsername() );
        $email->setIncoming(false);
        
        $et = new EmailTo();
        $et->setToEmail( $user->getEmail() );
        $email->addRecipient( $et );
        
        // mail
        $mailtpl = module_file('base', 'templates/auth/_reset_password_email-'.ctx()->getSelectedLang().'.php');
        if ($mailtpl == false) {
            $mailtpl = module_file('base', 'templates/auth/_reset_password_email.php');
        }
        $html = get_template( $mailtpl, [
            'reset_password_id' => $rp->getResetPasswordId(),
            'security_string'   => $rp->getSecurityString(),
            'ip'                => $rp->getRequestIp(),
            'username'          => $rp->getUsername()
        ]);
        $email->setTextContent( $html );
        $email->save();
        
        $et->setEmailId( $email->getEmailId() );
        $et->save();
        
        $sm = SendMail::createMail( $email );
        $sm->send();
        
        $longdesc = '';
        if (ctx()->getUser()) {
            $longdesc = 'Requested by ' . ctx()->getUser()->getUsername() . "\n<br/>IP: " . remote_addr();
        }
        else {
            $longdesc = "Requested by an unauthenticated user\n<br/>IP: " . remote_addr();
        }
        
        ActivityUtil::logActivityUser($user->getUserId(), $user->getUsername(), 'password-request', 'Password requested', $longdesc);
    }
    
    
    
    public function readGroup( $groupId, $opts=array() ) {
        $gDao = new UserGroupDAO();
        $g = $gDao->read($groupId);
        
        
        if (!$g) {
            if (isset($opts['null-not-found']) && $opts['null-not-found'])
                return null;
            else
                throw new ObjectNotFoundException( 'UserGroup not found' );
        }
        
        // read permissions
        $ugcDao = new UserGroupCapabilityDAO();
        $ugcs = $ugcDao->readByUserGroup( $groupId );
        
        $g->setCapabilities( $ugcs );
        
        return $g;
    }
    
    public function readAllGroups() {
        $ugDao = new UserGroupDAO();
        
        return $ugDao->readAll();
    }
    
    
    public function readGroupsFlat() {
        $ugDao = new UserGroupDAO();
        $groups = $ugDao->readAll();
        
        $groups = $this->_structureGroups( $groups );
        $groups = $this->_flattenGroups($groups);
        
        return $groups;
    }
    public function mapGroupsFlat() {
        
        if ($this->cache_mapGroupsFlat === null) {
            $m = array();
            
            $groups = $this->readGroupsFlat();
            foreach($groups as $g) {
                $m[ $g->getUserGroupId() ] = $g;
            }
            
            $this->cache_mapGroupsFlat = $m;
        }
        
        return $this->cache_mapGroupsFlat;
    }
    
    public function fullGroupName( $userGroupId ) {
        $m = $this->mapGroupsFlat();
        
        if (isset( $m[$userGroupId] )) {
            $group = $m[$userGroupId];
            
            $parentNames = $group->getField('parentNames');
            $groupPath = $group->getGroupName();
            if (count($parentNames))
                $groupPath = implode(' >> ', $parentNames ) . ' >> ' . $groupPath;
            
            return $groupPath;
        }
        else {
            return 'group-'.$userGroupId;
        }
        
    }
    
    protected function _flattenGroups( $groups, $parentNames = array() ) {
        $r = array();
        
        foreach($groups as $g) {
            $g->setField('parentNames', $parentNames);
            
            $r[] = $g;
            
            if ($g->hasChildren()) {
                $parentNames[] = $g->getGroupName();
                
                $childs = $this->_flattenGroups( $g->getChildren(), $parentNames );
                $r = array_merge($r, $childs);
            }
        }
        
        return $r;
    }
    
    
    public function readGroupsAsTree() {
        $ugDao = new UserGroupDAO();
        $groups = $ugDao->readAll();
        
        $r = $this->_structureGroups( $groups );
        
        return $r;
    }
    protected function _structureGroups( $groups, $parentUserGroupId=0, &$setGroupIds=array() ) {
        
        $l = array();
        
        foreach( $groups as $g ) {
            if ($parentUserGroupId == (int)$g->getParentUserGroupId()) {
                $l[] = $g;
                $setGroupIds[] = $g->getUserGroupId();
                
                $children = $this->_structureGroups( $groups, $g->getUserGroupId(), $setGroupIds );
                
                $g->setChildren( $children );
            }
        }
        
        // add parentless groups
        if ($parentUserGroupId == 0) {
            foreach($groups as $g) {
                if (in_array( $g->getUserGroupId(), $setGroupIds) == false) {
                    $l[] = $g;
                }
            }
        }
        
        
        return $l;
    }
    
    
    public function saveGroup( $form ) {
        $id = $form->getWidgetValue('user_group_id');
        if ($id) {
            $group = $this->readGroup( $id );
        }
        else {
            $group = new UserGroup();
        }
        
        $oldForm = null;
        if ($group->isNew() == false) {
            $oldForm = new UserGroupForm();
            $oldForm->bind( $group );
        }
        
        $form->fill( $group, ['parent_user_group_id', 'group_name'] );
        
        $group->save();
        
        $capWidgets = $form->getWidget('container-permissions')->getWidgets();
        $capabilities = array();
        foreach($capWidgets as $w) {
            if ( $w->getValue() ) {
                $name = $w->getField('module_name');
                $code = $w->getField('capability_code');
                
                if ($name && $code) {
                    $c = new UserGroupCapability();
                    $c->setModuleName($name);
                    $c->setCapabilityCode($code);
                    $c->setUserGroupId( $group->getUserGroupId() );
                    
                    $capabilities[] = $c;
                }
            }
        }
        
        $ugcDao = new UserGroupCapabilityDAO();
        $ugcDao->mergeFormListMTO1( 'user_group_id', $group->getUserGroupId(), $capabilities );
        
        
        if ($oldForm) {
            $desc = FormChangesHtml::formChanged($oldForm, $form)->getHtml();
            ActivityUtil::logActivityRefObject( UserGroup::class, $group->getUserGroupId(), 'user-group-edited', 'User group changed: ' . $group->getGroupname(), $desc);
        }
        else {
            $desc = FormChangesHtml::formNew( $form )->getHtml();
            ActivityUtil::logActivityRefObject( UserGroup::class, $group->getUserGroupId(), 'user-group-created', 'User group created: ' . $group->getGroupname(), $desc);
        }
        
        return $group;
    }
    
    
    
    public function deleteGroup( $groupId ) {
        
        $g = $this->readGroup($groupId);
        
        $form = new UserGroupForm();
        $form->bind( $g );
        
        
        $g->delete();
        
        $ugcDao = new UserGroupCapabilityDAO();
        $ugcDao->deleteByGroup( $g->getUserGroupId() );
        
        $uguDao = new UserGroupUserDAO();
        $uguDao->deleteByGroup( $g->getUserGroupId() );
        
        
        
        $desc = FormChangesHtml::formDeleted( $form )->getHtml();
        ActivityUtil::logActivityRefObject( UserGroup::class, $g->getUserGroupId(), 'user-group-deleted', 'User group deleted: ' . $g->getGroupname(), $desc);
    }
    
    
    public function mapGroupCapabilitiesForUser( $userId ) {
        
        $ugcDao = new UserGroupCapabilityDAO();
        
        $ugcs = $ugcDao->readByUser( $userId );
        
        
        $map = array();
        foreach($ugcs as $ugc) {
            $c = $ugc->getModuleName() . '.' . $ugc->getCapabilityCode();
            $map[$c] = true;
        }
        
        return $map;
    }
    
    
    public function searchUserOrGroup( $q, $opts ) {
        
        if (!$q)
            $q = '';
        
        if (trim($q) == '' && isset($opts['id']) && $opts['id']) {
            
            if ( strpos($opts['id'], 'group-') === 0 ) {
                $groupId = (int)substr($opts['id'], strlen('group-'));
                
                $ugDao = new UserGroupDAO();
                $g = $ugDao->read( $groupId );
                
                if ($g) {
                    $groupPath = $this->fullGroupName( $g->getUserGroupId() );
                    
                    $result = array();
                    $result[] = array(
                        'type'         => t('User group'),
                        'id'           => 'group-'.$g->getUserGroupId(),
                        'name'         => $groupPath,
                        'email'        => '',
                        'fullname'     => '',
                        'default_text' => $groupPath
                    );
                    
                    return $result;
                }
            }
            
            if ( strpos($opts['id'], 'user-') === 0 ) {
                $userId = (int)substr($opts['id'], strlen('user-'));
                
                $userDao = new UserDAO();
                $u = $userDao->read( $userId );
                
                if ($u) {
                    $result = array();
                    $result[] = array(
                        'type'         => t('User'),
                        'id'           => 'user-'.$u->getUserId(),
                        'name'         => $u->getUsername(),
                        'email'        => $u->getEmail(),
                        'fullname'     => trim($u->getFirstname() . ' ' . $u->getLastname()),
                        'default_text' => $u->getUsername()
                    );
                    
                    return $result;
                }
            }

        }
        
        
        
        $result = array();
        
        $ugDao = new UserGroupDAO();
        $groups = $ugDao->searchForSelect($q);
        
        if (count($groups) > 0) {
            $mapGroups = $this->mapGroupsFlat();
            
            
            foreach($groups as $g) {
                
                $groupPath = $this->fullGroupName( $g->getUserGroupId() );
                
                $result[] = array(
                    'type'         => t('User group'),
                    'id'           => 'group-'.$g->getUserGroupId(),
                    'name'         => $groupPath,
                    'email'        => '',
                    'fullname'     => '',
                    'default_text' => $groupPath
                );
            }
        }
        
        
        $uDao = new UserDAO();
        $users = $uDao->searchForSelect($q);
        
        foreach($users as $u) {
            $result[] = array(
                'type'         => t('User'),
                'id'           => 'user-'.$u->getUserId(),
                'name'         => $u->getUsername(),
                'email'        => $u->getEmail(),
                'fullname'     => trim($u->getFirstname() . ' ' . $u->getLastname()),
                'default_text' => $u->getUsername()
            );
        }
        
        return $result;
    }
    
    
    public function getGroupIdsByUser( $userId ) {
        $uguDao = new UserGroupUserDAO();
        $ugus = $uguDao->readByUser($userId);
        
        $ids = array();
        foreach($ugus as $ugu) {
            $ids[] = $ugu->getUserId();
        }
        
        return $ids;
    }
    
    
    
    
}

