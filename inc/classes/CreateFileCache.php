<?php
/**
 * Created by IntelliJ IDEA.
 * User: Joe
 * Date: Mar 6, 2010
 * Time: 11:19:02 AM
 * @abstract
 */

abstract class CreateFileCache {
    // Force Extending class to define this method
    abstract protected function getCache();

    protected static $REFRESH_INTERVAL = 60;

   	private $filename_;
    private $response_;
    private $type_;
    private $url_;
    private $userAgent_;

    public function __construct ( $filename = NULL, $type = NULL) {
		if( empty($filename)) {
			throw new Exception("Filename cannot be empty!");
		}

        if( empty($type)) {
            throw new Exception("Data type must by json or xml!");
        }

		$this->filename_ = $filename;
        $this->type_ = $type;

		$this->response_ = array();
        $this->userAgent = '';
        $this->url_ = '';

    }

    public function setUserAgent($value = NULL){
		if ( empty($value) ) {
			throw new Exception("User-Agent cannot be empty!");
		}
        $this->userAgent_ = $value;
    }

    public function getUserAgent(){
        return $this->userAgent_;
    }

    public function readCache() {

        $cacheFile = $this->filename_ . "." . $this->type_;
        if (!file_exists($cacheFile)) {
            return false;
        }

        $fp = @fopen($cacheFile, "r");
        $buffer = "";

        if (!$fp) {
            return false;
        }
        else {
            while (!feof($fp)) {
                $buffer .= fgets($fp, 4096);
            }
        }

        fclose($fp);
        return $buffer;

    }

	protected function saveCache ( $data ) {

		$cacheFile = $this->filename_.".".$this->type_;

		$fp = @fopen($cacheFile,"w");

		if(!$fp){
			return false;
		}

		fwrite($fp,$data);

		fclose($fp);

	}

	protected function getCacheLastModified ( ) {

		$cacheFile = $this->filename_.".".$this->type_;
		return @filemtime($cacheFile);
    }

    public function loadUrl($url, $postargs = false, $suppressResponse = false) {

        $url = ($suppressResponse) ? $url . '&suppress_response_code=true' : $url;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent_);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        $this->response_ = curl_getinfo($ch);
        curl_close($ch);

        if (intval($this->response_['http_code']) == 200) {
            return $response;
        }
        else {
            return false;
        }

    }


}
