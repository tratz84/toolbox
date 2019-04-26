<?php


namespace base\service;

use base\model\AddressDAO;
use base\model\EmailDAO;
use base\model\Person;
use base\model\PersonDAO;
use base\model\PhoneDAO;
use base\util\ActivityUtil;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use base\model\ObjectMetaDAO;
use base\forms\FormChangesHtml;
use base\forms\PersonForm;

class PersonService extends ServiceBase {
    
    
    public function readPerson($id) {
        
        $person = new Person($id);
        if ($person->read() == false)
            return false;
        
//         if ($person->getDeleted())
//             return false;
        
        $addressDao = new AddressDAO();
        $addresses = $addressDao->readByPerson($id);
        $person->setAddressList($addresses);
        
        $emailDao = new EmailDAO();
        $emails = $emailDao->readByPerson($id);
        $person->setEmailList($emails);
        
        $phoneDao = new PhoneDAO();
        $phones = $phoneDao->readByPerson($id);
        $person->setPhoneList($phones);
        
        return $person;
    }
    
    public function save(\base\forms\PersonForm $personForm) {
        $personId = $personForm->getWidgetValue('person_id');
        if ($personId) {
            $person = $this->readPerson($personId);
        } else {
            $person = new Person();
        }
        
        $isNew = $person->isNew();
        
        if ($isNew) {
            $fch = FormChangesHtml::formNew($personForm);
        } else {
            $oldForm = PersonForm::createAndBind($person);
            $fch = FormChangesHtml::formChanged($oldForm, $personForm);
        }
        
        $personForm->fill($person, array('firstname', 'insert_lastname', 'lastname', 'note', 'iban', 'bic'));
        
        if (!$person->save()) {
            // exception would also be on it's place
            return false;
        }
        
        $personForm->getWidget('person_id')->setValue($person->getPersonId());
        
        $addressDao = new AddressDAO();
        $newAddresses = $personForm->getWidget('addressList')->asArray();
        $addressDao->mergeFormListMTON('customer__person_address', 'person_id', $person->getPersonId(), $newAddresses, 'sort');
        
        
        $emailDao = new EmailDAO();
        $newEmails = $personForm->getWidget('emailList')->asArray();
        $emailDao->mergeFormListMTON('customer__person_email', 'person_id', $person->getPersonId(), $newEmails, 'sort');
        
        $phoneDao = new PhoneDAO();
        $newPhones = $personForm->getWidget('phoneList')->asArray();
        $phoneDao->mergeFormListMTON('customer__person_phone', 'person_id', $person->getPersonId(), $newPhones, 'sort');
        
        
        if ($isNew) {
            ActivityUtil::logActivityPerson($person->getPersonId(), 'customer__person', null, 'person-created', 'Persoon aangemaakt', $fch->getHtml());
        } else {
            ActivityUtil::logActivityPerson($person->getPersonId(), 'customer__person', null, 'person-edited', 'Persoon aangepast', $fch->getHtml());
        }
        
        return $person->getPersonId();
    }
    
    
    public function search($start, $limit, $opts = array()) {
        $pDao = new PersonDAO();
        
        $cursor = $pDao->search($opts);
        
        $r = ListResponse::fillByCursor($start, $limit, $cursor, array('person_id', 'firstname', 'insert_lastname', 'lastname', 'fullname', 'note', 'edited', 'created'));
        
        return $r;
    }
    
    
    public function delete($personId) {
        // TODO: check (active) contracts
        
        
        // addresses
//         $aDao = new AddressDAO();
//         $aDao->deleteMTON('customer__person_address', 'person_id', $personId);
        
//         // phone
//         $pDao = new PhoneDAO();
//         $pDao->deleteMTON('customer__person_phone', 'person_id', $personId);
        
//         // mail
//         $eDao = new EmailDAO();
//         $eDao->deleteMTON('customer__person_email', 'person_id', $personId);
        
//         $pDao = new PersonDAO();
//         $pDao->delete( $personId );

        // delete meta
        $omDao = new ObjectMetaDAO();
        $omDao->deleteByObject(Person::class, $personId);
        $omDao->deleteByObject('person', $personId);            // deprecated ?
        
        // delete person
        $personDao = $this->oc->get(PersonDAO::class);
        $personDao->delete($personId);
        
        
        ActivityUtil::logActivityPerson($personId, 'customer__person', null, 'person-deleted', 'Persoon verwijderd');
        
        return true;
    }
    
    
    
    public function getFullname($id) {
        $p = $this->readPerson($id);
        
        if ($p) {
            return $p->getFullname();
        } else {
            return null;
        }
    }
    
}

