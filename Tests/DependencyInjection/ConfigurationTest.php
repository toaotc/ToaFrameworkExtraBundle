<?php

namespace Toa\Bundle\FrameworkExtraBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Toa\Bundle\FrameworkExtraBundle\DependencyInjection\Configuration;

/**
 * ConfigurationTest
 *
 * @author Enrico Thies <enrico.thies@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(true), array());

        $this->assertEquals(
            array_merge(array('request' => array()), self::getBundleDefaultConfig()),
            $config
        );
    }

    /**
     * @dataProvider getTestValidRequestAttributesData
     */
    public function testValidRequestAttributes($requestAttributes, $processedAttributes)
    {
        $processor = new Processor();
        $configuration = new Configuration(true);
        $config = $processor->processConfiguration($configuration, array(array(
            'request' => $requestAttributes,
        )));

        $this->assertEquals($processedAttributes, $config['request']);
    }

    public function getTestValidRequestAttributesData()
    {
        return array(
            array(array('recycle_attributes' => array('_domain')), array('recycle_attributes' => array('_domain')))
        );
    }

    protected static function getBundleDefaultConfig()
    {
        return array(
            'request' => array(
                'recycle_attributes' => array()
            ),
        );
    }
}
