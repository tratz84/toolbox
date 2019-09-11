<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use fastsite\data\FastsiteTemplateFileSettings;
use fastsite\data\FastsiteTemplateSettings;

class templateFileController extends BaseController {
    
    
    public function __construct() {
        parent::__construct();
        
    }
    
    public function action_index() {
        $this->template = get_var('n');
        $this->file = get_var('f');
        
        $t = get_data_file_safe('fastsite/templates', $this->template);
        if ($t === false) {
            throw new InvalidStateException('Template not found');
        }
        
        $this->tfs = new FastsiteTemplateFileSettings($this->template, $this->file);
        $this->tfs->load();
        
        
        $this->snippets = $this->tfs->getSnippets();
        for($x=0; $x < count($this->snippets); $x++) {
            $this->snippets[$x]['code'] = tpl_load_snippet( $this->template, $this->snippets[$x]['snippet_name'] );
        }
        
        
        $ftSettings = new FastsiteTemplateSettings($this->template);
        $ftSettings->load();
        if (is_post()) {
            
            // save snippet code
            $snippets = is_array($_REQUEST['snippets']) ? $_REQUEST['snippets'] : array();
            foreach($snippets as $s) {
                $ftSettings->saveSnippet( $s['snippet_name'], $s['snippet_code']);
            }
            
            // save snippets linked to tempalte
            $snippet_links = array();
            foreach($snippets as $s) {
                $snippet_links[] = array(
                    'xpath' => $s['snippet_xpath'],
                    'snippet_name' => $s['snippet_name']
                );
            }
            $this->tfs->setSnippets( $snippet_links );
            $this->tfs->setDescription( get_var('description') );
            $this->tfs->save();
            
            
            // global settings
            if (get_var('default_template')) {
                $ftSettings->setDefaultTemplateFile($this->file);
            }
            if ($this->tfs->getDescription()) {
                $ftSettings->registerTemplateFile($this->file, $this->tfs->getDescription());
            } else {
                $ftSettings->unregisterTemplateFile($this->file);
            }
            
            $ftSettings->save();
            
            
            report_user_message('Changes saved');
            
            redirect('/?m=fastsite&c=template/templateFile&n='.urlencode($this->template).'&f='.urlencode($this->file));
        }
        
        $this->ftSettings = $ftSettings;
        
        return $this->render();
    }
    
    protected function listSnippets( $template ) {
        $t = get_data_file_safe('fastsite/templates/', $template);
        if (!$t) {
            throw new InvalidStateException('Template not found');
        }
        
        $t = $t.'/fastsite/';
        
        $files = list_files($t);
        
        $arr = array();
        if ($files) foreach($files as $f) {
            if (strpos($f, 'snippet-') === 0 && file_extension($f) == 'php') {
                $snippetName = substr($f, 8, -4);
                
                if ($snippetName) {
                    $arr[] = $snippetName;
                }
            }
        }
        
        return $arr;
    }
    
    public function action_snippet() {
        $this->snippets = $this->listSnippets( $this->template );
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    public function action_load_snippet() {
        $template = get_var('template');
        $snippet = get_var('snippet');

        
        $resp = array();
        $resp['success'] = false;
        
        $f = get_data_file_safe('fastsite/templates', $template.'/fastsite/snippet-'.$snippet.'.php');
        
        if ($f) {
            $data = file_get_contents( $f );
            if ($data !== false) {
                $resp['data'] = $data;
                $resp['success'] = true;
            } else {
                $resp['message'] = 'Unable to read snippet-file';
            }
        } else {
            $resp['message'] = 'snippet-file not found';
        }
        
        $this->json($resp);
    }
    
    
    
    
}
