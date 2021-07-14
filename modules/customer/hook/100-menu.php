<?php



use base\model\Menu;

hook_eventbus_subscribe('base', 'MenuService::listMainMenu', function($src) {
    if (ctx()->isModuleEnabled('customer')) {
        if (ctx()->isCustomersSplit()) {
            $menuCompany = new Menu();
            $menuCompany->setIconLabelUrl('fa-user', t('Companies'), '/?m=customer&c=company', 200);
            $menuCompany->setMenuCode('company');
            $src->add($menuCompany);
            
            $menuPerson = new Menu();
            $menuPerson->setIconLabelUrl('fa-user', t('Persons'), '/?m=customer&c=person', 300);
            $menuPerson->setMenuCode('person');
            $src->add($menuPerson);
        }
        else {
            $menuPerson = new Menu();
            $menuPerson->setIconLabelUrl('fa-user', t('Persons'), '/?m=customer&c=person', 300);
            $src->add($menuPerson);
        }
    }
});
    
    

