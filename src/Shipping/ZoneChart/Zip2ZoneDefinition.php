<?php /** @copyright Alejandro Salazar (c) 2016 */
namespace Shipping\ZoneChart;

/**
 * The <kbd>Zip2ZoneDefinition</kbd>
 *
 * @author Alejandro Salazar (alejandros@pley.com)
 * @version 1.0
 * @package 
 * @subpackage
 */
class Zip2ZoneDefinition
{
    /** @var string */
    private $_zipRangeStartStr;
    /** @var string */
    private $_zipRangeEndStr;
    /** @var int */
    private $_zipRangeStart;
    /** @var int */
    private $_zipRangeEnd;
    /** @var int */
    private $_zone;
    /** @var boolean */
    private $_isException ;

    /**
     * Returns a new <kbd>Zip2ZoneDefinition</kbd> Object from a configuration destination array map.
     * @param array   $destinationDefinition An array map with the any of the following two structures
     *      <ul>
     *         <li>The Range (<i>`r`</i>) is a single string, representing a direct zipcode to a 
     *             Zone (<i>`z`</i>)<br/>
     *             <pre>['r' => '005', 'z' => 7]</pre>
     *         </li>
     *         <li>The Range (<i>`r`</i>) is an array, representing a zipcode range to a Zone (<i>`z`</i>)<br/>
     *             <pre>['r' => ['12345', '456789'], 'z' => 7]</pre>
     *         </li>
     *      </ul>
     * @param boolean $isException           Used to determine if comparisons should be done using
     *      the first 3 digits for regular definitions, or 5 digits for exception definitions.
     * @return \Shipping\ZoneChart\Zip2ZoneDefinition
     */
    public static function fromConfig($destinationDefinition, $isException = false)
    {
        // If the range is an array, it is a true zip code range to a zone
        if (is_array($destinationDefinition['r'])) {
            $zipRangeStartStr = $destinationDefinition['r'][0];
            $zipRangeEndStr   = $destinationDefinition['r'][1];

            // otherwise there is no range, but a fixed zip code to a zone, so the range is created
        // with the same value
        } else {
            $zipRangeStartStr = $destinationDefinition['r'];
            $zipRangeEndStr   = $zipRangeStartStr;
        }
        
        $zone = $destinationDefinition['z'];
        
        return new static($zipRangeStartStr, $zipRangeEndStr, $zone, $isException);
    }
    
    /**
     * Creates a new instance of <kbd>Zip2ZoneDefinition</kbd> with the supplied definition
     * @param string  $zipRangeStartStr
     * @param string  $zipRangeEndStr
     * @param int     $zone
     * @param boolean $isException      Used to determine if comparisons should be done using
     *      the first 3 digits for regular definitions, or 5 digits for exception definitions.
     */
    public function __construct($zipRangeStartStr, $zipRangeEndStr, $zone, $isException)
    {
        $this->_zipRangeStartStr = $zipRangeStartStr;
        $this->_zipRangeEndStr   = $zipRangeEndStr;
        $this->_zone             = (int)$zone;
        $this->_isException      = (boolean)$isException;
        
        $this->_zipRangeStart = (int)$zipRangeStartStr;
        $this->_zipRangeEnd   = (int)$zipRangeEndStr;
    }

    
    /**
     * The zipcode for the start of the range.
     * <p>It can be either a 3 digit string for a regular definition, or a 5 digit if it is for
     * an exception definition. (see <kbd>isException()</kbd>)</p>
     * @return string
     */
    public function getZipRangeStart()
    {
        return $this->_zipRangeStartStr;
    }
    
    /**
     * The zipcode for the end of the range.
     * <p>It can be either a 3 digit string for a regular definition, or a 5 digit if it is for
     * an exception definition. (see <kbd>isException()</kbd>)</p>
     * @return string
     */
    public function getZipRangeEnd()
    {
        return $this->_zipRangeEndStr;
    }
    
    /**
     * The Zone assigned to the zipcode range of this definition
     * @return int
     */
    public function getZone()
    {
        return $this->_zone;
    }

    /**
     * Indicates if this definition is a regular zipcode range to zone, or an exception definition.
     * <p>Regular range uses the first 3 digits of the ZipCode, while Exception ranges use the full
     * 5 digits of a ZipCode.</p>
     * @return boolean
     */
    public function isException()
    {
        return $this->_isException;
    }

    /**
     * Returns if the supplied ZipCode falls in the range of this definition.
     * @param string|int $zipcode
     * @return boolean
     */
    public function isInRange($zipcode)
    {
        // Checking if the zipcode is a Zip+4 style, if so, we need to trim the `+4` part
        if (is_string($zipcode) && strpos($zipcode, '-') !== FALSE) {
            $zipcode = substr($zipcode, strpos($zipcode, '-'));
        }
        
        $zipCodeInt = (int)$zipcode;
        
        // If this is NOT an exception definition, then we need to use the first three digits only
        if (!$this->_isException) {
            $zipCodeInt = (int)substr($zipcode, 0, 3);
        }
        
        return $this->_zipRangeStart <= $zipCodeInt && $zipCodeInt <= $this->_zipRangeEnd;
    }
}
