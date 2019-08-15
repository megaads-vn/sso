<?php

namespace Megaads\Sso;

use Config;

class SsoController {
    public static function getRedirectUrl () {
        $callbackUrl = Config::get('sso.callback_url');
        $encodedCallbackUrl = urlencode($callbackUrl);
        $redirectUrl = Config::get('sso.server') . '' . Config::get('sso.login_path') . "?continue=$encodedCallbackUrl";
        return $redirectUrl;
    }

    public static function getUser ($token, $appId = 0) {
        $retval = false;
        $params = Config::get('sso.auth_params');
        $urlParams = '';
        $getUserUrl  = Config::get('sso.server');
        $authPath = Config::get('sso.auth_path');
        $getUserUrl .= $authPath;
        if ( count($params) > 0 ) {
            foreach ( $params as $key => $val ) {
                if ( $key == 'token' && $val == '' ) {
                    $val = $token;
                }
                if ( $key == 'app_id' && $val == '' ) {
                    $val = $appId;
                }
                $urlParams .= $key . '=' . $val . '&';
            }
            $urlParams = rtrim($urlParams,'&');
            $getUserUrl .= '?' . $urlParams;
        } 
        $response = self::sendRequest($getUserUrl);
        $response = json_decode($response);

        if ($response && $response->status == 'success' && $response->code == 0 && $response->user) {
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
