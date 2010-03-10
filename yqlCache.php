<?php
/**
 * Created by IntelliJ IDEA.
 * User: Joe
 * Date: Mar 6, 2010
 * Time: 12:47:11 PM
 */

define( 'BASEPATH', dirname(__FILE__) . '/' );

require_once( BASEPATH. "inc/classes/CreateFileCache.php");
require_once( BASEPATH. "inc/classes/YqlBankCache.php");

$yql = new YqlFileCache("latest-YQL-cache", "json", 'http://query.yahooapis.com/v1/public/yql?q=SELECT%20*%20FROM%20html%20WHERE%20url%3D%22http%3A%2F%2Fwww.fdic.gov%2Fbank%2Findividual%2Ffailed%2Fbanklist.html%22%20AND%20xpath%3D%22%2F%2Ftable%5B%40id%3D\'table\'%5D%22&format=json');
$yql->setUserAgent("Mozilla/5.0 (compatible; YQL-Cacher/1.0;)");

try{
    $yql->getCache();
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}


