<?php



use base\model\Menu;

hook_eventbus_subscribe('base', 'MenuService::listMainMenu', function($src) {
    if (ctx()->isModuleEnabled('customer')) {
        if (ctx()->isCustomersSplit()) {
            $menuCompany = new Menu();
            $menuCompany->setIconLabelUrl('fa-user', t('Companies'), '/?m=customer&c=company');
            $menuCompany->setWeight(20);
            $menuCompany->setMenuCode('company');
            $src->add($menuCompany);
            
            $menuPerson = new Menu();
            $menuPerson->setIconLabelUrl('fa-user', t('Persons'), '/?m=customer&c=person');
            $menuPerson->setWeight(21);
            $menuPerson->setMenuCode('person');
            $src->add($menuPerson);
        }
        else {
            $menuCustomers = new Menu();
            $menuCustomers->setIconLabelUrl('fa-user', t('Customers'), '/?m=customer&c=customer');
            $menuCustomers->setWeight(20);
            $src->add( $menuCustomers );
        }
    }
});
    
    

