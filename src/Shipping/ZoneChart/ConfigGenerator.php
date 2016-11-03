<?php /** @copyright Alejandro Salazar (c) 2016 */
namespace Shipping\ZoneChart;

/**
 * The <kbd>ConfigGenerator</kbd>
 *
 * @author Alejandro Salazar (alphazygma@gmail.com)
 * @version 1.0
 * @package Shipping
 * @subpackage ZoneChart
 */
class ConfigGenerator
{
    const DATA_DIRNAME = '_data'; 
    
    /**
     * Retrieve the File path to the supplied zipcode 
     * @param string $zipcode
     * @return string
     */
    public static function getDataPath($zipcode)
    {
        $zipcodeFirst3 = $zipcode;
        if (strlen($zipcode > 3)) {
            $zipcodeFirst3 = substr($zipcode, 0, 3);
        }
        
        return __DIR__ . '/' . self::DATA_DIRNAME . '/' . $zipcodeFirst3 . '.json';
    }
    
    /**
     * Takes care of Calling USPS website to retrieve ZoneChart data for all possible zones (between
     * 1 to 1000) and store the data in a JSON config file.
     */
    public function generate()
    {
        for ($zone = 1, $i = 0; $zone < 1000; $zone++) {
            // Creating the sanitized zipcode first 3 digits
            $zipcodeFirst3 = str_pad($zone, 3, '0', STR_PAD_LEFT);
            
            // Retrieving the USPS response and parsing it into a map
            $uspsResponse = $this->_requestZip($zipcodeFirst3);
            $struct = $this->_parseUSPSResponse($uspsResponse);
            
            // If the parsed structure is empty, it was an invalid Zone
            if (empty($struct)) { continue; }
            
            // Now that we have a valid zone, write the config file
            $this->_writeJsonConfig($zipcodeFirst3, $struct);
            
            // Output of some progress (which Zone was processed
            if ($i != 0 && $i % 25 == 0) { echo "\n"; } // Just to help with progress readability
            echo $zipcodeFirst3, "\t";
            
            $i++; // Only used for progress printing
        }
        echo "\n";
        
        echo "Done\n";
    }

