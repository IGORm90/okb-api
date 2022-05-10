<?php

namespace App;

use Exception;
use App\XmlController;

class RequestController {

    private $functionIds = [
        'informReq' => '67', //Запрос по информационной части
        'expandedFizReq' => '40', //Запрос по физ. Лицам расширенный
        'standartFizReq' => '70', //Запрос по физ. Лицам стандартный
        'entityReq' => '53+73', //Запросы по юр. Лицам
    ];

    private $subscriber = '6189';
    private $group = 'MRKO-TEST';
    private $user = 'User1';
    private $password = 'mrko-te@84';
    private $serverAddres = "https://ch-test.bki-okb.com/cpuEnquiry.asp";
    private $certHash = 'E937A41F36A8AEB947E332ED69B2278B59592988';
    private $cryptoProPath = '/opt/cprocsp/bin/amd64/';

    public function __construct() {

    }

    public function sendRequest($xmlString, $functionId){
        
        try {

            $ch = curl_init();
            $encodedString = curl_escape($ch, $xmlString);
            $requestBody = "Subscriber={$this->subscriber}&Group={$this->group}&User={$this->user}&Password={$this->password}&Format=xml&Function={$functionId}&Request=";
            $requestBody .= $encodedString;

            $headers = [
                "Content-Type: application/x-www-form-urlencoded; Charset=windows-1251",
                "Connection: Close",
                "Content-Length: " . strval(strlen($requestBody)),
            ];

            curl_setopt($ch, CURLOPT_URL, $this->getB2BokbPath());
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'CERT_SHA1_HASH_PROP_ID:CERT_SYSTEM_STORE_LOCAL_MACHINE:MY');
            curl_setopt($ch, CURLOPT_SSLCERT, $this->certHash);

            $responce = curl_exec($ch);

            if(curl_errno ($ch) == 0){
                return $responce;
            } else {
                return curl_error($ch);
            }

        } catch (Exception $e) {
           return $e->getMessage();
        }      
    
    }

    public function getB2BokbPath(){
        return $this->cryptoProPath;
    }

    public function getFunctionIds(){
        return $this->functionIds;
    }

    public function makePostCommand($encodedXmlString, $functionId){

        $cryptoProPath = $this->getB2BokbPath();

        $command = "{$cryptoProPath}curl -X POST {$this->serverAddres} \
            -E {$this->certHash} \
            -d Subscriber={$this->subscriber} \
            -d Group={$this->group} \
            -d User={$this->user} \
            -d Password={$this->password} \
            -d Format=xml \
            -d Function={$functionId} \
            -d 'Request={$encodedXmlString}'
            ";

        return $command;

    }

    public function encodeXML($xmlString){
        $ch = curl_init();
        $xmlString = curl_escape($ch, $xmlString);
        curl_close($ch); 
        return $xmlString;
    }

    public function decodeAnswer($answer){
        return base64_decode($answer);
    }

    public function checkServerError($serverAnswerString){
        $pos = strpos($serverAnswerString, 'DOCTYPE');
        if($pos){
            return $this->getPageTitle($serverAnswerString);
        }
        return false; 
    }

    public function getPageTitle($htmlString) {

        $matches = array();
    
        if (preg_match('/<title>(.*?)<\/title>/', $htmlString, $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }


    public function execPostRequest($xmlString, $functionId) {

        try {
            
            $xmlString = $this->encodeXML($xmlString);

            $terminalCommand = $this->makePostCommand($xmlString, $functionId);

            $reqAnswer = shell_exec($terminalCommand);

            return $reqAnswer;

        } catch (Exception $e) {
            return false;
        }  

    }

}