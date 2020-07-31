<?php


namespace twofaauth\handler;

use core\template\DefaultTemplate;
use webmail\model\EmailTo;
use webmail\service\EmailService;
use twofaauth\service\TwoFaService;
use webmail\mail\SendMail;

class TwoFaEmailHandler {

    
    public function __construct() {
        
    }
    
    
    public function execute() {
        $user = ctx()->getUser();
        
        
        // fetch cookie
        $tfService = object_container_get( TwoFaService::class );
        $cookie = null;
        if (isset($_COOKIE['twofaauth'])) {
            $cookie = $tfService->readCookie( $_COOKIE['twofaauth'] );
        }
        
        // cookie entry not found?
        if ($cookie == null) {
            $this->sendMail();
        }

        $error_msg = null;
        if (is_post()) {
            
            // new password requested?
            if (get_var('btnNewCode')) {
                $this->sendMail( $user );
            }
            
            // form submitted or next-button clicked?
            if (get_var('btnNext')) {
                $secret_key = trim(get_var('c'));
                
                
                $succes = false;
                if ($cookie && $secret_key == $cookie->getSecretKey()) {
                    $tfService->activateCookie( $_COOKIE['twofaauth'] );
                    
                    $succes = true;
                }
                else {
                    // check old secret_key's last 30 minutes? user might have clicked 'New code' twice button...
                    $old_cookies = $tfService->lookupCookie( $user->getUserId(), $secret_key );
                    foreach ( $old_cookies as $oc ) {
                        if ($oc->getSecretKey() == $secret_key && $oc->getActivated() == false) {
                            $tfService->activateCookie( $oc->getCookieValue() );
                            
                            $cookie = $oc;
                            
                            $succes = true;
                            break;
                        }
                    }
                }
                
                // code success?
                if ($succes) {
                    if (get_var('remember_me')) {
                        setcookie('twofaauth', $cookie->getCookieId().':'.$cookie->getCookieValue(), time()+(60*60*24*365), appUrl('/'));
                    }
                    
                    header('Location: ' . $_SERVER['REQUEST_URI'] );
                    exit;
                }
                
                
                $error_msg = t('Invalid code');
            }
        }
        
        
        // request code
        $tplEmail = new DefaultTemplate( module_file('twofaauth', 'templates/auth/email.php') );
        $tplEmail->setVar('masked_email', \mask_email($user->getEmail()));
        $tplEmail->setVar('error_msg', $error_msg);
        
        if (validate_email( $user->getEmail() ) == false) {
            $tplEmail->setVar('fatal_error', t('User has no valid e-mail configured. Unable to proceed'));
        }
        
        $tplDecorator = new DefaultTemplate( module_file('base', 'templates/decorator/blank.php') );
        $tplDecorator->setVar('context', ctx());
        $tplDecorator->setVar('pageTitle', ['Two factor authentication']);
        $tplDecorator->setVar('content', $tplEmail->getTemplate());
        $tplDecorator->showTemplate();
        exit;
    }
    
    /**
     * sendMail() - creates twofaauth-cookie & sends mail with secret_key-code
     */
    public function sendMail($user=null) {
        if ($user == null) {
            $user = ctx()->getUser();
        }
        
        // set cookie
        $tfService = object_container_get( TwoFaService::class );
        $tfc = $tfService->createCookie();
        setcookie('twofaauth', $tfc->getCookieId().':'.$tfc->getCookieValue(), null, appUrl('/'));
        
        // send mail
        $html = get_template(module_file('twofaauth', 'templates/email/twofaauth_code.php'), [
            'user'       => $user,
            'secret_key' => $tfc->getSecretKey()
        ]);
        
        
        $emailService = object_container_get(EmailService::class);
        $identity = $emailService->readSystemMessagesIdentity();
        
        $e = new \webmail\model\Email();
        $e->setStatus(\webmail\model\Email::STATUS_DRAFT);
        if ($identity) {
            $e->setIdentityId($identity->getIdentityId());
            $e->setFromName($identity->getFromName());
            $e->setFromEmail($identity->getFromEmail());
        }
        $e->setUserId( $user->getUserId() );
        $e->setSubject( t('Authentication code for') . ' ' . $user->getUsername() );
        $e->setTextContent($html);
        $e->setIncoming(false);
        
        
        $et = new EmailTo();
        $et->setToEmail( $user->getEmail() );
        $e->addRecipient($et);
        
        $emailService->createDraft( $e );
        $sm = SendMail::createMail( $e );

        return $sm->send();
    }
    
    
}

