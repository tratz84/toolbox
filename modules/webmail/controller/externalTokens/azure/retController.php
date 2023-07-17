<?php


use core\controller\BaseController;
use webmail\office365\Office365Auth;

class retController extends BaseController {
    
    
    public function action_index() {
        
        $code = get_var('code');
        $state = get_var('state');
        $session_state = get_var('session_state');
        
        print 'hi';
        
        $oa = new Office365Auth();
        $oa->setAuthUrl( 'https://login.microsoftonline.com/9bb7ad20-07f5-4b43-beb8-b60c680ea51c/oauth2/v2.0/token' );
        $oa->setCode( $code );
        $oa->setClientId( '29bca4e8-2abb-4a3d-9fd4-e053c2dac4b9' );
        $oa->setClientSecret( 'jl38Q~FvEpfLv6hAj8oKYEyc1pIJfie26fN_3b~f' );
        $oa->setRedirectUri( 'https://portal.itxplain.nl/itxplain/?m=webmail&c=office365/ret' );
        print $oa->requestToken();
        
    }
    
    
}