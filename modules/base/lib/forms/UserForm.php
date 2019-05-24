<?php



namespace base\forms;


use base\model\User;
use base\service\UserService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\EmailField;
use core\forms\HiddenField;
use core\forms\HtmlDatetimeField;
use core\forms\HtmlField;
use core\forms\PasswordField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\WidgetContainer;
use core\forms\validator\EmailValidator;
use core\forms\validator\NotEmptyValidator;

class UserForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addKeyField('user_id');
        
        $this->addWidget( new HiddenField('user_id', '', 'Id') );
        
        $this->addWidget( new TextField('username', '', t('Username')) );
        
        $this->addWidget( new PasswordField('password', '', t('Password')) );
        
        $this->addWidget( new SelectField('user_type', '', array('admin' => 'Administrator', 'user' => t('User')), t('Usertype')) );
        
        $this->addUserCapabilities();
        
        $this->addWidget( new EmailField('email', '', t('Email')) );
        $this->addWidget( new TextField('firstname', '', t('Firstname')) );
        $this->addWidget( new TextField('lastname', '', t('Lastname')) );
        
        
        $this->addWidget( new HtmlDatetimeField('edited', '', t('Last modified'), array('hide-when-invalid' => true)) );
        $this->addWidget( new HtmlDatetimeField('created', '', t('Created on'), array('hide-when-invalid' => true)) );
        
        $this->addWidget( new ListUserIpLineWidget('ips') );
        $this->getWidget('ips')->setInfoText('Indien hier ip-adressen staan ingevuld, mag de gebruiker alleen vanaf deze adressen zich aanmelden.');
        
        
        $this->addValidator('username', new NotEmptyValidator());
        $this->addValidator('email', new EmailValidator(array('empty-allowed' => true)));
        
        $this->addValidator('username', function($form) {
            $userId = $form->getWidgetValue('user_id');
            $username = $form->getWidgetValue('username');
            
            $userService = ObjectContainer::getInstance()->get(UserService::class);
            $user = $userService->readByUsername($username);
            if ($user != null && $user->getUserId() != $userId) {
                return t('Username in use');
            }
            
            return null;
        });
        
    }
    
    public function bind($obj) {
        parent::bind($obj);
        
        
        if (is_a($obj, User::class)) {
            $user_id = $this->getWidgetValue('user_id');
            
            if ($user_id) {
                $userService = ObjectContainer::getInstance()->get(UserService::class);
                $user = $userService->readUser($user_id);
                
                foreach($user->getCapabilities() as $c) {
                    $widget = $this->getWidget('capability_'.$c->getCapabilityCode());
                    if ($widget)
                        $widget->setValue(true);
                }
            }
        }
    }
    
    
    
    protected function addUserCapabilities() {
        $userService = ObjectContainer::getInstance()->get(UserService::class);
        $capabilities = $userService->getCapabilities();
        
        $wc = new WidgetContainer();
        $wc->setName('user-capabilities');
        
        $wc->addWidget(new HtmlField('', '', 'Permissies'));
        
        foreach($capabilities as $c) {
            $w = new CheckboxField('capability_' . $c['capability_code'], '', t('modulename.'.$c['module_name']) . ' - ' . $c['short_description']);
            $w->setInfoText($c['infotext']);
            $w->setField('module_name', $c['module_name']);
            $w->setField('capability_code', $c['capability_code']);
            
            $wc->addWidget($w);
        }
        
        $this->addWidget($wc);
    }
    
}
