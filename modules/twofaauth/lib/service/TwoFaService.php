<?php

namespace twofaauth\service;


use core\service\ServiceBase;
use twofaauth\model\TwoFaCookieDAO;
use twofaauth\model\TwoFaCookie;

class TwoFaService extends ServiceBase {
    
    
    
    public function readCookie( $cookieValue ) {
        $tfCookieDao = object_container_get( TwoFaCookieDAO::class );
        
        // id inside cookie-string?
        if (strpos($cookieValue, ':') !== false) {
            list($cookieId, $cookieVal) = explode(':', $cookieValue);
            
            $tfc = $tfCookieDao->read( $cookieId );
            if ($tfc && $tfc->getCookieValue() == $cookieVal) {
                return $tfc;
            }
        }
        
        // try to read by cookie_value
        return $tfCookieDao->readByValue( $cookieValue );
    }
    
    public function activateCookie( $cookieValue ) {
        $tfa = $this->readCookie( $cookieValue );
        
        if (!$tfa) {
            return false;
        }
        
        $tfa->setActivated( true );
        $tfa->setLastVisit( date('Y-m-d H:i:s') );
        return $tfa->save();
    }
    
    public function checkCookie($cookieValue) {
        if (trim($cookieValue) == '') {
            return false;
        }
        
        $c = $this->readCookie($cookieValue);
        if ($c && $c->getActivated() && $c->getUserId() == ctx()->getUser()->getUserId()) {
            $tfCookieDao = object_container_get( TwoFaCookieDAO::class );
            
            $tfCookieDao->updateLastVisit( $c->getCookieId() );
            
            return true;
        }
        
        return false;
    }
    
    public function lookupCookie($userId, $oldSecret) {
        $tfCookieDao = object_container_get( TwoFaCookieDAO::class );
        
        $tfs = $tfCookieDao->search([
            'user_id' => $userId,
            'after_created_date' => date('Y-m-d H:i:s', time() - 60*30),
            'return_list' => true
        ]);
        
        return $tfs;
    }
    
    
    public function cleanupCookies() {
        $tfCookieDao = object_container_get( TwoFaCookieDAO::class );
        
        // delete not-activated entries older then 30 minutes
        $tfCookieDao->query('delete from twofaauth__two_fa_cookie where activated = false and created <= ?', array(date('Y-m-d H:i:s', time()-(60*30))));
        
        // delete last_visit-entries older then 60 days
        $tfCookieDao->query('delete from twofaauth__two_fa_cookie where last_visit <= ?', array(date('Y-m-d H:i:s', time()-(60 * 60 *24 * 60))));
    }
    
    
    public function createCookie($user=null) {
        if ($user == null) {
            $user = ctx()->getUser();
        }
        
        $tfc = new TwoFaCookie();
        $tfc->setCookieValue(md5(uniqid().uniqid().uniqid().uniqid().uniqid().uniqid().uniqid().time()));
        $tfc->setSecretKey( random_int(10000, 99999) );
        $tfc->setActivated( false );
        $tfc->setUserId( $user->getUserId() );
        $tfc->save();
        
        return $tfc;
    }
    
    
}
