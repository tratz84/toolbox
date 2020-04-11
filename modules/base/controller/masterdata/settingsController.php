<?php



use base\service\SettingsService;
use core\controller\BaseController;
use core\forms\CheckboxField;
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
                return ceil( $p );
            }
            
            // sort by name
            return strcmp($m1->getName(), $m2->getName()) * -1;
        });
        
        if (is_post()) {
            
            $pageSize = (int)$_REQUEST['PAGE_SIZE'];
            if ($pageSize >= 10 && $_REQUEST['PAGE_SIZE'] < 100)
                $settingsService->updateValue('PAGE_SIZE', $pageSize);

            $settingsService->updateValue('object_locking', get_var('object_locking')?1:0);
            $settingsService->updateValue('customers_split', get_var('customer_split')?1:0);
            $settingsService->updateValue('master_base_color', get_var('master_base_color'));
            
                
            redirect('/?m=base&c=masterdata/index');
        }
        
        $this->checkboxObjectLocking = new CheckboxField('object_locking', @$this->settings['object_locking']?'1':'0', 'Object locking');
        $this->checkboxObjectLocking->setInfoText(t('Possibility to mark objects as "locked"'));

        $this->checkboxSplitCustomers = new CheckboxField('customers_split', ctx()->isCustomersSplit(), 'Split customers');
        $this->checkboxSplitCustomers->setInfoText(t('Split customers into persons/companies?'));
        
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
        $settingsService->updateValue($mod.'Enabled', 1);
    
        report_user_message('Module "'.$mod.'" enabled');
        
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
        
        // de-activation script?
        $deactivationFile = module_file($moduleName, 'deactivate.php');
        if ($deactivationFile) {
            include $deactivationFile;
        }
        
        // mark as disabled
        $settingsService = $this->oc->get(SettingsService::class);
        $settingsService->updateValue($mod.'Enabled', 0);
        
        report_user_message('Module "'.$mod.'" disabled');
   
        redirect('/?m=base&c=masterdata/settings');
    }
    
    
    
}