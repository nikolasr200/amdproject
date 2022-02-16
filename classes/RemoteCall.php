<?php

/**
 * Description of RemoteCall
 * This is a class that performs api calls to specified endpoints
 *
 * @author nikpapa
 * 
 * @property string $url
 * 
 */
class RemoteCall {
    
    const GET_METHOD = 'GET';
    private $url;
    private $parameters;
    private $method;
    
    function __construct($url, $parameters, $method) {
        $this->url = $url;
        $this->method = $method;
        if(is_array($parameters)){
            $this->parameters = http_build_query($parameters);
        }else{
            $this->parameters = $parameters;
        }
    } 
    
    /*
     * This method performs the remote connection and retrieval of the response,
     * @var $options holds extra curl parameters that need to be applied to the request
     */
    public function performCurl($options = []){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $this->url.(($this->method === self::GET_METHOD)?'?'.$this->parameters:''));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        
        foreach($options as $optionIndex => $optionValue){
            curl_setopt($ch, $optionIndex, $optionValue);
        }
        
        if($this->method !== 'GET' && !empty($this->parameters)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->parameters);
        }
        
        $response = curl_exec($ch);
        if(curl_error($ch)){
            $response = json_encode(['errorCode' => -1, 'errorMessage' => 'Request Error:' . curl_error($ch)]);
        }
        curl_close($ch);
        return $response;
    }
    
    
}
