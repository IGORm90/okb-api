<?php

namespace App;

use SimpleXMLElement;
use Exception;

class XmlController {

    public function prepareXML($inputXml){

        try {

            $startXml = strpos($inputXml, '<s>');
            $endtXml = strrpos($inputXml, '</s>');
            $outXml = new SimpleXMLElement(substr($inputXml, $startXml, $endtXml));

            return $outXml->asXML();

        } catch (Exception $e) {
            return false;
        }
        
    }

    public function getError($xmlString){
        $xml = new SimpleXMLElement($xmlString);
        foreach($xml as $x){
            if(isset($x->attributes()['n']) && $x->attributes()['n'] == 'ValidationErrors'){
                if(isset($x->s->a)){
                    return $x->s->a->__toString();
                }
            }
        }
        return false;
    }

    public function generateXmlForOkb($userData){
        $xmlString = '<?xml version="1.0" encoding="windows-1251"?>
                        <s n="EnquiryRequest">
                            <a n="accountClass">1</a>
                            <a n="reason">02</a>
                            <a n="financeType">02</a>
                            <a n="currency">RUB</a>
                            <c n="Consumer">
                                <s>
                                    <a n="applicantType">01</a>
                                    <a n="name1">'.$userData['firstname'].'</a>
                                    <a n="name2">'.$userData['patronymic'].'</a>
                                    <a n="surname">'.$userData['lastname'].'</a>
                                    <a n="sex">'.($userData['gender'] == 'male' ? 1 : 0).'</a>
                                    <a n="dateOfBirth">'.$this->prepareDate($userData['birth']).'</a>
                                    <a n="nationality">RU</a>
                                    <a n="consentFlag">0</a>
                                    <a n="dateConsentGiven">'.$this->prepareDate(date('Y-m-d')).'</a>
                                    <a n="dateConsentExpiry">'.$this->prepareDate(date('Y-m-d', strtotime("+1 day"))).'</a>
                                    <a n="consentPurpose">02</a>
                                    <a n="consentPurposeDetails"></a>
                                    <a n="consentResponsibilityFlag">0</a>
                                    <a n="consentUserDetails">'.$userData['companyName'].'</a>
                                    <a n="primaryIDType">01</a>
                                    <a n="primaryID">'.str_replace('-', '', $userData['passport_serial']).'</a>
                                    <a n="primaryIDIssueDate">'.$this->prepareDate($userData['passport_date']).'</a>
                                    <a n="primaryIDIssuePlace">-</a>
                                    <a n="primaryIDAuthority">-</a>
                                    <a n="primaryIncome">0</a>
                                    <a n="primaryIncomeFreq">3</a>
                                    <c n="Address">
                                        <s>
                                            <a n="addressFlag">0</a>
                                            <a n="addressType">2</a>
                                            <a n="line1">-</a>
                                            <a n="line2">-</a>
                                            <a n="line3">-</a>
                                            <a n="country">RU</a>
                                        </s>
                                    </c>
                                </s>
                            </c>   
                        </s>
                        ';
        return $xmlString;
    }

    public function prepareDate($dateString){
        $time = strtotime($dateString);
        $newformat = date('Ymd',$time);
        return $newformat;
    }
    
        public function parseXml($xmlString){
        $xml = simplexml_load_string($xmlString);
        $userData = json_encode($xml);
        $newArr = json_decode($userData, true);
        return $newArr;
    }

    public function getDataFromXml($encodeFilePath){
        $encodededData = json_decode(file_get_contents($encodeFilePath));
        return $encodededData;

        // $parser = xml_parser_create();
        // xml_parse_into_struct($parser, $xmlstring, $vals, $index);
        // xml_parser_free($parser);

        // echo '<pre>';
        // var_dump($vals);
        // echo '</pre>';
    }

}
