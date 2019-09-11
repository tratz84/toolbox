<?php



use core\controller\BaseController;
use core\exception\InvalidStateException;
use fastsite\template\TemplatePageData;
use core\exception\FileException;

class templatePageController extends BaseController {
    
    
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
        
        $this->tpd = new TemplatePageData($this->template, $this->file);
        $this->tpd->load();
        
        $this->snippets = $this->tpd->getSnippets();
        for($x=0; $x < count($this->snippets); $x++) {
            $this->snippets[$x]['code'] = tpl_load_snippet( $this->template, $this->snippets[$x]['snippet_name'] );
        }
        
        
        if (is_post()) {
            $settings_dir = $t . '/fastsite';
            if (is_dir($settings_dir) == false) {
                if (mkdir($settings_dir)) {
                    throw new FileException('Unable to create settings-directory');
                }
            }
            
            // save snippet code
            $snippets = is_array($_REQUEST['snippets']) ? $_REQUEST['snippets'] : array();
            foreach($snippets as $s) {
                file_put_contents($settings_dir.'/snippet-'.$s['snippet_name'].'.php', $s['snippet_code']);
            }
            
            // save snippets linked to tempalte
            $snippet_links = array();
            foreach($snippets as $s) {
                $snippet_links[] = array(
                    'xpath' => $s['snippet_xpath'],
                    'snippet_name' => $s['snippet_name']
                );
            }
            $this->tpd->setSnippets( $snippet_links );
            
            
            
            $this->tpd->setPageName( get_var('template_page_name') );
            $this->tpd->save();
            
            
            report_user_message('Changes saved');
            
            redirect('/?m=fastsite&c=template/templatePage&n='.urlencode($this->template).'&f='.urlencode($this->file));
        }
        
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
            if ($data != false) {
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
