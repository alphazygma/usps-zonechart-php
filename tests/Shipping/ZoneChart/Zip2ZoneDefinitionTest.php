<?php /** @copyright Alejandro Salazar (c) 2016 */
namespace Shipping\ZoneChart;

/**
 * The <kbd>Zip2ZoneDefinitionTest</kbd> test suite for the <kbd>Zip2ZoneDefinition</kbd> entity class.
 *
 * @author     Alejandro Salazar (alejandros@pley.com)
 * @version    1.0
 * @license    http://www.gnu.org/licenses/lgpl-3.0.en.html GNU LGPLv3
 * @link       https://github.com/alphazygma/usps-zonechart-php
 * @package    Shipping
 * @subpackage ZoneChart
 */
class Zip2ZoneDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testFromConfigDirect()
    {
        $config = ['r' => '005', 'z' => 2];
        
        $z2z = Zip2ZoneDefinition::fromConfig($config);
        $this->assertEquals('005', $z2z->getZipRangeStart());
        $this->assertEquals('005', $z2z->getZipRangeEnd());
        $this->assertEquals(2, $z2z->getZone());
        $this->assertFalse($z2z->isException());
        
        // Test inside range ------------------------------------------
        // Test with regular zipcode
        $this->assertTrue($z2z->isInRange('00510'));
        // Test with Zip+4 format
        $this->assertTrue($z2z->isInRange('00510-0332'));
        
        // Test outside range ------------------------------------------
        // Test with regular zipcode
        $this->assertFalse($z2z->isInRange('00610'));
        // Test with Zip+4 format
        $this->assertFalse($z2z->isInRange('00610-0332'));
    }
    
    public function testFromConfigExceptionDirect()
    {
        $config = ['r' => '95055', 'z' => 2];
        
        $z2z = Zip2ZoneDefinition::fromConfig($config, true);
        $this->assertEquals('95055', $z2z->getZipRangeStart());
        $this->assertEquals('95055', $z2z->getZipRangeEnd());
        $this->assertEquals(2, $z2z->getZone());
        $this->assertTrue($z2z->isException());
        
        // Test inside range ------------------------------------------
        // Test with regular zipcode
        $this->assertTrue($z2z->isInRange('95055'));
        // Test with Zip+4 format
        $this->assertTrue($z2z->isInRange('95055-0332'));
        
        // Test outside range ------------------------------------------
        // Test with regular zipcode
        $this->assertFalse($z2z->isInRange('95056'));
        // Test with Zip+4 format
        $this->assertFalse($z2z->isInRange('95056-0332'));
    }
    
    public function testFromConfigRange()
    {
        $config = ['r' => ['005', '027'], 'z' => 3];
        
        $z2z = Zip2ZoneDefinition::fromConfig($config);
        $this->assertEquals('005', $z2z->getZipRangeStart());
        $this->assertEquals('027', $z2z->getZipRangeEnd());
        $this->assertEquals(3, $z2z->getZone());
        $this->assertFalse($z2z->isException());
        
        // Test inside range ------------------------------------------
        // Test with regular zipcode at origin
        $this->assertTrue($z2z->isInRange('00510'));
        // Test with Zip+4 format
        $this->assertTrue($z2z->isInRange('00510-0332'));
        
        // Test with regular zipcode in betweend
        $this->assertTrue($z2z->isInRange('01510'));
        // Test with Zip+4 format
        $this->assertTrue($z2z->isInRange('01510-0332'));
        
        // Test with regular zipcode at edge
        $this->assertTrue($z2z->isInRange('02710'));
        // Test with Zip+4 format
        $this->assertTrue($z2z->isInRange('02710-0332'));
        
        // Test outside range ------------------------------------------
        // Test with regular zipcode
        $this->assertFalse($z2z->isInRange('02810'));
        // Test with Zip+4 format
        $this->assertFalse($z2z->isInRange('02810-0332'));
    }
    
    public function testFromConfigExceptionRange()
    {
        $config = ['r' => ['95055', '95128'], 'z' => 3];
        
        $z2z = Zip2ZoneDefinition::fromConfig($config, true);
        $this->assertEquals('95055', $z2z->getZipRangeStart());
        $this->assertEquals('95128', $z2z->getZipRangeEnd());
        $this->assertEquals(3, $z2z->getZone());
        $this->assertTrue($z2z->isException());
        
        // Test inside range ------------------------------------------
        // Test with regular zipcode at origin
        $this->assertTrue($z2z->isInRange('95055'));
        // Test with Zip+4 format
        $this->assertTrue($z2z->isInRange('95055-0332'));
        
        // Test with regular zipcode in betweend
        $this->assertTrue($z2z->isInRange('95089'));
        // Test with Zip+4 format
        $this->assertTrue($z2z->isInRange('95089-0332'));
        
        // Test with regular zipcode at edge
        $this->assertTrue($z2z->isInRange('95128'));
        // Test with Zip+4 format
        $this->assertTrue($z2z->isInRange('95128-0332'));
        
        // Test outside range ------------------------------------------
        // Test with regular zipcode
        $this->assertFalse($z2z->isInRange('95129'));
        // Test with Zip+4 format
        $this->assertFalse($z2z->isInRange('95129-0332'));
    }
}
