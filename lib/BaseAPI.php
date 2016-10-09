<?php

namespace cryptonator;

use cryptonator\exceptions\HashError;

require_once __DIR__ . "/exceptions.php";

class BaseAPI
{
    const API_URL = 'https://api.cryptonator.com/api/merchant/v1/';

    protected function sendReqest($url, $options = array())
    {
        $url = self::API_URL . $url;
        $query = http_build_query($options);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Merchant.SDK/PHP');

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 2);

        $body = curl_exec ($curl);

        $result = new \StdClass();
        $result->status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result->body = json_decode($body, true);

        curl_close($curl);

        return $this->processResult($result);
    }

    protected function processResult($result)
    {
        if ($result->status_code != 400) {
            return $result->body;
        }
        else {
            throw new Exceptions\ServerError($result->body['error'], $result->status_code);
        }
    }
}