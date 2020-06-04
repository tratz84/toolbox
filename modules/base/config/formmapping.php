<?php



use base\forms\CompanyForm;
use base\model\AddressDAO;
use base\model\Company;
use base\model\CompanyAddressDAO;
use base\model\CompanyDAO;
use base\model\CompanyEmailDAO;
use base\model\CompanyPersonDAO;
use base\model\CompanyPhoneDAO;
use base\model\EmailDAO;
use base\model\PersonDAO;
use base\model\PhoneDAO;
use core\service\FormDbMapper;

$mapping = array();

$fdm = new FormDbMapper( \base\forms\CompanyForm::class, CompanyDAO::class );
$fdm->setLogCreatedCode('company-created');
$fdm->getLogCreatedText('Bedrijf aangemaakt');
$fdm->setLogUpdatedCode('company-edited');
$fdm->setLogUpdatedText('Bedrijf aangepast');
$fdm->setLogDeletedCode('company-deleted');
$fdm->setLogDeletedText('Bedrijf verwijderd');

$fdm->addMTON(CompanyAddressDAO::class, AddressDAO::class, 'addressList');
$fdm->addMTON(CompanyEmailDAO::class,   EmailDAO::class,   'emailList');
$fdm->addMTON(CompanyPhoneDAO::class,   PhoneDAO::class,   'phoneList');
$fdm->addMTON(CompanyPersonDAO::class,  PersonDAO::class,  'personList');
$mapping[ CompanyForm::class ] = $fdm;
$mapping[ Company::class ] = $fdm;

return $mapping;

