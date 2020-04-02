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
        
        // get-request? => show if password is set/not-set in placeholder and not actually the password itself
        if (is_get()) {
            $w = $this->form->getWidget('mail_password');
            
            if ($w->getValue()) {
                $w->setValue('');
                $w->setPlaceholder('Password set');
            } else {
                $w->setPlaceholder('No password set');
            }
        }
        
        
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
                
                report_user_message(t('Changes saved'));
                redirect('/?m=webmail&c=settingsMailOut');
            }
            
        }
        
        
        return $this->render();
    }
    
    public function test_mail() {
        
    }
    
    
}
