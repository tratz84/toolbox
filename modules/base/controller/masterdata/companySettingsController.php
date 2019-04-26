<?php



use base\service\SettingsService;
use core\controller\BaseController;

class companySettingsController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
    }
    
    public function action_index() {

        $settingsService = $this->oc->get(SettingsService::class);
        $this->settings = $settingsService->settingsAsMap();
        
        if (is_post()) {
            
            $settingsService->updateValue('companyName',      get_var('companyName'));
            $settingsService->updateValue('companyStreet',    get_var('companyStreet'));
            $settingsService->updateValue('companyZipcode',   get_var('companyZipcode'));
            $settingsService->updateValue('companyCity',      get_var('companyCity'));
            $settingsService->updateValue('companyCocNumber', get_var('companyCocNumber'));
            $settingsService->updateValue('companyIBAN',      get_var('companyIBAN'));
            $settingsService->updateValue('companyVat',       get_var('companyVat'));
            $settingsService->updateValue('companyPhone',     get_var('companyPhone'));
            $settingsService->updateValue('companyEmail',     get_var('companyEmail'));
            $settingsService->updateValue('prefixNumbers',    get_var('prefixNumbers'));
            
            
            if (has_file('logoFile')) {
                $oldLogoFile = $this->ctx->getSetting('logoFile');
                
                // save logo
                $newLogoFile = save_upload_to('logoFile', 'logo/');
                if ($newLogoFile != $oldLogoFile) {
                    if ($oldLogoFile)
                        delete_data_file($oldLogoFile);
                        
                        $settingsService->updateValue('logoFile', $newLogoFile);
                }
            }
            
            
            redirect('/?m=base&c=masterdata/index');
        }
        
        
        $this->render();
    }
    
}
