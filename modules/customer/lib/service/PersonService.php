<?php


namespace customer\service;

use base\forms\FormChangesHtml;
use customer\forms\PersonForm;
use customer\model\AddressDAO;
use customer\model\CompanyDAO;
use customer\model\CompanyPersonDAO;
use customer\model\EmailDAO;
use base\model\ObjectMetaDAO;
use customer\model\Person;
use customer\model\PersonDAO;
use customer\model\PhoneDAO;
use base\util\ActivityUtil;
use core\forms\lists\ListResponse;
use core\service\ServiceBase;
use core\service\FormDbHandler;

class PersonService extends ServiceBase {
    
    
    public function readPerson($id, $opts=array()) {
        // read customer__person-record only?
        if (isset($opts['record-only']) && $opts['record-only']) {
            $pDao = object_container_get(PersonDAO::class);
            return $pDao->read( $id );
        }
        
        $fh = FormDbHandler::getHandler(PersonForm::class);
        
        return $fh->readObject( $id );
    }
    
    public function save(\customer\forms\PersonForm $personForm) {
        
//         sleep( 10 );
        
        $fh = FormDbHandler::getHandler( PersonForm::class );
        
        $obj = $fh->saveForm( $personForm );
        
        return $obj->getPersonId();
    }
    
    
    public function search($start, $limit, $opts = array()) {
        $fh = FormDbHandler::getHandler( PersonForm::class );
        
        return $fh->search($start, $limit, $opts);
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

