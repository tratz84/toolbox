<?php



use core\controller\BaseController;
use webmail\form\MailSettingsOutForm;
use webmail\service\EmailService;

class settingsMailOutController extends BaseController {
    
    
    public function action_index() {
        $emailService = object_container_get(EmailService::class);
        $mailServerSettings = $emailService->getMailServerSettings();
        
        $this->form = new MailSettingsOutForm();
        $this->form->bind( $mailServerSettings );
        
        
        if (is_post()) {
            $this->form->bind($_REQUEST);
            if ($this->form->validate()) {
                
                // save
                $emailService->saveMailServerSettings( $this->form );
                
                if (get_var('send_test')) {
                    if ($emailService->sendMailTest( get_var('send_test') )) {
                        report_user_message('Mail sent successfully');
                    } else {
                        report_user_error('Error sending mail');
                    }
                    redirect('/?m=webmail&c=settingsMailOut');
                }
                
                redirect('/?m=base&c=masterdata/index');
            }
            
        }
        
        
        return $this->render();
    }
    
    public function test_mail() {
        
    }
    
    
}
