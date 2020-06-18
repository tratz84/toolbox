<?php

namespace customer\externalapi;


class VatCheckApiService {
    
    protected $soapCache = array();
    
    
    /**
     * validateVat() - checks a vatnumber with the 'VIES' service
     * 
     * @return true or false
     */
    public function validateVat($nr) {
        $r = $this->vatInfo($nr);
        
        if (is_object($r) && isset($r) && $r->valid) {
            return true;
        }
        
        return false;
    }
    
    public function vatInfo($nr, $cacheEnabled=true) {
        $nr = strtoupper($nr);
        $nr = preg_replace('/[^A-Z0-9]/', '', $nr);
        
        // cached?
        if ($cacheEnabled && isset($this->soapCache[$nr])) {
            return $this->soapCache[$nr];
        }
        
        // regexp source, https://www.oreilly.com/library/view/regular-expressions-cookbook/9781449327453/ch04s21.html
        $regexp = "^((AT)?U[0-9]{8}|(BE)?0[0-9]{9}|(BG)?[0-9]{9,10}|(CY)?[0-9]{8}L|(CZ)?[0-9]{8,10}|(DE)?[0-9]{9}|(DK)?[0-9]{8}|(EE)?[0-9]{9}|"
            . "(EL|GR)?[0-9]{9}|(ES)?[0-9A-Z][0-9]{7}[0-9A-Z]|(FI)?[0-9]{8}|(FR)?[0-9A-Z]{2}[0-9]{9}|(GB)?([0-9]{9}([0-9]{3})?|[A-Z]{2}[0-9]{3})|"
            . "(HU)?[0-9]{8}|(IE)?[0-9]S[0-9]{5}L|(IT)?[0-9]{11}|"
            . "(LT)?([0-9]{9}|[0-9]{12})|(LU)?[0-9]{8}|(LV)?[0-9]{11}|(MT)?[0-9]{8}|"
            . "(NL)?[0-9]{9}B[0-9]{2}|(PL)?[0-9]{10}|(PT)?[0-9]{9}|(RO)?[0-9]{2,10}|"
            . "(SE)?[0-9]{12}|(SI)?[0-9]{8}|(SK)?[0-9]{10})$";
        
        // check regexp
        $r = preg_match('/'.$regexp.'/', $nr);
        if (!$r) {
            return null;
        }
        
        
        // check soap api
        $countryCode = substr($nr, 0, 2);
        $vatNumber = substr($nr, 2);
        
        try {
            $soapClient = new \SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');
            $r = $soapClient->checkVat(array('countryCode' => $countryCode, 'vatNumber' => $vatNumber));
        } catch (\Exception $ex) {
            return null;
        }
        
        if (is_object($r)) {
            $this->soapCache[$nr] = $r;
            
            // TODO: wrap response in a class?
            
            return $r;
        }
        
        return null;
    }
    
    
    
}

