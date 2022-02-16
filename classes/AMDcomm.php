<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AMDcomm
 *
 * @author nikpapa
 */
class AMDcomm {
    //put your code here
    const APPLICATION_ID = '5c5d5e28e4b0bae5f4accfec';
    const APPLICATION_SECRET = 'MGkNfqGud0';
    const AUTHENTICATION_URL = 'https://auth.routee.net/oauth/token';
    const SMS_URL = 'https://connect.routee.net/sms';
    public $body;
    public $recipient;
    
    function __construct($to, $message) {
        $this->body = $message;
        $this->recipient = $to;
    }
    
    /*
     * This method builds necessary query parameters in order to authorize application
     * to use sms service. Extra curl parameters are sent to RemoteCall class 
     */
    private function authorization(){
        $fields = ['grant_type' => 'client_credentials', 'scope' => 'sms'];
        $authorize = new RemoteCall(self::AUTHENTICATION_URL, $fields, 'POST');
        $authorizationResponse = $authorize->performCurl([
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_HTTPHEADER => [
                            "authorization: Basic ". base64_encode(self::APPLICATION_ID.":".self::APPLICATION_SECRET),
                            "content-type: application/x-www-form-urlencoded"
                        ]]);
        return json_decode($authorizationResponse);
    }
   
    /*
     * This method builds necessary query parameters to send sms to the recipient.
     * Extra curl parameters are sent to RemoteCall class.
     */
    public function sendSMS(){
        $authorizationResponse = $this->authorization();
        if(isset($authorizationResponse->access_token)){
            $fields = "{ \"body\": \"$this->body\",\"to\" : \"$this->recipient\",\"from\": \"amdTelecom\"}";
            $smsSend = new RemoteCall(self::SMS_URL, $fields, 'POST');
            $smsSendResponse = $smsSend->performCurl([
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_HTTPHEADER => [
                                "authorization: Bearer ".$authorizationResponse->access_token,
                                "content-type: application/json"
                              ]]);
            return json_decode($smsSendResponse);
        }else{
            return false;
        }
        
    }

    
}