    /**
     * Retrieves the USPS website response for a chart based on the First 3 digits of a ZipCode.
     * @param string $zipcodeFirst3 First 3 digits of the ZipCode to retrieve zone map
     * @return string
     */
    private function _requestZip($zipcodeFirst3)
    {
        // Headers captured for valid request to USPS website https://postcalc.usps.com/Zonecharts/
        // This may need to be updated for subsequent calls to their website as USPS don't truly
        // expose an API that I could leverage
        $headers = [
            'Cache-Control'    => 'no-cache',
            'Origin'           => 'https://postcalc.usps.com',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-MicrosoftAjax'  => 'Delta=true',
            'User-Agent'       => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36',
            'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Accept'           => '*/*',
            'Referer'          => 'https://postcalc.usps.com/Zonecharts/',
            'Accept-Encoding'  => 'gzip, deflate, br',
            'Accept-Language'  => 'en-US,en;q=0.8',
            'Cookie'           => 'usps_id=01572a58bdad0008efa8dc0af5c405079004f07100fb8; ASP.NET_SessionId=3yphql45kvf1fq5534rudqax; utag_main=v_id:01572a58bdad0008efa8dc0af5c405079004f07100fb8$_sn:4$_ss:0$_st:1478114358337$_pn:4%3Bexp-session$ses_id:1478112481378%3Bexp-session; acs.t=%7B%22_ckX%22%3A1485888555205%2C%22rid%22%3A%22de35434-94555209-e4af-8931-9e441%22%2C%22cp%22%3A%7B%22code_version%22%3A%2219.0.34%22%2C%22env%22%3A%22prd%22%7D%7D; fsr.s=%7B%22v2%22%3A1%2C%22v1%22%3A1%2C%22rid%22%3A%22de35431-94242429-53df-3ea1-30d9c%22%2C%22ru%22%3A%22https%3A%2F%2Fwww.google.com%2F%22%2C%22r%22%3A%22www.google.com%22%2C%22st%22%3A%22%22%2C%22cp%22%3A%7B%22anyapp_outage%22%3A%22N%22%2C%22tracking_edw%22%3A%22false%22%7D%2C%22to%22%3A4%2C%22c%22%3A%22https%3A%2F%2Fwww.usps.com%2Fbusiness%2Fweb-tools-apis%2Fdocumentation-updates.htm%22%2C%22pv%22%3A7%2C%22lc%22%3A%7B%22d1%22%3A%7B%22v%22%3A7%2C%22s%22%3Atrue%7D%7D%2C%22cd%22%3A1%2C%22sd%22%3A1%2C%22mid%22%3A%22de35431-94242820-94b3-c72d-9dd8f%22%2C%22rt%22%3Afalse%2C%22rc%22%3Afalse%7D; _ga=GA1.3.1411765959.1473884242; _ga=GA1.2.1411765959.1473884242; NSC_dbmd-qspe-qptudbmd-mc=ffffffff3b22bf0e45525d5f4f58455e445a4a4212d3',
        ];
        
        // Form params captured for valid request to USPS website https://postcalc.usps.com/Zonecharts/
        // This may need to be updated for subsequent calls to their website as USPS don't truly
        // expose an API that I could leverage
        $bodyParams = [
            'ToolkitScriptManager1'                             => 'TabContainer1$TabPanel1$UpdatePanel1|TabContainer1$TabPanel1$ImageButton1',
            'ToolkitScriptManager1_HiddenField'                 => ';;AjaxControlToolkit, Version=3.5.60501.0, Culture=neutral, PublicKeyToken=28f01b0e84b6d53e:en-US:61715ba4-0922-4e75-a2be-d80670612837:475a4ef5:effe2a26:8e94f951:1d3ed089',
            'TabContainer1_ClientState'                         => '{"ActiveTabIndex":0,"TabState":[true,true]}',
            '__EVENTTARGET'                                     => '',
            '__EVENTARGUMENT'                                   => '',
            'TabContainer1$TabPanel1$TextBoxZipCode'            => $zipcodeFirst3,
            'TabContainer1$TabPanel2$TextBoxOriginZipCode'      => '',
            'TabContainer1$TabPanel2$TextBoxDestinationZipCode' => '',
            '__VIEWSTATE'                                       => '/wEPDwUKLTk0NTg5Mjc2Mg9kFgICBA9kFg4CAw9kFgJmDw9kFgQeC29ubW91c2VvdmVyBTJ0aGlzLnNyYz0nLi4vaW1hZ2VzL0J1dHRvbkN1c3RvbWVyU2VydmljZU92ZXIuanBnJx4Kb25tb3VzZW91dAUudGhpcy5zcmM9Jy4uL2ltYWdlcy9CdXR0b25DdXN0b21lclNlcnZpY2UuanBnJ2QCBQ9kFgJmDw9kFgQfAAUpdGhpcy5zcmM9Jy4uL2ltYWdlcy9CdXR0b25Nb2JpbGVPdmVyLmpwZycfAQUldGhpcy5zcmM9Jy4uL2ltYWdlcy9CdXR0b25Nb2JpbGUuanBnJ2QCBw8PFgIeC05hdmlnYXRlVXJsBRdodHRwczovL2RiY2FsYy51c3BzLmNvbWQWAmYPD2QWBB8ABSt0aGlzLnNyYz0nLi4vaW1hZ2VzL0J1dHRvbkJ1c2luZXNzT3Zlci5qcGcnHwEFJ3RoaXMuc3JjPScuLi9pbWFnZXMvQnV0dG9uQnVzaW5lc3MuanBnJ2QCCQ9kFgJmDw9kFgQfAAUxdGhpcy5zcmM9Jy4uL2ltYWdlcy9CdXR0b25Qb3N0YWxFeHBsb3Jlck92ZXIuanBnJx8BBS10aGlzLnNyYz0nLi4vaW1hZ2VzL0J1dHRvblBvc3RhbEV4cGxvcmVyLmpwZydkAgsPZBYCZg8PZBYEHwAFJ3RoaXMuc3JjPScuLi9pbWFnZXMvQnV0dG9uVVNQU092ZXIuanBnJx8BBSN0aGlzLnNyYz0nLi4vaW1hZ2VzL0J1dHRvblVTUFMuanBnJ2QCDw8PFgIeBFRleHQFEE5vdmVtYmVyIDEsIDIwMTZkZAIRD2QWAmYPZBYCZg9kFgICAQ9kFgJmD2QWBgIDDw9kFgIeCW9ua2V5ZG93bgWFAWlmICgoZXZlbnQud2hpY2ggJiYgZXZlbnQud2hpY2ggPT0gMTMpIHx8IChldmVudC5rZXlDb2RlICYmIGV2ZW50LmtleUNvZGUgPT0gMTMpKSB7IGdldFpvbmVDaGFydCgpOyByZXR1cm4gZmFsc2U7IH0gZWxzZSByZXR1cm4gdHJ1ZTtkAgcPDxYEHwIFI1pvbmVDaGFydFByaW50YWJsZS5hc3B4P3ppcGNvZGU9MDA2HgdWaXNpYmxlZ2RkAgkPZBYEZg8WAh8D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',
            '__VIEWSTATEGENERATOR'                              => '83D91D1F',
            '__EVENTVALIDATION'                                 => '/wEWBgKS3JadBAL4tdekDwLY9LmLDgKQm76JCwKmw4qOBwLT8or/BOvnXHLyr27kAWFK0s4Zc32huh+v',
            '__ASYNCPOST'                                       => 'true',
            'TabContainer1$TabPanel1$ImageButton1.x'            => '0',
            'TabContainer1$TabPanel1$ImageButton1.y'            => '0',
        ];
        
        $client   = new \GuzzleHttp\Client();
        $response = $client->post('https://postcalc.usps.com/Zonecharts/default.aspx', [
            'headers'     => $headers,
            'form_params' => $bodyParams,
        ]);
        
        return (string)$response->getBody();
    }
    
