<?php


namespace webmail\azure;


class Office365Auth {
    
    protected $authUrl      = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    
    protected $clientId     = null;
    protected $clientSecret = null;
    protected $redirectUri  = 'http://localhost/';
    protected $code         = null;
    
    protected $scopes    = array();
    protected $resources = array();
    
    public function __construct() {
        $this->addScope( 'https://outlook.office.com/IMAP.AccessAsUser.All' );
//         $this->addScope( 'https://outlook.office.com/SMTP.SendAsApp' );
    }
    
    public function setAuthUrl($p) { $this->authUrl = $p; }
    
    public function setClientId($p) { $this->clientId = $p; }
    public function setClientSecret($p) { $this->clientSecret = $p; }
    public function setRedirectUri($p) { $this->redirectUri = $p; }
    public function setCode($p) { $this->code = $p; }
    
    
    public function addScope($s) { $this->scopes[] = $s; }
    public function clearScopes() { $this->scopes= array(); }
    
    public function addResource($s) { $this->resources[] = $s; }
    public function clearResources() { $this->resources = array(); }
    
    
//     public function getRedirectUri() {
//         $params = array();
//         $params['grant_type']    = 'authorization_code';
//         $params['client_id']     = $this->clientId;
// //         $params['client_secret'] = $this->clientSecret;
        
//         if ($this->code)
//             $params['code'] = $this->code;
// //             $params['redirect_uri']  = $this->redirectUri;
        
//         // afaik this is consumer-stuff
//         if (count($this->scopes))
//             $params['scope'] = implode(',', $this->scopes);
        
//         // afaik this is enterprise/business-stuff
//         if (count($this->resources))
//             $params['resource'] = implode(',', $this->resources);
        
//         $url = $this->authUrl;
//         $url .= '?' . http_build_query( $params );
//         return $url;
//     }
    
