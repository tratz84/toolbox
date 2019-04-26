#!/usr/bin/php
<?php

/**
 * fillperson - testdata for person-module
 */

use base\forms\PersonForm;
use base\service\PersonService;
use core\ObjectContainer;

include dirname(__FILE__).'/../bootstrap.php';



$personService = ObjectContainer::getInstance()->get(PersonService::class);

for($x=0; $x < 1000; $x++) {
    print "Adding person no $x\n";

    $req = array();
    $req['firstname'] = generateName();
    $req['lastname'] = generateName();

    $req['addressList'] = array();
    $req['addressList'][] = array(
        'person_address_id' => '',
        'address_id' => '',
        'street' => generateName(),
        'street_no' => rand(0, 999),
        'zipcode' => rand(1111, 9999) . strtoupper( randA2Z() . randA2Z() ),
        'city' => '',
        'country_id' => 148,
        'note' => ''
    );

    $req['emailList'] = array();
    $req['emailList'][] = array(
        'email_id' => '',
        'person_email_id' => '',
        'email_address' => strtolower(slugify($req['firstname']).'.'.slugify($req['lastname'])) . '-dev@itxplain.nl',
        'note' => ''
    );

    $req['phoneList'] = array();
    $req['phoneList'][] = array(
        'phone_id' => '',
        'person_phone_id' => '',
        'phonenr' => '072 ' . rand(11111, 99999),
        'note' => ''
    );

    $f = new PersonForm();

    $f->bind($req);

    $personService->save($f);

}
