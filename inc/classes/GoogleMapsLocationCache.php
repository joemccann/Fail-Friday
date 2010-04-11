<?php
/**
 * Created by IntelliJ IDEA.
 * User: Joe
 * Date: Mar 6, 2010
 * Time: 4:42:45 PM
 * To change this template use File | Settings | File Templates.
 */

class GoogleMapsLocationCache extends CreateFileCache {

    private $yqlFile_;
    private $gmapsFile_;
    private $banks_;
    private static $MAX_ROWS;

    public function __construct($filename = NULL, $type = NULL, $yqlFile = NULL) {
        parent::__construct($filename, $type);

        if (empty($yqlFile)) {
            throw new exception("Datafile is missing!");
        }

        $this->yqlFile_ = json_decode($this->loadCache($yqlFile));
        $this->gmapsFile_ = $this->loadCache($filename .".". $type);
        $this->banks_ = array();
        $this->init();
    }

    private function init(){
        $this->setMaxRows();


    }

    private function setMaxRows() {
        $this->MAX_ROWS = count( $this->yqlFile_->query->results->table->tbody->tr );
        $this->banks_ = $this->createBanksArray();

    }

    private function loadGmapData() {
        return json_encode($this->createLatLngArray());
    }

    // Return the json object from the Google Maps file.
    private function loadCache($file = NULL) {
        if (!file_exists($file)) {
            return false;
        }
        $fp = @fopen($file, "r");
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

    private function IsTimeForUpdate() {
        $goog = json_decode($this->gmapsFile_);
        return $goog[0]->MAX_ROWS_PRIOR == NULL ? 0 : $this->MAX_ROWS == $goog[0]->MAX_ROWS_PRIOR ;
    }

    private function createBanksArray() {
        $json = $this->yqlFile_;
        $banks = array();
        for ($i = 0; $i < $this->MAX_ROWS; $i++)
        {
            $tr = $json->query->results->table->tbody->tr[$i];
            $loc = array(
                "name" => $tr->td[0]->a->content,
                "city" => $tr->td[1]->p,
                "state" => $tr->td[2]->p,
                "certNumber" => $tr->td[3]->p,
                "closingDate" => $tr->td[4]->p,
                "dateUpdate" => $tr->td[5]->p);
            array_push($banks, $loc);
        }

        return $banks;

    }

    private function createCurrentMaxRowsEntry() {
        return array("MAX_ROWS_PRIOR" => $this->MAX_ROWS);
    }

    private function createLatLngArray() {
        $points = array();
        array_push($points, $this->createCurrentMaxRowsEntry());

        // We re-count here in case there was an error in the getLatLng call. MAX_ROWS would be error-prone.
        $MAX_POINTS = 3;// count($this->banks_);
        for ($i = 0; $i < $MAX_POINTS; $i++)
        {
            array_push($points, $this->getLatLng($this->banks_[$i]['city'] . "," . $this->banks_[$i]['state'], $i));
        }
        return $points;
    }

    private function getLatLng($address, $i) {
        // Spaces are no bueno in query.
        $q = str_replace(" ", "+", $address);
        if ($d = @fopen("http://maps.google.com/maps/api/geocode/json?address=$q&sensor=false", "r")) {
            $gMapsResponse = json_decode(@fread($d, 30000));
            @fclose($d);
            $point = array(
                "lat" => $gMapsResponse->results[0]->geometry->location->lat,
                "lng" => $gMapsResponse->results[0]->geometry->location->lng,
                "address" => $address,
                "queryAddress" => $q,
                "response" => $gMapsResponse->status,
                "name" => $this->banks_[$i]['name'],
                "closingDate" => $this->banks_[$i]['closingDate']
            );
        }
        else
        {
            $point = array(
                "lat" => null,
                "lng" => null,
                "address" => $address,
                "queryAddress" => $q,
                "response" => null,
                "name" => $this->banks_[$i]['name'],
                "closingDate" => $this->banks_[$i]['closingDate']
             );
        }
        return $point;
    }

    private function loadAndSave() {

        $data = $this->loadGmapData();
        if (!$data) {
            throw new Exception("ERROR: Unable to retrieve data from Google Maps.");
        }
        else {
            $this->saveCache($data);
            return $data;
        }
    }

    public function getCache() {

                    try {
                return $this->loadAndSave();
            }
            catch (Exception $e) {
                return $e->getMessage();
            }
        
        // Cast to int because we are returning a "boolean" integer from the method call.
        if ( (int)$this->IsTimeForUpdate() !== 1) {
            try {
                return $this->loadAndSave();
            }
            catch (Exception $e) {
                return $e->getMessage();
            }
        }
        else {
            return $this->readCache();
        }


    }
}