    public function getRedirectAuthUrl() {
        $url = $this->authUrl;
        
        $params = array();
        $params['client_id']      = $this->clientId;
        $params['response_type']  = 'code';
        $params['redirect_uri']   = $this->redirectUri;
        $params['response_mode']  = 'query';
        $params['state']          = $this->code;
        $params['prompt']         = 'select_account';
        
        $verifierBytes = random_bytes(64);
        $codeVerifier = rtrim(strtr(base64_encode($verifierBytes), "+/", "-_"), "=");
        
        $hash = hash('sha256', $codeVerifier);
        $code_challenge = rtrim(strtr(base64_encode(pack('H*', $hash)), "+/", "-_"), "=");
        
        $_SESSION['random_data'] = $codeVerifier;
        
        $params['code_challenge'] = $code_challenge;
        $params['code_challenge_method'] = 'S256';

        // afaik this is consumer-stuff
        if (count($this->scopes))
                $params['scope'] = implode(',', $this->scopes);
        
        // afaik this is enterprise/business-stuff
        if (count($this->resources))
                $params['resource'] = implode(',', $this->resources);
        
        $url = $url . '?' . http_build_query($params);
        
        return $url;
    }
    
    
    public function requestToken() {
        
//         var_export($_GET);exit;
//
//        hi{"token_type":"Bearer","scope":"https://outlook.office.com/IMAP.AccessAsUser.All","expires_in":4249,"ext_expires_in":4249,"access_token":"eyJ0eXAiOiJKV1QiLCJub25jZSI6Ijc4b3R0RGpaR3NDTTM3R0o2Y2w5T0ktWkZaeUljOGI0TWotRjIxZmhkSGciLCJhbGciOiJSUzI1NiIsIng1dCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyIsImtpZCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyJ9.eyJhdWQiOiJodHRwczovL291dGxvb2sub2ZmaWNlLmNvbSIsImlzcyI6Imh0dHBzOi8vc3RzLndpbmRvd3MubmV0LzliYjdhZDIwLTA3ZjUtNGI0My1iZWI4LWI2MGM2ODBlYTUxYy8iLCJpYXQiOjE2ODk1ODE5MjEsIm5iZiI6MTY4OTU4MTkyMSwiZXhwIjoxNjg5NTg2NDcxLCJhY2N0IjowLCJhY3IiOiIxIiwiYWlvIjoiQVZRQXEvOFRBQUFBSnJheVBnanFIcmlpZHJwU0Q5c205WlYyejRQWE1sOE0vMThqTFpZcFY1L3hxSFIwNFZsbVhKMTJ2TXlZd3VsS2pPd0VMYzIwSlgrbUE3WEFNczNRMGpWSTVVVXgwUXNidFJTZHRTQS9HYWs9IiwiYW1yIjpbInB3ZCIsIm1mYSJdLCJhcHBfZGlzcGxheW5hbWUiOiJ0b29sYm94IGNvbm5lY3Rpb24gdjEiLCJhcHBpZCI6IjI5YmNhNGU4LTJhYmItNGEzZC05ZmQ0LWUwNTNjMmRhYzRiOSIsImFwcGlkYWNyIjoiMSIsImVuZnBvbGlkcyI6W10sImZhbWlseV9uYW1lIjoidmFuIFdlZXplbmJlZWsiLCJnaXZlbl9uYW1lIjoiVGltIiwiaXBhZGRyIjoiODQuMjQxLjIwNi40MiIsImxvZ2luX2hpbnQiOiJPLkNpUXlORGN6TVRsaFlpMWlOemRsTFRRd056UXRZalJpTkMweE5ESTRNelE0WTJWbVltSVNKRGxpWWpkaFpESXdMVEEzWmpVdE5HSTBNeTFpWldJNExXSTJNR00yT0RCbFlUVXhZeG9QZEdsdFFHbDBlSEJzWVdsdUxtNXNJRTg9IiwibmFtZSI6IlRpbSB2YW4gV2VlemVuYmVlayIsIm9pZCI6IjI0NzMxOWFiLWI3N2UtNDA3NC1iNGI0LTE0MjgzNDhjZWZiYiIsInB1aWQiOiIxMDAzMjAwMkMxMUY4QUY5IiwicmgiOiIwLkFhOEFJSzIzbV9VSFEwdS11TFlNYUE2bEhBSUFBQUFBQVBFUHpnQUFBQUFBQUFDdkFHMC4iLCJzY3AiOiJJTUFQLkFjY2Vzc0FzVXNlci5BbGwiLCJzaWQiOiI0MmFhMDk0Zi04OWI5LTRhMTAtOGRlZS05YjFlNWFmNTQ1MTkiLCJzaWduaW5fc3RhdGUiOlsia21zaSJdLCJzdWIiOiJGQXhRcEhhUC1mc1lvX2ZIU0xIRERSMVdWWXV2bnhkaXdJYTBZUDZGTFZJIiwidGlkIjoiOWJiN2FkMjAtMDdmNS00YjQzLWJlYjgtYjYwYzY4MGVhNTFjIiwidW5pcXVlX25hbWUiOiJ0aW1AaXR4cGxhaW4ubmwiLCJ1cG4iOiJ0aW1AaXR4cGxhaW4ubmwiLCJ1dGkiOiJPN3RQTVhCLWhrZVpJYS1HN3ZVUUFBIiwidmVyIjoiMS4wIiwid2lkcyI6WyI2MmU5MDM5NC02OWY1LTQyMzctOTE5MC0wMTIxNzcxNDVlMTAiLCJiNzlmYmY0ZC0zZWY5LTQ2ODktODE0My03NmIxOTRlODU1MDkiXX0.pvWT8dyyZVUeLbM-UyPjFutWtWc3JxsrWYhWW89mEL5pXYiZsUoKpzMlstGggZARbJcpMP5tRTtXUoC3bhpwVXL4e4oEqRW7eYjfIX6-uoq5zepp32RDAkuiA0MaBCHkpCl2CfvARfEc8vNQgP228CygaNP-1LULuT-A77N1yTEeQpHzvteaJEBIKdau4ORBrNteEkmpes0XeMl1SQyyTgJHTQ8Nqd8Gqb4AkiV9IvIk7r8vnRoAjGeYhnW8GXRP_Vvo6Z7vpQlyU5hDsiZwe3KDLBJPZ0YFLNtvNV5e7cykUKUGAqHVanjiGbGOQ6CdwcKkfrYKjAE6sdAZgggiUA"}
        
        $params = array();
        $params['grant_type']    = 'authorization_code';
        $params['client_id']     = $this->clientId;
        $params['client_secret'] = $this->clientSecret;
        $params['code_verifier'] = $_SESSION['random_data'];
//         print strlen($_SESSION['random_data']);exit;
//         $params['code_verifier'] = base64_encode( hash('sha256', $_SESSION['random_data']) );
//         var_export($params);exit;
        
        if ($this->code)
            $params['code'] = $this->code;
        $params['redirect_uri']  = $this->redirectUri;
        
        // afaik this is consumer-stuff
        if (count($this->scopes))
            $params['scope'] = implode(',', $this->scopes);
        
        // afaik this is enterprise/business-stuff
        if (count($this->resources))
            $params['resource'] = implode(',', $this->resources);
        
//             var_export($params);exit;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $this->authUrl );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
//         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//             'Content-type: application/json'
//         ));
        
        $r = curl_exec($ch);
        print $r;exit;
        
    }
    
    
}



