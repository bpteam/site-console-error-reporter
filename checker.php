<?php
/**
 * Created by PhpStorm.
 * User: iEC
 * Date: 3/22/2015
 * Time: 15:32
 * Project: site-console-error-reporter
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */
use GetContent\cPhantomJS;
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__.'/../_coolLib/loader/include.php');

$email = 'bpteam22@gmail.com';

$host = 'localhost';
$db = 'test';
$user = 'Z';
$password = '123456';

$mysqli = new mysqli($host,$user, $password, $db);
if ($mysqli->connect_error) {
	echo "MySQL connect error (" . $mysqli->connect_errno . ')' . $mysqli->connect_error;
	return false;
}
$mysqli->set_charset("utf8");

$phantom = new cPhantomJS;
$phantom->setOption(cPhantomJS::optIgnoreSslErrors, 'false');
$phantom->setOption(cPhantomJS::optLoadImages, 'true');
$fHead = fopen(__DIR__.'/links.txt', 'r');
$newErrors = [];
while($fHead && !feof($fHead)){
	$url = trim(fgets($fHead, 4096));
	$errors = json_decode($phantom->getErrors($url), true);
	foreach($errors as $error){
		$msg = implode(' ', $error);
		$sql = "INSERT INTO js_errors_alerts VALUES ('".md5($msg . $url)."', '".date('Y-m-d')."', '".$mysqli->escape_string($msg)."')";
		if($mysqli->query($sql)){
			$newErrors[$url][] = $msg;
		}
	}
}
$mysqli->close();

$text = '';
foreach($newErrors as $url => $messages){
	$text .= $url . "\n";
	$text .= implode("\n", $messages) . "\n";
}

if($text){
	mail($email, 'new JS errors ' . date('d.m.Y H:s'), $text);
}