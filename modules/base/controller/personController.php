<?php


use base\forms\PersonForm;
use base\model\Address;
use base\model\Email;
use base\model\Person;
use base\model\Phone;
use base\service\PersonService;
use core\Context;
use core\container\ActionContainer;
use core\controller\BaseController;
use core\event\ActionValidationEvent;
use core\event\EventBus;
use core\exception\InvalidStateException;
use core\exception\ObjectNotFoundException;

class personController extends BaseController {


    public function init() {
        if (Context::getInstance()->isPersonsEnabled() == false)
            throw new InvalidStateException('Person-module not activated');
    }

    public function action_index() {

        $this->render();
    }

    public function action_search() {
        $pageNo = isset($_REQUEST['pageNo']) ? (int)$_REQUEST['pageNo'] : 0;
        $limit = $this->ctx->getPageSize();

        $personService = $this->oc->get(PersonService::class);

        $r = $personService->search($pageNo*$limit, $limit, $_REQUEST);

        $arr = array();
        $arr['listResponse'] = $r;


        $this->json($arr);
    }


    public function action_edit() {
        $id = isset($_REQUEST['person_id'])?(int)$_REQUEST['person_id']:0;

        $personService = $this->oc->get(PersonService::class);
        if ($id) {
            $person = $personService->readPerson($id);

            if ($person == null || $person->getDeleted()) {
                return $this->renderError('Person not found'.($person&&$person->getDeleted()?' (deleted)':''));
            }
        } else {
            $person = new Person();
        }

        $personForm = $this->oc->create(PersonForm::class);
        $personForm->bind($person);

        if (is_post()) {

            $personForm->bind($_REQUEST);


            if ($personForm->validate()) {
                $personService->save($personForm);

                redirect('/?m=base&c=person');
            }

        }



        $this->isNew = $person->isNew();
        $this->person = $person;
        $this->form = $personForm;


        $this->actionContainer = new ActionContainer('person', $person->getPersonId());
        hook_eventbus_publish($this->actionContainer, 'person', 'person-edit');
        
        $this->render();
    }


    public function action_delete() {
        $id = isset($_REQUEST['person_id'])?(int)$_REQUEST['person_id']:0;

        /**
         * @var PersonService $personService
         */
        $personService = $this->oc->get(PersonService::class);
        $person = $personService->readPerson($id);

        /**
         * @var EventBus $eventBus
         */
        $eventBus = $this->oc->get(EventBus::class);
        $evt = $eventBus->publish(new ActionValidationEvent($person, 'base', 'person-delete'));

        if ($evt->hasErrors()) {
            report_user_error($evt->getErrors());
        } else {
            $personService->delete($id);
        }


        redirect('/?m=base&c=person');
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
