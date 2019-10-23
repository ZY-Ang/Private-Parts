<?php
require_once('config.php');

if (isset($_GET['code'])) {
    $redirect_url = rawurlencode(BASE . 'facebook.php');

    $url = 'https://graph.facebook.com/oauth/access_token?client_id=' . FB_APP_ID . '&client_secret=' . FB_APP_SECRET . '&code=' . $_GET['code'] . '&redirect_uri=' . $redirect_url;

    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_USERAGENT, BRAND_NAME . ' Facebook Login');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $url);

    $response = curl_exec($curl);

    curl_close($curl);

    $response = json_decode($response, true);

    if (!empty($response['access_token'])) {
        $url =  'https://graph.facebook.com/v2.12/me?fields=email,first_name,last_name&access_token=' . $response['access_token'];

        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_USERAGENT, BRAND_NAME . ' Facebook Login');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        if (!empty($response['email'])) {
            $_SESSION['fb_email'] = $response['email'];
            
            if (isset($response['first_name'])) {
                $_SESSION['fb_firstname'] = utf8_decode($response['first_name']);
            } else {
                $_SESSION['fb_firstname'] = '';
            }
            
            if (isset($response['last_name'])) {
                $_SESSION['fb_lastname'] = utf8_decode($response['last_name']);
            } else {
                $_SESSION['fb_lastname'] = '';
            }
        }
    }
    
    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $email = isset($_GET['email']) ? $_GET['email'] : '';
    $instagram = isset($_GET['instagram']) ? $_GET['instagram'] : '';
    
    $redirect_url = rawurlencode(BASE . 'index.php?step=5&name=' . rawurlencode($name) . '&email=' . rawurlencode($email) . '&instagram=' . rawurlencode($instagram));
    
    header('Location: ' . $redirect_url);
} else {
    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $email = isset($_GET['email']) ? $_GET['email'] : '';
    $instagram = isset($_GET['instagram']) ? $_GET['instagram'] : '';
    
    $redirect_url = rawurlencode(BASE . 'facebook.php?name=' . rawurlencode($name) . '&email=' . rawurlencode($email) . '&instagram=' . rawurlencode($instagram));
    
    header('Location: https://www.facebook.com/v2.12/dialog/oauth?client_id=' . FB_APP_ID . '&redirect_uri=' . $redirect_url . '&scope=email&display=page');
}