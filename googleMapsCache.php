<?php
/**
 * Created by IntelliJ IDEA.
 * User: Joe
 * Date: Mar 6, 2010
 * Time: 4:48:00 PM
 */

define( 'BASEPATH', dirname(__FILE__) . "/" );

require_once( BASEPATH. "inc/classes/CreateFileCache.php");
require_once( BASEPATH. "inc/classes/GoogleMapsLocationCache.php");

$gmaps = new GoogleMapsLocationCache(
    "latest-GMaps-cache",
    "json",
    "latest-YQL-cache.json"
);
$gmaps->setUserAgent("Mozilla/5.0 (compatible; GoogleMaps-Cacher/1.0;)");

try{
    $gmaps->getCache();
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
?>