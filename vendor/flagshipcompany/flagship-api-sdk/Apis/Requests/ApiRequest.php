<?php

namespace Flagship\Apis\Requests;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\FilterException;

abstract class ApiRequest{

    protected function api_request(string $url,array $json,string $apiToken,string $method, int $timeout, string $flagshipFor=null, string $version=null) : array

    {
        $curl = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS  => json_encode($json),
            CURLOPT_HTTPHEADER => array(
                "X-Smartship-Token: ". $apiToken,
                "Content-Type: application/json",
                "X-F4".$flagshipFor."-Version: ".$version
                )
            ];

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl,CURLINFO_HTTP_CODE);

        $responseArray = [
            "response"  => json_decode($response),
            "httpcode"  => $httpcode
        ];

        curl_close($curl);

        if(($httpcode >= 400 && $httpcode < 600) || ($httpcode === 0) || ($response === false) || ($httpcode === 209)){
            throw new ApiException($response,$httpcode);
        }

        return $responseArray;
    }

    protected function addRequestFilter($key,$value){

        if(in_array($key,$this->filters)){
            $this->url = $this->url.'?'.$key.'='.$value;
            return $this;
        }
        throw new FilterException("Invalid filter argument provided");
    }

}
