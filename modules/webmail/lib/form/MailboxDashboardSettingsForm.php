<?php

namespace webmail\form;


class MailboxDashboardSettingsForm extends MailboxSearchSettingsForm {
    
    
    public function __construct() {
        
        parent::__construct();
        
        // mailbox specific widgets
        $this->removeWidget('lblHide');
        $this->removeWidget('hideFolderList');
        
        $this->disableSubmit();
        $this->hideSubmitButtons();
        
    }
    
}
