<?php


use admin\model\ExceptionLog;
use core\controller\BaseController;
use core\exception\InvalidStateException;

class debugController extends BaseController {
    
    public function init() {
        
    }
    
    
    public function action_show_debug_info() {
        if (!DEBUG) {
            throw new InvalidStateException('Debugging not enabled');
        }
        
        $this->setShowDecorator(false);
        
        return $this->render();
    }
    
    /**
     * action_report_bug() - reports bug, used in javascript
     */
    public function action_report_bug() {
        $cn = \core\Context::getInstance()->getContextName();
        debug_admin_notification('Error: ' . $cn . ': ' . get_var('message'));
        
        $el = new ExceptionLog();
        $el->setContextName(ctx()->getContextName());
        $el->setRequestUri( get_var('url') );
        if (ctx()->getUser())
            $el->setUserId(ctx()->getUser()->getUserId());
        $el->setMessage('Javascript bug');
        
        $el->setStacktrace(get_var('message'));
        $el->save();
    }
    
}
