<?php


use base\forms\PersonForm;
use base\model\Address;
use base\model\Email;
use base\model\Person;
use base\model\Phone;
use base\service\PersonService;
use core\Context;
use core\container\ActionContainer;
use core\controller\FormController;
use core\event\ActionValidationEvent;
use core\event\EventBus;
use core\exception\InvalidStateException;

class personController extends FormController {


    public function init() {
        if (Context::getInstance()->isPersonsEnabled() == false)
            throw new InvalidStateException('Person-module not activated');
        
        $this->varNameId = 'person_id';
        
        $this->formClass = PersonForm::class;
        $this->objectClass = Person::class;
        
        $this->serviceClass = PersonService::class;
        $this->serviceFuncSearch = 'search';
        $this->serviceFuncRead   = 'readPerson';
        $this->serviceFuncSave   = 'save';
        $this->serviceFuncDelete = 'delete';
        
        $this->addTitle(t('Persons'));
    }

    
    
    public function action_edit($opts=array()) {
        $r = parent::action_edit(array(
            'render' => false,
            'return_on_object_deleted' => 'not_found'
        ));
        
        $this->person = $this->object;
        
        if ($r == 'not_found') {
            $person = $this->object;
            return $this->renderError('Person not found'.($person&&$person->getDeleted()?' (deleted)':''));
        }
        
        if ($this->person->getPersonId()) {
            $this->addTitle( $this->person->getFullname() );
        } else {
            $this->addTitle( t('New person') );
        }
        
        $this->render();
    }



    public function action_widget() {

        if (isset($this->personForm)) {
            $this->person = new Person();
            $this->personForm->fill($this->person, array('firstname', 'insert_lastname', 'lastname', 'note', 'iban', 'bic'));
            $this->person->setAddressList( $this->personForm->getWidget('addressList')->asObjects( Address::class) );
            $this->person->setEmailList( $this->personForm->getWidget('emailList')->asObjects( Email::class) );
            $this->person->setPhoneList( $this->personForm->getWidget('phoneList')->asObjects( Phone::class) );
            
        } else {
            $personService = $this->oc->get(PersonService::class);
            $this->person = $personService->readPerson($this->person_id);
        }

        $this->render();
    }

}
