<?php


use core\controller\BaseController;

class generatedjsController extends BaseController {
    
    
    public function action_lang() {
        
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60 * 24))); // 24 hours
        header("Pragma: cache");
        header("Cache-Control: max-age=3600");
        header('Content-type: text/javascript');
        
        $lang = t_loadlang();
        
        
        print 'var lang_insights = ' . json_encode($lang) . ';' . PHP_EOL;
        
        print '
            function _(str) {
                if (typeof lang_insights[str] != \'undefined\') {
                    return lang_insights[str];
                } else {
                    return str;
                }
            }';
        
        
        
    }
}
