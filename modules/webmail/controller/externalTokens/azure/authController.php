<?php



use core\controller\BaseController;
use webmail\office365\Office365Auth;

class authController extends BaseController {
    
    
    public function action_index() {
        
    }
    
    public function action_start_auth() {
        
        $oa = new Office365Auth();
        $oa->setAuthUrl('https://login.microsoftonline.com/9bb7ad20-07f5-4b43-beb8-b60c680ea51c/oauth2/v2.0/authorize');
        $oa->setClientId( '29bca4e8-2abb-4a3d-9fd4-e053c2dac4b9' );
        $oa->setRedirectUri( 'https://portal.itxplain.nl/itxplain/?m=webmail&c=office365/ret' );
        $oa->setCode( 'con1' );
        print $oa->getRedirectAuthUrl();exit;
        header('Location: ' . $oa->getRedirectAuthUrl());
        
    }
    
    
    
}

