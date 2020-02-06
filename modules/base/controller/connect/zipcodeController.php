<?php


use core\controller\BaseController;

class zipcodeController extends BaseController {
    
    
    public function action_index() {
        
        $zipcode = strtoupper( get_var('zipcode') );
        $nr = get_var('nr');
        
        
        $zipcode = preg_replace('/[^A-Z0-9]/', '', $zipcode);
        if (preg_match('/^[0-9]{4}[A-Z]{2}$/', $zipcode) == false) {
            return $this->json(array(
                'success' => false,
                'error' => 'Invalid zipcode'
            ));
        }
        
        $nrs = array();
        if (preg_match('/\\d+/', $nr, $nrs) == false) {
            return $this->json(array(
                'success' => false,
                'error' => 'Invalid house nr'
            ));
        }
        
        $nr = $nrs[0];
        
        
        $query = 'q='.urlencode($zipcode . ' ' . $nr);
        
        $url = 'http://geodata.nationaalgeoregister.nl/locatieserver/free?'.$query;
        
//         print $url;exit;
        $resp = get_url($url);
//         print $resp;exit;
        
        $r = @json_decode($resp);
        
        if ($r == false || is_object($r) == false || isset($r->response) == false || $r->response->numFound == 0 || count($r->response->docs) == 0) {
            var_export($resp);exit;
            return $this->json(array(
                'success' => false,
                'error' => 'Zipcode not found'
            ));
        }
        
        
        $d = $r->response->docs[0];
        
        $r = array();
        $r['city']          = $d->woonplaatsnaam;
        $r['province']      = $d->provincienaam;
        $r['province_code'] = $d->provincieafkorting;
        $r['street']        = $d->straatnaam;
        
        $this->json(array(
            'success' => true,
            'data' => $r
        ));
    }
    
    
}


