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

        
        // implement this with curl
        $r = $this->callSoapService($countryCode, $vatNumber);
        
        if (is_object($r)) {
            $this->soapCache[$nr] = $r;
            
            // wrap response in a class?
            if ($r->valid) {
                return $r;
            }
            else {
                return null;
            }
        }
        
        return null;
    }
    
    protected function callSoapService($countryCode, $vatNr) {
        // build soap call
        $xml = new \DOMDocument();
        $elEnv = $xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Envelope');
        $elEnv->setAttribute('xmlns:urn', 'urn:ec.europa.eu:taxud:vies:services:checkVat:types');
        
        $elHeader = $xml->createElement('soapenv:Header');
        $elEnv->appendChild( $elHeader );
        
        
        $elBody = $xml->createElement('soapenv:Body');
        $elEnv->appendChild( $elBody );
        
        $elCheckVat = $xml->createElement('urn:checkVat');
        
        $elCountry = $xml->createElement('urn:countryCode');
        $elCountry->textContent = $countryCode;
        $elCheckVat->appendChild( $elCountry );
        
        $elVatNr = $xml->createElement('urn:vatNumber');
        $elVatNr->textContent = $vatNr;
        $elCheckVat->appendChild( $elVatNr );
        
        $elBody->appendChild($elCheckVat);
        $xml->appendChild($elEnv);
        
        
        $xml = $xml->saveXML(null);
        $xml = substr($xml, strpos($xml, '?>')+3);
        
        
        // send soap call
        $ch = curl_init('http://ec.europa.eu/taxation_customs/vies/services/checkVatService');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: text/xml; charset=UTF-8']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);        // timeout, in the past the service failed, while the connection hang infinite
        curl_setopt($ch, CURLOPT_TIMEOUT,        5);
        
        $r = curl_exec($ch);
        curl_close($ch);
        
        
        // parse
        $xd = new \DOMDocument();
        $xd->loadXML($r);
        $el = $xd->getElementsByTagName('checkVatResponse');
        
        $r = new \stdClass();
        $r->valid = false;
        if ($el->count()) {
            
            $el = $el->item(0)->getElementsByTagName('*');
            for($x=0; $x < $el->count(); $x++) {
                $el2 = $el->item($x);
                
                if (is_a($el2, \DOMElement::class)) {
                    $name = $el2->nodeName;
                    if ($name == 'valid') {
                        $r->valid = $el2->textContent == 'true' ? true : false;
                    } else {
                        $r->{$name} = $el2->textContent;
                    }
                }
            }
        }
        
        return $r;
    }
    
    
    
}

