<?php /** @copyright Alejandro Salazar (c) 2016 */
namespace Shipping\ZoneChart;

/**
 * The <kbd>ZoneChartTest</kbd>
 *
 * @author Alejandro Salazar (alejandros@pley.com)
 * @version 1.0
 * @package 
 * @subpackage
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
}
