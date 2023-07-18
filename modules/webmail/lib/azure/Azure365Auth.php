<?php


namespace webmail\azure;


use webmail\service\CloudTokenService;

class Azure365Auth {
    
    protected $endpoint      = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    
    protected $webmailAzureTokenId = null;
    
    protected $clientId     = null;
    protected $clientSecret = null;
    protected $redirectUri  = 'http://localhost/';
    protected $code         = null;
    
    protected $scopes    = array();
    protected $resources = array();
    
    protected $codeVerifier;
    
    
    
    public function __construct() {
//         $this->addScope( 'IMAP.AccessAsUser.All' );
//         $this->addScope( 'SMTP.Send' );
//         $this->addScope( 'https://ps.outlook.com/IMAP.AccessAsApp' );
        $this->addScope( 'https://outlook.office.com/IMAP.AccessAsUser.All' );
//         $this->addScope( 'Mail.Send' );
        $this->addScope( 'offline_access' );
//         $this->addResource( 'IMAP.AccessAsUser.All' );
//         $this->addResource( 'offline_access' );
        //      
//         $this->addScope( 'https://outlook.office.com/SMTP.SendAsApp' );
    }
    
    public function setEndpoint($p) { $this->endpoint = $p; }
    public function getEndpoint() { return trim($this->endpoint); }
    
    public function setClientId($p) { $this->clientId = $p; }
    public function setClientSecret($p) { $this->clientSecret = $p; }
    public function setRedirectUri($p) { $this->redirectUri = $p; }
    public function setCode($p) { $this->code = $p; }
    
    
    public function addScope($s) { $this->scopes[] = $s; }
    public function clearScopes() { $this->scopes = array(); }
    
    public function addResource($s) { $this->resources[] = $s; }
    public function clearResources() { $this->resources = array(); }
    
    public function getCodeVerifier() { return $this->codeVerifier; }
    public function setCodeVerifier($p) { $this->codeVerifier = $p; }
    
    public function loadAzureTokenById( $id ) {
        $this->webmailAzureTokenId = $id;
        
        $ctService = object_container_get( CloudTokenService::class );
        $wat = $ctService->readAzureToken( $id );
        
        $this->setEndpoint( $wat->getAzureAuthorizationUrl() );
        $this->setClientId( $wat->getAzureClientId() );
        $this->setClientSecret( $wat->getAzureClientSecret() );
        $this->setRedirectUri( $ctService->getAzureRedirectUri() );
        
        $this->setCode( 'wattok' . $id );
    }
    
    
    public function getRedirectAuthUrl() {
        $url = $this->endpoint;
        
        $params = array();
        $params['client_id']      = $this->clientId;
        $params['response_type']  = 'code';
        $params['redirect_uri']   = $this->redirectUri;
        $params['response_mode']  = 'query';
        $params['state']          = $this->code;
        $params['prompt']         = 'select_account';
        
        $verifierBytes = random_bytes(64);
        $this->codeVerifier = $codeVerifier = rtrim(strtr(base64_encode($verifierBytes), "+/", "-_"), "=");
        
        $hash = hash('sha256', $codeVerifier);
        $code_challenge = rtrim(strtr(base64_encode(pack('H*', $hash)), "+/", "-_"), "=");
        
        $params['code_challenge'] = $code_challenge;
        $params['code_challenge_method'] = 'S256';

        // afaik this is consumer-stuff
        if (count($this->scopes))
            $params['scope'] = implode(' ', $this->scopes);
        
        // afaik this is enterprise/business-stuff
        if (count($this->resources))
            $params['resource'] = implode(' ', $this->resources);
        
        $url = $url . '?' . http_build_query($params);
        
        // save request
        if ($this->webmailAzureTokenId) {
            $request_data = array();
            $request_data = array_merge($params, $request_data);
            $request_data['code_verifier'] = $codeVerifier;
            
            $ctService = object_container_get( CloudTokenService::class );
            $ctService->saveAzureRequestData( $this->webmailAzureTokenId, $request_data );
        }
        
        return $url;
    }
    
    
    public function refreshToken( ) {
        
        $ctService = object_container_get( CloudTokenService::class );
        $wat = $ctService->readAzureToken( $this->webmailAzureTokenId );
        
        $this->setEndpoint( $wat->getAzureTokenUrl() );
        
        
        $json = json_decode( $wat->getResponseData() );
        if (!$json || isset($json->refresh_token) == false)
            return false;
        
        
        $params = array();
        $params['grant_type']    = 'refresh_token';
        $params['client_id']     = $this->clientId;
        $params['client_secret'] = $this->clientSecret;
        
        $params['refresh_token'] = $json->refresh_token;
            
        // afaik this is consumer-stuff
        if (count($this->scopes))
            $params['scope'] = implode(' ', $this->scopes);
        
        // afaik this is enterprise/business-stuff
        if (count($this->resources))
            $params['resource'] = implode(' ', $this->resources);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $this->endpoint );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        
        $r = curl_exec($ch);
        $json = json_decode( $r );
        
        if ($this->webmailAzureTokenId) {
            $ctService = object_container_get( CloudTokenService::class );
            $wat = $ctService->readAzureToken( $this->webmailAzureTokenId );
            $wat->setResponseData( $r );
            
            if ($json && isset($json->expires_in)) {
                $wat->setExpiresIn( $json->expires_in );
                $wat->setRefreshTimestamp( date('Y-m-d H:i:s') );
            }
            else {
                $wat->setExpiresIn( -1 );
                $wat->setRefreshTimestamp( null );
            }
            
            $wat->save();
        }
        
        if ($json) {
            return $json;
        }
        else {
            return false;
        }
    }
    
    
    public function requestToken() {
        $params = array();
        $params['grant_type']    = 'authorization_code';
        $params['client_id']     = $this->clientId;
        $params['client_secret'] = $this->clientSecret;
        $params['code_verifier'] = $this->codeVerifier;
        
        if ($this->code)
            $params['code'] = $this->code;
        $params['redirect_uri']  = $this->redirectUri;
        
        // afaik this is consumer-stuff
        if (count($this->scopes))
            $params['scope'] = implode(' ', $this->scopes);
        
        // afaik this is enterprise/business-stuff
        if (count($this->resources))
            $params['resource'] = implode(' ', $this->resources);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $this->endpoint );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        
        $r = curl_exec($ch);
//         var_export($params);exit;
//         print $r;exit;
        $json = json_decode( $r );
        
        if ($this->webmailAzureTokenId) {
            $ctService = object_container_get( CloudTokenService::class );
            $wat = $ctService->readAzureToken( $this->webmailAzureTokenId );
            $wat->setResponseData( $r );
            
            if ($json && isset($json->expires_in)) {
                $wat->setExpiresIn( $json->expires_in );
                $wat->setRefreshTimestamp( date('Y-m-d H:i:s') );
            }
            else {
                $wat->setExpiresIn( -1 );
                $wat->setRefreshTimestamp( null );
            }
            
            $wat->save();
        }
        
        if ($json) {
            return $json;
        }
        else {
            return false;
        }
    }
    
}



