<?php

namespace Megaads\Sso;

use Config;

class SsoController {
    public static function getRedirectUrl () {
        $callbackUrl = Config::get('sso.callback_url');
        $encodedCallbackUrl = urlencode($callbackUrl);
        $redirectUrl = Config::get('sso.server') . "/system/home/login?continue=$encodedCallbackUrl";
        return $redirectUrl;
    }

    public static function getUser ($token, $appId = 0) {
        $retval = false;

        if ($appId == 0 && Config::get('sso.app_id')) {
            $appId = Config::get('sso.app_id');
        }

        $getUserUrl = Config::get('sso.server') . "/sso/auth?token=$token&app_id=$appId";
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
