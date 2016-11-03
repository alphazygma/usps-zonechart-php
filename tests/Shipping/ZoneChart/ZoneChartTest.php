<?php /** @copyright Alejandro Salazar (c) 2016 */
namespace Shipping\ZoneChart;

/**
 * The <kbd>ZoneChartTest</kbd> test suite for the <kbd>ZoneChart</kbd> class.
 *
 * @author     Alejandro Salazar (alejandros@pley.com)
 * @version    1.0
 * @license    http://www.gnu.org/licenses/lgpl-3.0.en.html GNU LGPLv3
 * @link       https://github.com/alphazygma/usps-zonechart-php
 * @package    Shipping
 * @subpackage ZoneChart
 */
class ZoneChartTest extends \PHPUnit_Framework_TestCase
{
    public function testGetZoneForValidZone()
    {
        $zoneChart = new ZoneChart('94040');
        
        $zone = $zoneChart->getZoneFor('94404');
        
        // Since we are not testing zones as these could potentially upon USPS choices, we just need
        // to make sure that the code execution is performed and we retrieve an integer value for
        // valid zipcodes
        $this->assertNotNull($zone);
        $this->assertTrue(is_int($zone));
    }
    
    public function testGetZoneForInvalidZone()
    {
        $zoneChart = new ZoneChart('94040');
        
        $zone = $zoneChart->getZoneFor('00001');
        
        // Since we are not testing zones as these could potentially upon USPS choices, we just need
        // to make sure that the code execution is performed and we retrieve FALSE for invalid zipcodes
        $this->assertFalse($zone);
    }
    
    public function testGetZoneForExceptionZone()
    {
        $zoneChart = new ZoneChart('94040');
        
        $zone = $zoneChart->getZoneFor('96938');
        
        // Since we are not testing zones as these could potentially upon USPS choices, we just need
        // to make sure that the code execution is performed and we retrieve an integer value for
        // valid zipcodes
        // BIG NOTE: If the data is updated upon USPS changes, there is a chance this test might
        //           fail if the destination zone is no longer considered an exception.
        $this->assertNotNull($zone);
        $this->assertTrue(is_int($zone));
    }
    
    /**
     * @expectedException Exception
     */
    public function testNoConfigException()
    {
        // Zone doesn't exist so it should throw an exception
        new ZoneChart('00100');
    }
}
