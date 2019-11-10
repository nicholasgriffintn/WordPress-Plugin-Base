<?php
if (!defined('ABSPATH')) exit;
// Exit if accessed directly

if (!function_exists('NGRIFFIN_PLUGIN_TRACKING_IMG')) {
    function NGRIFFIN_PLUGIN_TRACKING_IMG($text)
    {
        //THIS RETURNS THE IMAGE
        header('Content-Type: image/gif');
        readfile('trackingimg.gif');

        // User Info Tracking
        $date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        // Query String Tracking
        $userID = '';
        if (isset($_GET["userID"])) {
            $userID = $_GET["userID"];
        }
        $eventCat = '';
        if (isset($_GET["eventCat"])) {
            $eventCat = $_GET["eventCat"];
        }
        $eventAction = '';
        if (isset($_GET["eventAction"])) {
            $eventAction = $_GET["eventAction"];
        }
        $eventLabel = '';
        if (isset($_GET["eventLabel"])) {
            $eventLabel = $_GET["eventLabel"];
        }

        // Check if GA tracking is off
        $sendToGA = true;
        if (isset($_GET["sendToGA"])) {
            $sendToGA = $_GET["sendToGA"];
        }

        exit;
    }
}