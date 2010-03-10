<?php
/**
 * Created by IntelliJ IDEA.
 * User: Joe
 * Date: Mar 6, 2010
 * Time: 4:42:45 PM
 * To change this template use File | Settings | File Templates.
 */

class GoogleMapsLocationCache extends CreateFileCache {

    private $apikey_;
    private $datafile_;
    private $yqlFile_;
    private $gmapsFile_;

    // API Key no longer needed thx to new Geocoding web service?
    public function __construct($filename = NULL, $type = NULL, $apikey = NULL, $yqlFile = NULL)
    {
        parent::__construct($filename, $type);
        if( empty($apikey) )
        {
            throw new exception("API Key is missing!");
        }

        if( empty($yqlFile) )
        {
            throw new exception("Datafile is missing!");
        }

        $this->apikey_ = $apikey;
        $this->yqlFile_ = $this->loadCache( $yqlFile );
        $this->gmapsFile_ = $this->loadCache( $filename.$type );
    }

    private function loadGmapData ( ) {
        return json_encode( $this->createLatLngArray() );
	}

    // Return the json object from the YQL file.
    private function loadCache($file = NULL)
    {
        if (!file_exists( $file )) {
            return false;
        }
        $fp = @fopen( $file, "r");
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

    // TODO implement this
    private function isTimeForUpdate()
    {
        return true;
    }

    private function createBanksArray()
    {
        $json = json_decode($this->yqlFile_);
        $banks = array();
        $MAX_ROWS = count( $json->query->results->table->tbody->tr );

        for ($i = 0; $i < $MAX_ROWS; $i++)
            {
                $tr = $json->query->results->table->tbody->tr[$i];
                $loc = array(
                    "name" => $tr->td[0]->a->content,
                    "city"=> $tr->td[1]->p,
                    "state"=> $tr->td[2]->p,
					"certNumber"=> $tr->td[3]->p,
					"closingDate"=> $tr->td[4]->p,
                    "dateUpdate"=> $tr->td[5]->p);
                array_push($banks, $loc);
            }

        return $banks;

    }

    private function createLatLngArray()
    {
        $banks = $this->createBanksArray();
        $points = array();
        $MAX_POINTS = count( $banks );
        for($i=0; $i < $MAX_POINTS; $i++)
        {
            array_push($points, $this->getLatLng( $banks[$i]['city'].",".$banks[$i]['state'] ));
            //sleep(1);
        }
        return $points;
    }

    private function getLatLng($address)
    {
      // Spaces are no bueno in query.
      $q = str_replace(" ", "+", $address);
      if ($d = @fopen("http://maps.google.com/maps/api/geocode/json?address=$q&sensor=false", "r"))
      {
        $gMapsResponse = json_decode( @fread($d, 30000) );
        @fclose($d);
        $point = array(
            "lat"=>$gMapsResponse->results[0]->geometry->location->lat,
            "lng" => $gMapsResponse->results[0]->geometry->location->lng,
            "address" => $address,
            "queryAddress" => $q,
            "response" => $gMapsResponse->status);
      }
      else
      {
          $point = array(
              "lat"=>null,
              "lng" => null,
              "address" => $address,
              "queryAddress" => $q,
              "response" => null);
      }
      return $point;
    }

	private function loadAndSave ( ) {

		$data = $this->loadGmapData();
		if(!$data){
			throw new Exception("ERROR: Unable to retrieve data from Google Maps.");
		}
		else {
			$this->saveCache( $data );
			return $data;
		}
	}

    public function getCache()
    {
        // TODO:  Change this to the last updated.
        $json = json_decode($this->yqlFile_);
        $lastFailure = $json->query->results->table->tbody->tr[0]->td[0];
        //$lastDate->headers->p;
		$last = $this->getCacheLastModified();
		$now = time();

		if ( !$last || (( $now - $last ) > parent::$REFRESH_INTERVAL) ) {
			try { return $this->loadAndSave(); }
			catch ( Exception $e ) { return $e->getMessage(); }
		}
		else {
			return $this->readCache();
		}


    }
}
