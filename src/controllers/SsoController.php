<?php

namespace Megaads\Sso;

use Config;

class SsoController {
    public static function getRedirectUrl () {
        $httpHost = "http://{$_SERVER['HTTP_HOST']}";
        $callbackUrl = Config::get('sso.callback_url');
        $encodedCallbackUrl = urlencode($httpHost . $callbackUrl);
        $redirectUrl = Config::get('sso.server') . "/system/home/login?continue=$encodedCallbackUrl";
        return $redirectUrl;
    }

    public static function getUser ($token, $appId = 0) {
        $retval = false;

        if ($appId == 0 && Config::get('sso.app_id')) {
            $appId = Config::get('sso.app_id');
        }

        $ip = isset($_SERVER['REMOTE_ADDR']) ? urlencode($_SERVER['REMOTE_ADDR']) : '';
        $url = \Session::has('redirect_url') ? urlencode(\Session::get('redirect_url')) : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? urlencode($_SERVER['HTTP_USER_AGENT']) : '';
        $domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '');
        $domain = urlencode($domain);
        $getUserUrl = Config::get('sso.server') . "/sso/auth?token=$token&app_id=$appId&ip=$ip&url=$url&user_agent=$user_agent&domain=$domain";
        
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
          CURLOPT_SSL_VERIFYPEER => false
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
