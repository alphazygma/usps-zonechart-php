<?php /** @copyright Alejandro Salazar (c) 2016 */
namespace Shipping\ZoneChart;

/**
 * The <kbd>ZoneChart</kbd> class provides the functionality to find the Zone between a Source
 * ZipCode and a destination ZipCode.
 *
 * @author     Alejandro Salazar (alejandros@pley.com)
 * @version    1.0
 * @license    http://www.gnu.org/licenses/lgpl-3.0.en.html GNU LGPLv3
 * @link       https://github.com/alphazygma/usps-zonechart-php
 * @package    Shipping
 * @subpackage ZoneChart
 */
class ZoneChart
{
    /** @var string */
    protected $_sourceZipCode;
    /**
     * Map containing information of zipcode ranges to zones.
     * <p>The array structure is as follows:<br/>
     * <pre>array(
     * &nbsp;    'destinationZipList' => \Shipping\ZoneChart\Zip2ZoneDefinition[],
     * &nbsp;    'exceptionList'      => \Shipping\ZoneChart\Zip2ZoneDefinition[]
     * }</pre>
     * </p>
     * @var array
     */
    protected $_configMap;
    
    public function __construct($sourceZipCode)
    {
        $this->_sourceZipCode = $sourceZipCode;
        
        $jsonConfig       = $this->_getJsonConfig($sourceZipCode);
        $this->_configMap = $this->_parseJsonConfig($jsonConfig);
    }
    
    /**
     * Returns the Zone for the supplied destination ZipCode based on this instance source ZipCode.
     * <p><kbd>FALSE</kbd> is returned if the supplied destination is an invalid zipcode or there
     * is no rule data from source to destination.</p>
     * @param string|int $destinationZipCode
     * @return int|boolean
     */
    public function getZoneFor($destinationZipCode)
    {
        // We always start with the exception list as the list is always much shorter than the
        // regular zipcode checks, plus these checks have to be done anyway, so might as well
        // save vain iterations over the regular ranges if this zipcode were to be an exception.
        $exceptionList = $this->_configMap['exceptionList'];
        /* @var $zip2zoneDef \Shipping\ZoneChart\Zip2ZoneDefinition */
        foreach ($exceptionList as $zip2zoneDef) {
            if ($zip2zoneDef->isInRange($destinationZipCode)) {
                return $zip2zoneDef->getZone();
            }
        }
        
        // If we reach this section, the zipcode was not in the exception list, so now we need to
        // check against the regular zipcode ranges.
        $destinationList = $this->_configMap['destinationZipList'];
        /* @var $zip2zoneDef \Shipping\ZoneChart\Zip2ZoneDefinition */
        foreach ($destinationList as $zip2zoneDef) {
            if ($zip2zoneDef->isInRange($destinationZipCode)) {
                return $zip2zoneDef->getZone();
            }
        }
        
        // If we reach this code, it means that the supplied destination ZipCode was invalid and
        // not within any range rules
        return FALSE;
    }
    
    /**
     * Returns the JSON string confugration for the supplied zip code.
     * @param string $zipCode
     * @return string
     * @throws \Exception If there is no configuration for the source Zipcode
     */
    protected function _getJsonConfig($zipCode)
    {
        // Using the ConfigGenerator to retrieve the path to the configuration given a ZipCode
        $configPath = ConfigGenerator::getDataPath($zipCode);
        
        if (!file_exists($configPath)) {
            throw new \Exception('No configuration found for supplied ZipCode ' . $zipCode);
        }
        
        // Obatining the JSON configuration string inside the file
        $jsonConfig = file_get_contents($configPath);
        if ($jsonConfig === false) {
            throw new \Exception('Could not read contents of config for ZipCode ' . $zipCode);
        }
        
        return $jsonConfig;
    }
    
    /**
     * Parses a JSON configuration string into a map of <kbd>Zip2ZoneDefinition</kbd> objects.
     * @param string $jsonConfig
     * @return array A map with the following structure
     *      <pre>array(
     *      &nbsp;    'destinationZipList' => \Shipping\ZoneChart\Zip2ZoneDefinition[],
     *      &nbsp;    'exceptionList'      => \Shipping\ZoneChart\Zip2ZoneDefinition[]
     *      }</pre>
     */
    protected function _parseJsonConfig($jsonConfig)
    {
        $configMap = json_decode($jsonConfig, true);
        
        $destinationLength = count($configMap['destinationZipList']);
        $exceptionLength   = count($configMap['exceptionList']);
        
        // In-Plance parsing of array definition to object definition
        for ($i = 0; $i < $destinationLength; $i++) {
            $configMap['destinationZipList'][$i] = Zip2ZoneDefinition::fromConfig(
                $configMap['destinationZipList'][$i]
            );
        }
        
        // In-Plance parsing of array definition to object definition of exceptions
        for ($i = 0; $i < $exceptionLength; $i++) {
            $configMap['exceptionList'][$i] = Zip2ZoneDefinition::fromConfig(
                $configMap['exceptionList'][$i], true
            );
        }
        
        return $configMap;
    }
}
