<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe.mccann
 * Date: Mar 5, 2010
 * Time: 4:59:24 PM
 * To change this template use File | Settings | File Templates.
 * Copyright (c) 2010 Joe McCann
 * http://www.subprint.com
 */


class YqlFileCache extends CreateFileCache{

	public function __construct ( $filename = NULL, $type = NULL, $url = NULL ) {
        // Similar to super in Java.
        parent::__construct($filename, $type);
        if( empty($url)) {
            throw new Exception("Url can't be empty!");
        }
        $this->url_ = $url;
	}

	private function loadYql ( ) {
		return $this->loadUrl($this->url_);
	}

	private function loadAndSave ( ) {

		$data = $this->loadYql();
		if(!$data){
			throw new Exception("ERROR: Unable to retrieve data via YQL.");
		}
		else {
			$this->saveCache( $data );
			return $data;
		}
	}

	public function getCache ( ) {

		$last = $this->getCacheLastModified();
		$now = time();

		// If the cache file dosen't exist, or if the last time
		// the cache was refreshed was more than REFRESH_INTERVAL
		// seconds ago, then ask service for the latest version.
		if ( !$last || (( $now - $last ) > parent::$REFRESH_INTERVAL) ) {
			// The cache is older than our threshold, so load
			// the latest content from Twitter.
			try { return $this->loadAndSave(); }
			catch ( Exception $e ) { return $e->getMessage(); }
		}
		else {
			// Return what's in the cache file.
			return $this->readCache();
		}

	}
}
?>