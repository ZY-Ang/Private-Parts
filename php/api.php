<?php
require_once('config.php');
require_once('mysqli.php');

header('Content-Type: application/json');

$db = new DB(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

function sendRequest($header_data, $post_data, $url) {
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'CS3235 Project');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, $post_data ? true : false);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);

    if ($header_data) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
    }

    if ($post_data) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    }

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}

$json = array();

$name = isset($_POST['name']) ? $_POST['name'] : '';
$facebook = !empty($_SESSION['fb_email']) ? $_SESSION['fb_email'] : '';
$instagram = isset($_POST['instagram']) ? $_POST['instagram'] : '';

$db->query("INSERT INTO session SET name = '" . $db->escape($name) . "', instagram = '" . $db->escape($instagram) . "', facebook = '" . (int)$facebook . "', date_added = NOW()");

$session_id = $db->getLastId();

$emails = array();

if (isset($_POST['email'])) {
    $emails[] = $_POST['email'];
}

if (isset($_SESSION['fb_email'])) {
    $emails[] = $_SESSION['fb_email'];
}

$emails = array_unique($emails);

// HIBP email search
foreach ($emails as $email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $header_data = array(
            'hibp-api-key:' . HIBP_KEY
        );
        
        $response = sendRequest($header_data, false, 'https://haveibeenpwned.com/api/v3/breachedaccount/' . $email . '?truncateResponse=false');
        
        $response = json_decode($response, true);
        
        if (is_array($response)) {
            foreach ($response as $result) {
                $data = array(
                    'title'         => $result['Title'],
                    'domain'        => $result['Domain'],
                    'breach_date'   => $result['BreachDate'],
                    'description'   => $result['Description'],
                    'logo'          => $result['LogoPath'],
                    'lost_data'     => $result['DataClasses']
                );
            
                $db->query("INSERT INTO hibp_data SET session_id = '" . (int)$session_id . "', email = '" . $db->escape($email) . "', data = '" . $db->escape(json_encode($data)) . "'");
            }
        }
        
        $response = sendRequest($header_data, false, 'https://haveibeenpwned.com/api/v3/pasteaccount/' . $email);

        $response = json_decode($response, true);
        
        if (is_array($response)) {
            foreach ($response as $result) {
                $data = array(
                    'source'        => $result['Source'],
                    'title'         => $result['Title'],
                    'date'          => $result['Date'],
                    'email_count'   => $result['EmailCount']
                );
            
                $db->query("INSERT INTO hibp_data SET session_id = '" . (int)$session_id . "', email = '" . $db->escape($email) . "', data = '" . $db->escape(json_encode($data)) . "'");
            }
        }
    }
}

if ($instagram) {
    $instagram_html = @file_get_contents('https://www.instagram.com/' . $instagram . '/');
    
    preg_match('/_sharedData = ({.*);<\/script>/', $instagram_html, $matches);

    if (isset($matches[1])) {
        $profile_data = json_decode($matches[1])->entry_data->ProfilePage[0]->graphql->user;
                
        $images = array();

        foreach ($profile_data->edge_owner_to_timeline_media->edges as $edge) {            
            $images[] = array(
                'image'     => $edge->node->display_url,
                'caption'   => isset($edge->node->edge_media_to_caption->edges[0]->node->text) ? $edge->node->edge_media_to_caption->edges[0]->node->text : '',
                'url'       => 'https://www.instagram.com/p/' . $edge->node->shortcode
            );
        }
        
        $data = array(
            'images'    => $images,
            'name'      => $profile_data->full_name,
            'biography' => $profile_data->biography
        );
        
        $db->query("INSERT INTO instagram_data SET session_id = '" . (int)$session_id . "', image = '" . $db->escape($profile_data->profile_pic_url_hd) . "', data = '" . $db->escape(json_encode($data)) . "'");
    }
}

$json['location'] = BASE . 'results.php?session_id=' . $session_id . '&signature=' . sha1($session_id . RESULTS_KEY . $session_id);

unset($_SESSION['fb_email']);
unset($_SESSION['fb_firstname']);
unset($_SESSION['fb_lastname']);

echo json_encode($json);