#!/usr/bin/php
<?php

/**
 * fillcompany - testdata for company-module
 */

use core\ObjectContainer;
use customer\service\CompanyService;
use customer\forms\CompanyForm;

include dirname(__FILE__).'/../bootstrap.php';



$companyService = ObjectContainer::getInstance()->get(CompanyService::class);

for($x=0; $x < 1000; $x++) {
    print "Adding company no $x\n";

    $req = array();
    $req['company_name'] = generateName();
    $req['contact_person'] = substr(generateName(), 0, 3) . ' ' . generateName();
    $req['coc_number'] = rand(1111111, 9999999);
    $req['addressList'] = array();
    $req['addressList'][] = array(
        'address_id' => '',
        'company_address_id' => '',
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
        'company_email_id' => '',
        'email_address' => strtolower(slugify($req['contact_person'])) . '-dev@itxplain.nl',
        'note' => ''
    );

    $req['phoneList'] = array();
    $req['phoneList'][] = array(
        'phone_id' => '',
        'company_phone_id' => '',
        'phonenr' => '072 ' . rand(11111, 99999),
        'note' => ''
    );

    $f = new CompanyForm();

    $f->bind($req);

    $companyService->save($f);

}
