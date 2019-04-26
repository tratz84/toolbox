<?php



use admin\controller\AdminBaseController;
use admin\service\AdminUserService;

class userSettingsController extends AdminBaseController {
    
    public function action_index() {
        
        
        redirect('/?m=admin&c=userSettings&a=password');
    }
    
    
    public function action_password() {
        
        if (is_post()) {
            if (strlen(trim($_POST['p1'])) < 3) {
                report_user_error('Wachtwoord niet gewijzigd: minimale lengte 3 karakters');
            } else if ($_POST['p1'] != $_POST['p2']) {
                report_user_error('Wachtwoord niet gewijzigd: wachtwoorden komen niet overeen');
            } else {
                
                $adminUserService = $this->oc->get(AdminUserService::class);
                
                $adminUserService->changePasswordCurrentUser($_POST['p1']);
                
                
                report_user_message('Wachtwoord aangepast');
                
                redirect('/');
            }
        }
        
        
        $this->render();
    }
    
    
}