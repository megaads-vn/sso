<?php 

Route::filter('sso', function() {
    if (Config::get("sso.active")) {
        $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        \Session::put("redirect_url", $currentUrl);
        $validToken = \Megaads\Sso\SsoController::ssoTokenValidation();
        if (!$validToken) {
            return Redirect::to("/system/home/login");
        }
    }
});