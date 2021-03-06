<?php



use base\service\SettingsService;
use core\controller\BaseController;
use core\forms\CheckboxField;
use core\forms\SelectField;
use core\exception\InvalidStateException;

class settingsController extends BaseController {
    
    public function init() {
        checkCapability('base', 'edit-masterdata');
        
        $this->addTitle(t('Master data'));
        $this->addTitle(t('Basic settings'));
    }
    
    public function action_index() {
        
        $settingsService = $this->oc->get(SettingsService::class);
        $this->settings = $settingsService->settingsAsMap();
        
        $this->availableModules = $settingsService->getModuleList();
        
        usort($this->availableModules, function($m1, $m2) {
            // sort by prio
            $p = ($m1->getPrio() - $m2->getPrio());
            if ($p != 0) {
                return $p;
            }
            
            // sort by name
            return strcmp($m1->getName(), $m2->getName());
        });
        
        if (is_post()) {
            
            $pageSize = (int)$_REQUEST['PAGE_SIZE'];
            if ($pageSize >= 10 && $_REQUEST['PAGE_SIZE'] < 100)
                $settingsService->updateValue('PAGE_SIZE', $pageSize);

            // webmail-module required for password resets
            if (ctx()->isModuleEnabled('webmail')) {
                $settingsService->updateValue('reset_password', get_var('reset_password')?1:0);
            }
            
            $settingsService->updateValue('system_language', get_var('system_language'));
            $settingsService->updateValue('progressive_web_app_features', get_var('progressive_web_app_features')?1:0);
            $settingsService->updateValue('object_locking', get_var('object_locking')?1:0);
            $settingsService->updateValue('customers_split', get_var('customers_split')?1:0);
            $settingsService->updateValue('pdf_print_date_footer', get_var('pdf_print_date_footer')?1:0);
            $settingsService->updateValue('pdf_print_paging', get_var('pdf_print_paging', 'always'));
            
            $settingsService->updateValue('master_base_color', get_var('master_base_color'));
            
            report_user_message(t('Changes saved'));
            redirect('/?m=base&c=masterdata/settings');
        }
        
        // webmail-module required for password resets
        if (ctx()->isModuleEnabled('webmail')) {
            $this->checkboxResetPassword = new CheckboxField('reset_password', ctx()->isResetPasswordEnabled()?'1':'0', t('Reset password'));
            $this->checkboxResetPassword->setInfoText(t('Reset password support on login-page'));
        }
        
        $mapLang = array();
        $mapLang['nl_NL'] = 'Nederlands';
        $mapLang['en_US'] = 'English';
        $this->selectLanguage = new SelectField('system_language', @$this->settings['system_language'], $mapLang, t('System language'));
        
        $this->checkboxPwa = new CheckboxField('progressive_web_app_features', @$this->settings['progressive_web_app_features']?'1':'0', t('Progressive web app'));
        $this->checkboxPwa->setInfoText(t('Enable "Progressive Web App" features? (install app)'));
        
        $this->checkboxObjectLocking = new CheckboxField('object_locking', @$this->settings['object_locking']?'1':'0', t('Object locking'));
        $this->checkboxObjectLocking->setInfoText(t('Possibility to mark objects as "locked"'));

        $this->checkboxSplitCustomers = new CheckboxField('customers_split', ctx()->isCustomersSplit(), t('Split customers'));
        $this->checkboxSplitCustomers->setInfoText(t('Split customers into persons/companies?'));
        
        $this->checkboxDateOnPdf = new CheckboxField('pdf_print_date_footer', ctx()->pdfPrintDateFooter(), t('PDF: Print date in footer'));
        $this->checkboxDateOnPdf->setInfoText(t('Put date in footer of generated PDF files?'));
        
        $options = ['always' => t('Always')
                    , 'never' => t('Never')
                    , 'multi-page' => t('Multi-page documents')
                ];
        $this->selectPdfPrintPaging = new SelectField('pdf_print_paging', ctx()->pdfPrintPaging(), $options, t('PDF: paging'));
        $this->selectPdfPrintPaging->setInfoText(t('Print paging in PDF files'));
        
        $this->render();
    }
    
    
    protected function lookupModuleName() {
        $mod = get_var('mod');
        $tag = null;
        
        $settingsService = $this->oc->get(SettingsService::class);
        $availableModules = $settingsService->getModuleList();
        
        foreach($availableModules as $m) {
            if ($m->getTag() == $mod) {
                $tag = $m->getTag();
            }
        }
        
        if ($tag == null) {
            throw new InvalidStateException('Module not found');
        }
        
        return $tag;
    }
    
    public function action_activate_module() {
        $mod = $this->lookupModuleName();
        
        if (strpos($mod, 'Module') !== false)
            $moduleName = substr($mod, 0, strrpos($mod, 'Module'));
        else
            $moduleName = $mod;
        
        // activation script?
        $activationFile = module_file($moduleName, 'activate.php');
        if ($activationFile) {
            include $activationFile;
        }
        
        // update database
        \core\db\mysql\MysqlTableGenerator::updateModule( $moduleName );
        
        
        // mark as enabled
        $settingsService = $this->oc->get(SettingsService::class);
        $settingsService->updateValue( $mod.'Enabled', 1, ['type' => 'mod-activation'] );
    
        report_user_message(tf('Module "%s" enabled', $mod));
        
        redirect('/?m=base&c=masterdata/settings');
    }
    
    public function action_deactivate_module() {
        $mod = $this->lookupModuleName();
        
        $moduleName = $mod;
        
        if (strrpos($moduleName, 'Module') !== false) {
            $moduleName = substr($moduleName, 0, strrpos($moduleName, 'Module'));
        }
        
        // don't
        if ($moduleName == 'base') {
            report_user_error('Unable to de-activate base module');
            redirect('/?m=base&c=masterdata/settings');
        }
        
        // check dependencies
        $mam = meta_active_modules();
        if (isset($mam[ $mod ])) {
            foreach($mam as $m_name => $m) {
                // current module? => skip
                if ($m_name == $mod) {
                    continue;
                }
                
                // TODO: deprecated.. remove
                $m = is_array($m) ? $m[0] : $m;
                
                if (in_array($mod, $m->getDependencies())) {
                    report_user_error(t('Unable to disable module, required by') . ': ' . $m_name);
                    redirect('/?m=base&c=masterdata/settings');
                }
            }
        }
        
        
        // de-activation script?
        $deactivationFile = module_file($moduleName, 'deactivate.php');
        if ($deactivationFile) {
            include $deactivationFile;
        }
        
        // mark as disabled
        $settingsService = $this->oc->get(SettingsService::class);
        $settingsService->updateValue($mod.'Enabled', 0);
        
        report_user_message(tf('Module "%s" disabled', $mod));
   
        redirect('/?m=base&c=masterdata/settings');
    }
    
    
    
}