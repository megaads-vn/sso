<?php

namespace Megaads\Sso\Controllers;

class SsoController {
    
    public static function getRedirectUrl () {
        $callbackUrl = \Config::get('sso.callback_url');
        $encodedCallbackUrl = urlencode($callbackUrl);
        $redirectUrl = \Config::get('sso.server') . "/system/home/login?continue=$encodedCallbackUrl";
        return $redirectUrl;
    }

    public static function getUser ($token) {
        $retval = false;

        $getUserUrl = \Config::get('sso.server') . "/sso/auth?token=$token";
        $response = self::sendRequest($getUserUrl);
        $response = json_decode($response);

        if (isset($response->status) && $response->status == 'success'
        && (isset($response->code) && $response->code == 0)
         && (isset($response->user) && !empty($response->user))) {
            $retval =  $response->user;
        }

        return $retval;
    }

    public static function sendRequest ($url) {
        $retval = false;
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Cache-Control: no-cache",
            "Connection: keep-alive",
            "accept-encoding: gzip, deflate",
            "cache-control: no-cache",
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {

        } else {
            $retval = $response;
        }

        return $retval;
    }
}
