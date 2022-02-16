<?php

/**
 * Description of WeatherInfo
 *
 * @author nikpapa
 */
class WeatherInfo {

    const WEATHER_APP_ID = 'b385aa7d4e568152288b3c9f5c2458a5';
    const GEOLOCATION_URL = 'http://api.openweathermap.org/geo/1.0/direct';
    const WEATHER_URL = 'http://api.openweathermap.org/data/2.5/weather';
    public $city;
    public $temperature;
    
    function __construct($city, $temperature) {
        $this->city = $city;
        $this->temperature = $temperature;
    }
    
    /*
     * This method, builds necessary query parameters, in order to retrieve weather info
     * from the desired city, provided that valid coordinates have been received.
     * Returns an object containing weather info. In case of failure in retrieving 
     * coordinates, returns false.
     */
    public function getTemperature() {
        $geoLocation = $this->getGeoLocation();
        if($geoLocation){
            $weatherDataQuery = ['lat' => $geoLocation['lat'],
                                'lon' => $geoLocation['lon'],
                                'appid' => self::WEATHER_APP_ID,
                                'units' => 'metric'
                            ];

            $getWeatherCall = new RemoteCall(self::WEATHER_URL, $weatherDataQuery, 'GET');
            return json_decode($getWeatherCall->performCurl());
        }else{
            return false;
        }
    }
    
    /*
     * This method builds necessary query paramters, in order to retrieve coordinates
     * (latitude, longiitude) based on the specified city that the user has submitted.
     * Response, returns an array, in case of successfull retrieval, or false, if
     * no match is found.
     * On the initial implementation there was an iteration within array elements searching
     * for the specified city, and breaking if match was found.
     */
    public function getGeoLocation(){
        $geoLocationQuery = ['q' => $this->city,
                            'appid' => self::WEATHER_APP_ID
                        ];
        
        $geoData = new RemoteCall(self::GEOLOCATION_URL, $geoLocationQuery, 'GET');
        $getGeoCallParse = json_decode($geoData->performCurl());
        if(!empty($getGeoCallParse)) {
            return ['lat' => $getGeoCallParse[0]->lat, 'lon' =>$getGeoCallParse[0]->lon];
        }else{
            return false;
        }
        /*
        foreach($geoDataArray as $geoDataItem){
            if(strcasecmp($geoDataItem->name, $this->city) === 0){
                $this->latitude = $geoDataItem->lat;
                $this->longitude = $geoDataItem->lon;
                break;
            }
        }
         */
    }
    
}
