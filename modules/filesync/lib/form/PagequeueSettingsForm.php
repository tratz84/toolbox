<?php


namespace filesync\form;


use core\forms\BaseForm;
use core\forms\SelectField;
use filesync\service\StoreService;
use core\forms\HtmlField;
use core\forms\CheckboxField;
use core\forms\NumberField;

class PagequeueSettingsForm extends BaseForm {
    
    
    public function __construct() {
        parent::__construct();
        
        
        $this->addWidget(new HtmlField('lbl-filesync-generic-settings', '', t('Generic')));
        $this->addWidget(new CheckboxField('libreoffice_previews', '', t('LibreOffice previews')));
        
        $this->addWidget(new HtmlField('lbl-pagequeue-settings', '', t('Pagequeue settings')));
        $this->addWidget(new SelectField('default_rotation', '', array('0' => '0 graden', '90' => '90 graden', '180' => '180 graden', '270' => '270 graden'), t('Default rotation')));
        $this->getWidget('default_rotation')->setInfoText(t('Default rotation new images'));
        
        $storeService = object_container_get(StoreService::class);
        $archiveStores = $storeService->readArchiveStores();
        $mapStores = array();
        foreach($archiveStores as $as) {
            $mapStores[$as->getStoreId()] = $as->getStoreName();
        }
        $this->addWidget(new SelectField('archive_store', '', $mapStores, 'Store'));
        $this->getWidget('archive_store')->setInfoText(t('Location where newly generated PDF-files are saved'));

        
        $this->addWidget(new HtmlField('lbl-wopi-settings', '', t('WOPI settings')));
        $this->addWidget(new CheckboxField('wopi_active', '', t('Active')));
        
        $nf_access_token_ttl = new NumberField('wopi_access_token_ttl', '', 'Access token ttl');
        $nf_access_token_ttl->setMin(0);
        $nf_access_token_ttl->setInfoText('Access token time-to-live, in minutes');
        $this->addWidget($nf_access_token_ttl);
        
        
    }
}