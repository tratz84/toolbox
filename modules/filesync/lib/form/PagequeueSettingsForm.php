<?php


namespace filesync\form;


use core\forms\BaseForm;
use core\forms\SelectField;
use filesync\service\StoreService;

class PagequeueSettingsForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new SelectField('default_rotation', '', array('0' => '0 graden', '90' => '90 graden', '180' => '180 graden', '270' => '270 graden'), 'Standaard rotatie'));
        $this->getWidget('default_rotation')->setInfoText('Standaard rotatie nieuw geuploade afbeeldingen');
        
        $storeService = object_container_get(StoreService::class);
        $archiveStores = $storeService->readArchiveStores();
        $mapStores = array();
        foreach($archiveStores as $as) {
            $mapStores[$as->getStoreId()] = $as->getStoreName();
        }
        $this->addWidget(new SelectField('archive_store', '', $mapStores, 'Store'));
        $this->getWidget('archive_store')->setInfoText('Locatie waar nieuw gegenereerde PDF\'s worden opgeslagen');
        
        
    }
}