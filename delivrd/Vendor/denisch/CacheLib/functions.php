<?php

function writeCache($content, $filename){
        $fp = fopen($filename, 'w');
        fwrite($fp, $content);
        fclose($fp);
}

function readCache($filename, $expiry){
    if (file_exists($filename)) {
        if ((time() - $expiry) > filemtime($filename))
            return FALSE;
        $cache = file($filename);
        return implode('', $cache);
    }
    return FALSE;
}

function keepTimeToUnux($time) {
    $time = ($time + 21564000)*60;
    return $time;
}

function keepTimeToDate($time) {
    $time = ($time + 21564000)*60;

    return date('Y-m-d H:i:s', $time);
}

function utf8replacer($captures) {
  if ($captures[1] != "") {
    // Valid byte sequence. Return unmodified.
    return $captures[1];
  }
  elseif ($captures[2] != "") {
    // Invalid byte of the form 10xxxxxx.
    // Encode as 11000010 10xxxxxx.
    return "\xC2".$captures[2];
  }
  else {
    // Invalid byte of the form 11xxxxxx.
    // Encode as 11000011 10xxxxxx.
    return "\xC3".chr(ord($captures[3])-64);
  }
}

function xml2array($xml){
    $xml = preg_replace('/(<\/?)\w+:([^>]*>)/', '$1$2', $xml); //get rid of namespaces
    $xml = simplexml_load_string($xml);
    return json_decode(json_encode($xml),true); //use built-in stuff
}

function generateRandomString($length = 10, $type=0){
    if($type == 0) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    } elseif($type == 1) {
        $characters = '0123456789';
    } elseif($type == 2) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function plusMonth($date) {
    $day = date('j', strtotime($date));

    $month = date('n', strtotime($date)) + 1;
    $year = date('Y', strtotime($date));
    if($month > 12) {
        $month = 1;
        $year = $year + 1;
    }
    $h = date('H:i:s');
    if(date('t', strtotime($year .'-'. $month)) < $day) {
        $day = 1;
        $h = '00:00:00';
        $month = $month + 1;
        if($month > 12) {
            $month = 1;
            $year = $year + 1;
        }
    }

    return date('Y-m-d H:i:s', strtotime($year .'-'. $month .'-'. $day .' '. $h));
}