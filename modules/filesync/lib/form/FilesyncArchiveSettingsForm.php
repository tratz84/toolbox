<?php

namespace filesync\form;

use core\forms\BaseForm;
use core\forms\SelectField;
use core\forms\HiddenField;
use filesync\service\StoreService;


class FilesyncArchiveSettingsForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->addWidget(new HiddenField('filesync_archive_settings', '1'));
        
        $this->addArchiveStores();
        
    }
    
    
    protected function addArchiveStores() {
        $storeService = object_container_get(StoreService::class);
        $archiveStores = $storeService->readArchiveStores();
        
        $map = array();
        
        foreach($archiveStores as $as) {
            $map[$as->getStoreId()] = $as->getStoreName();
        }
        
        $this->addWidget(new SelectField('store_id', '', $map, 'Default archive store'));
        
    }
    
}

