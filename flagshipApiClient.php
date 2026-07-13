<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class FlagshipApiClient
{

    public function __construct(
        private string $token, 
        private string $baseUrl, 
        private string $storeName = '',
        private string $platformVersion = ''
    ){
    }

    public function getAvailableServices()
    {
        $response = $this->doRequest('GET', '/ship/available_services');
        return $response['response'];
        
    }

    public function validateToken(string $token): bool
    {
        $response = $this->doRequest('GET', "/check-token", ['token' => $token]);
        
        return $response['http_code'] === 200;
    }

    public function createQuote(array $payload)
    {
        $response = $this->doRequest('POST', '/ship/rates', ['payload' => $payload]);
        return $response['response'];
        
    }

    public function prepareShipment(array $payload)
    {
        
        $response = $this->doRequest('POST', '/ship/prepare', ['payload' => $payload]);
        return $response['response'];
            
    }

    public function updateShipment(int $shipmentId, array $payload)
    {
        
        $response = $this->doRequest('PUT', "/ship/shipments/{$shipmentId}", ['payload' => $payload]);
        return $response['response'];
        
    }

    public function getShipmentById(int $shipmentId)
    {
        
        $response = $this->doRequest('GET', "/ship/shipments/{$shipmentId}");
        return $response['response'];
        
    }

    public function packingRequest($payload)
    {
        $response = $this->doRequest('POST', '/ship/packing', ['payload' => $payload]);
        return $response['response'];
     
    }

    private function doRequest($method, $path, $data = [])
    {
        $url = $this->baseUrl.$path;
        $token = !empty($data['token']) ? $data['token'] : $this->token;
        $curl = curl_init();

        $headers = [
            'X-Smartship-Token: '.$token,
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        ];

        if(!empty($data['payload'])) {
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_POSTFIELDS] = json_encode($data['payload'], 
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        $httpStatus = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($errno !== 0 || ($body === false && $httpStatus >= 400)) {
            echo $error;
            return $error;
        }
        
        return [
            'response' => !empty($body) ? json_decode($body, true)['content'] : [],
            'http_code' => $httpStatus
        ];
    }
}