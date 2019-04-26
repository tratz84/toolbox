<?php



namespace admin\forms;


use admin\model\User;
use admin\service\AdminCustomerService;
use admin\service\AdminUserService;
use core\ObjectContainer;
use core\forms\BaseForm;
use core\forms\CheckboxField;
use core\forms\HiddenField;
use core\forms\HtmlDatetimeField;
use core\forms\HtmlField;
use core\forms\PasswordField;
use core\forms\SelectField;
use core\forms\TextField;
use core\forms\WidgetContainer;
use core\forms\validator\NotEmptyValidator;

class AdminUserForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget( new HiddenField('user_id', '', 'Id') );
        
        $this->addWidget( new TextField('username', '', 'Gebruikersnaam') );
        
        $this->addWidget( new PasswordField('password', '', 'Wachtwoord') );
        
        $this->addWidget( new SelectField('user_type', '', array('admin' => 'Administrator', 'manager' => 'Manager'), 'Gebruikerstype') );
        
        $this->addCustomerSelection();
        
        $this->addWidget( new HtmlDatetimeField('edited', '', 'Laatst bewerkt', array('hide-when-invalid' => true)) );
        $this->addWidget( new HtmlDatetimeField('created', '', 'Aangemaakt op', array('hide-when-invalid' => true)) );
        
        $this->addValidator('username', new NotEmptyValidator());
    }
    
    
    public function bind($obj) {
        parent::bind($obj);
        
        
        if (is_a($obj, User::class)) {
            $user_id = $this->getWidgetValue('user_id');
            
            if ($user_id) {
                $userService = ObjectContainer::getInstance()->get(AdminUserService::class);
                $user = $userService->readUser($user_id);
                
                foreach($user->getCustomers() as $c) {
                    $widget = $this->getWidget('admin_customer_'.$c->getCustomerId());
                    if ($widget)
                        $widget->setValue(true);
                }
            }
        }
    }
    
    
    
    protected function addCustomerSelection() {
        $customerService = ObjectContainer::getInstance()->get(AdminCustomerService::class);
        $customers = $customerService->readCustomers();
        
        $wc = new WidgetContainer();
        $wc->setName('insights-customers');
        
        $wc->addWidget(new HtmlField('', '', 'Klanten'));
        
        foreach($customers as $c) {
            $w = new CheckboxField( 'admin_customer_' . $c->getCustomerId(), '', $c->getContextName() );
            $w->setField('customerId', $c->getCustomerId());
            $w->setField('contextName', $c->getContextName());
            $wc->addWidget($w);
        }
        
        $this->addWidget($wc);
    }
    
    
}