    /**
     * Parses the USPS string response into a Map containing the destination zip to zone list and
     * any exceptions.
     * @param string $uspsResponse
     * @return array Returns a Map with the following structure<br/>
     *      <pre>array(
     *      &nbsp   'destinationZipList' => array(
     *      &nbsp       // `r` = range, `z` = zone
     *      &nbsp       // When `r` contains a string, there is no range, is a zip to a zone
     *      &nbsp       ['r' => '005', 'z' => 7],
     *      &nbsp       // When `r` contains an array, then it is a range to a zone
     *      &nbsp       ['r' => ['683', '693'], 'z' => 8],
     *      &nbsp       ...
     *      &nbsp   ),
     *      &nbsp   'exceptionList' => array(
     *      &nbsp       ['r' => '95585', 'z' => 7],
     *      &nbsp       ['r' => ['96585', '98885'], 'z' => 8],
     *      &nbsp       ...
     *      &nbsp   )
     *      )</pre>
     */
    private function _parseUSPSResponse($uspsResponse)
    {
        // USPS response comes as a combination of some text and HTML, which unfortunately prevented
        // from easily wrap it in an SimpleXMLElement object so we could use XPATH, as such
        // we instead found the pattern of their response and used the regular expressions below
        // to extract the values.
        $responseLines = explode("\n", $uspsResponse);
        
        // Regular expressions to obtain the Regular ZipCode ranges to Zone, and the exceptions
        // As the time of this writing, (Nov 3rd, 2016), Regular ZipCode ranges are based on the
        // first 3 digits, while exceptions are full 5-digit zipcodes
        // Regular HTML looks like
        //      <td [param1="value1" ...]>123[---456]</td><td [param1="value1" ...]>5[+*]</td>
        // Exception HTML looks like
        //      <td [param1="value1" ...]>12345[---45678]</td><td [param1="value1" ...]>5[+*]</td>
        $regex          = '/<td[\w\d=":;# -]*>((\d{3,3})(---(\d{3,3}))?)<\/td><td[\w\d=":;# -]*>(\d)[+*]?<\/td>/';
        $exceptionRegex = '/<td[\w\d=":;# -]*>((\d{5,5})(---(\d{5,5}))?)<\/td><td[\w\d=":;# -]*>(\d)[+*]?<\/td>/';
        
        // Arrays to store the Ranges and the Zone associated to them
        $destinationZipList = [
            //['r' => '005', 'z' => 7],['r' => ['683', '693'], 'z' => 8], ...
        ];
        $exceptionList = [
            //['r' => ['96900', '96938'], 'z' => 8], ...
        ];
        
        foreach ($responseLines as $htmlLine) {
            $matches = null;
            
            $matchCount = preg_match_all($regex, $htmlLine, $matches);
            for ($i = 0; $i < $matchCount; $i++) {
                $zipStart = $matches[2][$i];
                $zipEnd   = $matches[4][$i];
                $zone     = $matches[5][$i];
                
                if (empty($zipEnd)) {
                    $destinationZipList[$zipStart] = ['r' => $zipStart, 'z' => $zone];
                } else {
                    $destinationZipList[$zipStart] = [
                        'r' => [$zipStart, $zipEnd],
                        'z' => $zone
                    ];
                }
            }
            
            $exceptionMatchCount = preg_match_all($exceptionRegex, $htmlLine, $matches);
            for ($i = 0; $i < $exceptionMatchCount; $i++) {
                $zipStart = $matches[2][$i];
                $zipEnd   = $matches[4][$i];
                $zone     = $matches[5][$i];
                
                if (empty($zipEnd)) {
                    $exceptionList[] = ['r' => $zipStart, 'z' => $zone];
                } else {
                    $exceptionList[] = [
                        'r' => [$zipStart, $zipEnd],
                        'z' => $zone
                    ];
                }
            }
        }
        
        // If there was no data parsed, then the zone was invalid which generated a response with no chart
        if (empty($destinationZipList) && empty($exceptionList)) {
            return null;
        }
        
        // Just for organizing the data a little, we sort the destinations by ZIP, not that it really
        // matters, but just in case someone wants to visually take a look at the generated files
        // the zones wouldn't be all over the place.
        ksort($destinationZipList);
        
        $struct = [
            'destinationZipList' => array_values($destinationZipList),
            'exceptionList'      => $exceptionList
        ];
        return $struct;
    }
    
    /**
     * Writes the Data structure for the ZipCode first 3 digits into its respective config file.
     * @param string $zipcodeFirst3
     * @param array  $struct
     */
    private function _writeJsonConfig($zipcodeFirst3, $struct)
    {
        $fileName = self::getDataPath($zipcodeFirst3);
        
        $fh = fopen($fileName, 'w');
        fwrite($fh, json_encode($struct));
        fclose($fh);
    }
    
}

