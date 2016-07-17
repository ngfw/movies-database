<?php

/**
* Author Nick Gejadze
* this is not a admin panel, just simple redirect 
*/

$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";

if ($_SERVER["SERVER_PORT"] != "80"):
	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
else:
    $pageURL .= $_SERVER["SERVER_NAME"];
endif;
$lang = "en";
if(isset($_COOKIE["lang"]) and strlen($_COOKIE['lang']) == 2):
	$lang = $_COOKIE["lang"];
endif;
header("Location: ". $pageURL."/".$lang."/admin");

exit();