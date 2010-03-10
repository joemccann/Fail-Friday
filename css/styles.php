<?php
if (extension_loaded('zlib'))
{
    ob_start('ob_gzhandler');
}

header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");

$offset = 60*60;
$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s", time()+$offset)." GMT";

header($ExpStr);

ob_start("compress");
function compress($buffer)
{
    // remove comments
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    return $buffer;
}

include ('reset.css');
include ('type.css');
include ('header.css');
include ('debug.css');
include ('global.css');

if (extension_loaded('zlib'))
{
    ob_end_flush();
}


?>
