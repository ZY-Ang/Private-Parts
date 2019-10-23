<?php
require_once('../config.php');
require_once('../mysqli.php');

use duzun\hQuery;

include_once('hquery.php');

hQuery::$cache_path = './cache';
hQuery::$cache_expires = 3600;

$db = new DB(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$query = $db->query("SELECT * FROM web_url WHERE scraped = '0' LIMIT 50");

foreach ($query->rows as $result) {
    try {
        $doc = hQuery::fromUrl($result['url'], ['Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8']);
        
        if ($doc) {
            $urls = $doc->find('a[href]');
        } else {
            $db->query("UPDATE web_url SET scraped = '1', date_scraped = NOW() WHERE web_url_id = '" . (int)$result['web_url_id'] . "'");
        
            continue;
        }
    } catch (Exception $e) {
        $db->query("UPDATE web_url SET scraped = '1', date_scraped = NOW() WHERE web_url_id = '" . (int)$result['web_url_id'] . "'");
        
        continue;
    }

    if ($urls) {
        foreach ($urls as $url1) {
            $url_data = explode('#', $url1->attr('href'));
            $url_data = $url_data[0];
            
            $check_query = $db->query("SELECT * FROM web_url WHERE url = '" . $db->escape($url_data) . "'");
            
            if (!$check_query->num_rows) {
                $db->query("INSERT INTO web_url SET url = '" . $db->escape($url_data) . "', scraped = '0', date_scraped = NULL");
            }
        }
    }

    $db->query("INSERT INTO web_data SET url = '" . $db->escape($result['url']) . "', html = '" . $db->escape($doc->html()) . "'");
    $db->query("UPDATE web_url SET scraped = '1', date_scraped = NOW() WHERE web_url_id = '" . (int)$result['web_url_id'] . "'");
}