<?php
ob_start("ob_gzhandler");
require_once ('inc/classes/Mobilecheck.php');

$detect = new MobileCheck();
$page = 'index';

?>
<?php
if ($detect->isMobile()) 
{
    include ('inc/doctype-mobile.inc');
    include ('inc/head-mobile.inc');
    include ('inc/mobile.php');
} 
else 
{
    include ('inc/doctype-standard.inc');
    include ('inc/head-standard.inc');
    include ('inc/standard.php');
}

ob_flush();
?>
