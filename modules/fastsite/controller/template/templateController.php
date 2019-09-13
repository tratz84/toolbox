<?php


use core\controller\BaseController;
use core\exception\FileException;
use core\exception\InvalidStateException;
use fastsite\data\FastsiteSettings;
use fastsite\form\TemplateSettingsForm;
use fastsite\form\WebsiteTemplateForm;
use fastsite\model\TemplateSetting;
use fastsite\service\FastsiteSettingsService;
use fastsite\service\WebsiteTemplateService;

class templateController extends BaseController {
    
    
    public function action_index() {
        
        $wtService = $this->oc->get( WebsiteTemplateService::class );

        $this->templates = $wtService->getTemplates();
        
        
        return $this->render();
    }
    
    
    public function action_add() {
        $this->form = new WebsiteTemplateForm();
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            
            $path = get_data_file('fastsite/templates');
            if ($path == false) {
                if (!mkdir(get_data_file('.').'/fastsite/templates', 0755, true)) {
//                     throw new InvalidStateException('Unable to create fastsite/templates-directory in DATA_DIR');
                    throw new FileException('Unable to create template directory');
                }
            }
            
            if (has_file('file')) {
                $p = save_upload_to('file', 'fastsite/templates');
                $f = get_data_file($p);
                
                if ($p && $f) {
                    $zip = new ZipArchive();
                    if ($zip->open( $f ) == true) {
                        
                        // check if there's a file in the root (if yes, create subdir)
                        $fileInRoot = false;
                        for($x=0; $x < $zip->numFiles; $x++) {
                            $n = $zip->statIndex($x);
                            if (strpos($n['name'], DIRECTORY_SEPARATOR) === false) {
                                $fileInRoot = true;
                                break;
                            }
                        }
                        
                        // determine path to unzip
                        $path = get_data_file('fastsite/templates');
                        if ($fileInRoot) {
                            $name = basename($f);
                            $name = substr($name, 0, strrpos($name, '.'));
                            $path = $path . '/' . $name;
                            if (mkdir( $path, 0755, false ) == false) {
                                throw new FileException('Unable to create template directory');
                            }
                        }
                        
                        // unzip
                        $zip->extractTo( $path );
                        
                        // done
                        $zip->close();
                    }
                }
                
                redirect('/?m=fastsite&c=template/template');
            }
            
            if (isset($_FILES['file']) && isset($_FILES['file']['error'])) {
                if ($_FILES['file']['error'] === UPLOAD_ERR_INI_SIZE) {
                    $this->form->addError('file', 'The uploaded file exceeds the upload_max_filesize');
                } else {
                    $this->form->addError('file', 'Error uploading file');
                }
            }
            
        }
        
        return $this->render();
    }
    
    
    
    public function action_edit() {
        
        $this->form = object_container_create(TemplateSettingsForm::class);
        
        $tsService = object_container_get(FastsiteSettingsService::class);
        $ts = $tsService->readTemplateSettingsByName(get_var('n'));
        if ($ts === null) {
            $f = get_data_file('fastsite/templates/'.basename(get_var('n')));
            if (!$f) {
                throw new InvalidStateException('Template not found');
            }
            
            $ts = new TemplateSetting();
            $ts->setTemplateName(basename(get_var('n')));
        }
        $this->form->bind($ts);
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            
            if ($this->form->validate()) {
                $ts = $tsService->saveTemplateSettings($this->form);
                
                $fastsiteSettings = FastsiteSettings::getInstance();
                
                if ($ts->getActive()) {
                    // mark template as active
                    $fastsiteSettings->setActiveTemplate($ts->getTemplateName());
                    $r = $fastsiteSettings->save();
                }
                
                
                redirect('/?m=fastsite&c=template/fileEditor&n='.urlencode($ts->getTemplateName()));
            }
        }
        
        $this->templateName = $ts->getTemplateName();
        
        return $this->render();
    }
    
    public function action_delete() {
        
        $files = list_data_directory('fastsite/templates');
        
        foreach($files as $f) {
            $basename = $f;
            $lastDot = strrpos($f, '.');
            if ($lastDot !== false) {
                $basename = substr($basename, 0, $lastDot);
            }
            
            if ($basename == get_var('n')) {
                delete_data_path( 'fastsite/templates/'.$f, true );
            }
        }
        
        redirect('/?m=fastsite&c=template/template');
    }
    
}
