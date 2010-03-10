<?php

/**
 * Mobile Detect
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

class MobileCheck {
    
    protected $accept;
    protected $userAgent;
    
    protected $isMobile     = false;
    protected $isAndroid    = null;
    protected $isBlackberry = null;
    protected $isOpera      = null;
    protected $isPalm       = null;
    protected $isWindows    = null;
    protected $isGeneric    = null;

		// Joe McCann added check for Palm Pre and Opera 6.0

    protected $devices = array(
        "android"       => "android",
        "blackberry"    => "blackberry",
        "iphone"        => "(iphone|ipod)",
        "opera"         => "Opera 6.0",
        "palm"          => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino|pre)",
        "windows"       => "windows ce; (iemobile|ppc|smartphone)",
        "generic"       => "(kindle|mobile|mmp|midp|o2|pda|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap)"
    );


    public function __construct() {
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->accept    = $_SERVER['HTTP_ACCEPT'];

        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])|| isset($_SERVER['HTTP_PROFILE'])) {
            $this->isMobile = true;
        } elseif (strpos($this->accept,'text/vnd.wap.wml') > 0 || strpos($accept,'application/vnd.wap.xhtml+xml') > 0) {
            $this->isMobile = true;
        } else {
            foreach ($this->devices as $device => $regexp) {
                if ($this->isDevice($device)) {
                    $this->isMobile = true;
                }
            }
        }
    }


    /**
     * Overloads isAndroid() | isBlackberry() | isOpera() | isPalm() | isWindows() | isGeneric() through isDevice()
     *
     * @param string $name
     * @param array $arguments
     * @return bool
     */
    public function __call($name, $arguments) {
        $device = substr($name, 2);
        if ($name == "is" . ucfirst($device)) {
            return $this->isDevice($device);
        } else {
            trigger_error("Method $name not defined", E_USER_ERROR);
        }
    }


    /**
     * Returns true if any type of mobile device detected, including special ones
     * @return bool
     */
    public function isMobile() {
        return $this->isMobile;
    }


    protected function isDevice($device) {
        $var    = "is" . ucfirst($device);
        $return = $this->$var === null ? (bool) preg_match("/" . $this->devices[$device] . "/i", $this->userAgent) : $this->$var;

        if ($device != 'generic' && $return == true) {
            $this->isGeneric = false;
        }

        return $return;
    }
}