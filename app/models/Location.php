<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Location
 *
 * @author michael.hampton
 */
class Location
{

    /**
     *
     * @var type 
     */
    private $objDb;

    /**
     * 
     */
    public function __construct ()
    {
        $this->objDb = new Database();
        $this->objDb->connect ();
    }

    /**
     * 
     * @param type $searchText
     * @return boolean
     */
    public function locationSearch ($searchText)
    {
        $arrResults = $this->objDb->_query ("SELECT l.IL_NAME, c.IC_NAME, s.IS_NAME 
                                            FROM iso_location l
                                            INNER JOIN iso_subdivision s ON s.IS_UID = l.IS_UID
                                            INNER JOIN iso_country c ON c.IC_UID = l.IC_UID
                                            WHERE l.IL_NAME LIKE :searchText LIMIT 8000", [":searchText" => '%' . $searchText . '%']);

        if ( $arrResults === false )
        {
            return false;
        }

        if ( empty ($arrResults[0]) )
        {
            return [];
        }

        return $arrResults;
    }

    public function getLocation ($long, $lat)
    {
        try {

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim ($lat) . ',' . trim ($long) . '&sensor=false&key=AIzaSyA1COtE7cJxfGxNWiTjEgtSNhbvDBCiiqQ';

            $curlSession = curl_init ();
            curl_setopt ($curlSession, CURLOPT_URL, $url);
            curl_setopt ($curlSession, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt ($curlSession, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt ($curlSession, CURLOPT_BINARYTRANSFER, true);
            curl_setopt ($curlSession, CURLOPT_RETURNTRANSFER, true);

            $data = json_decode (curl_exec ($curlSession), true);

            $status = $data['status'];

            //if request status is successful
            if ( $status == "OK" )
            {
                //get address from json data

                $city = '';
                $country = '';

                foreach ($data["results"] as $result) {
                    foreach ($result["address_components"] as $address) {
                        if ( in_array ("locality", $address["types"]) )
                        {
                            $city = $address["long_name"];
                        }

                        if ( in_array ("country", $address["types"]) )
                        {
                            $country = $address["long_name"];
                        }
                    }
                }

                if ( trim ($country) !== 'United Kingdom' )
                {
                    return $city . "," . $country;
                }
                else
                {
                    return $city;
                }
            }
        } catch (Exception $ex) {
            
        }
    }

// function to retrieve lat and lang from address
    public function getLocationForAddress ($address)
    {

// urlencode(str) // for address
// $zip = 10001;
        try {
            $coordinates = @file_get_contents ('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode ($address) . '&sensor=true');

            $e = json_decode ($coordinates, true);

// call to google api failed so has ZERO_RESULTS -- i.e. rubbish address...
            if ( isset ($e->status) )
            {
                if ( $e->status == 'ZERO_RESULTS' )
                {
                    echo '1:';
                    $err_res = true;
                }
                else
                {
                    echo '2:';
                    $err_res = false;
                }
            }
            else
            {
                echo '3:';
                $err_res = false;
            }

// $coordinates is false if file_get_contents has failed so create a blank array with Longitude/Latitude.
            if ( $coordinates == false || $err_res == true )
            {
                $a = array('lat' => 0, 'lng' => 0);
                $coordinates = new stdClass();
                foreach ($a as $key => $value) {
                    $coordinates->$key = $value;
                }
            }
            else
            {
// call to google ok so just return longitude/latitude.
                $coordinates = $e;

                if ( !empty ($coordinates['results'][0]) )
                {
                    unset ($coordinates['results'][0]['address_components']);

                    return $coordinates['results'][0]['geometry']['location'];
                }
                
                return true;
            }

            return $coordinates;
// $lat = $coordinates->lat; //27.674426;
// $lng = $coordinates->lng; //85.312329;
        } catch (Exception $e) {
            
        }
    }

    /**
     * 
     * @return boolean
     */
    public function getLangauges ()
    {
        $arrResults = $this->objDb->_select ("list", "", [], "*", "value ASC");

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return $arrResults;
    }

    /**
     * 
     * @return boolean
     */
    public function getCountries ()
    {
        $arrResults = $this->objDb->_select ("iso_country", "", [], "*", "IC_NAME ASC");

        if ( $arrResults === false )
        {
            trigger_error ("Db query failed", E_USER_WARNING);
            return false;
        }

        return $arrResults;
    }

}
