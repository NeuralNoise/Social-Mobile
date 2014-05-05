<?php
/**
 * Webino (https://github.com/webino/)
 *
 * @link      https://github.com/webino/WebinoImageThumb/ for the canonical source repository
 * @copyright Copyright (c) 2013 Webino, s. r. o. (http://webino.sk/)
 * @license   New BSD License
 */

namespace WebinoImageThumb;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-07-12 at 14:11:34.
 */
class WebinoImageThumbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WebinoImageThumb
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new WebinoImageThumb;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers WebinoImageThumb\WebinoImageThumb::create
     */
    public function testCreate()
    {
        $src   = __DIR__ . '/../resources/test.jpg';
        $thumb = $this->object->create($src);
        $this->assertInstanceOf('PHPThumb\GD', $thumb);
    }

    /**
     * @covers WebinoImageThumb\WebinoImageThumb::create
     */
    public function testCreateMissing()
    {
        $this->setExpectedException('WebinoImageThumb\Exception\RuntimeException');
        $src = __DIR__ . '/../resources/this_should_not_exist.jpg';
        $this->object->create($src);
    }

    /**
     * @covers WebinoImageThumb\WebinoImageThumb::createReflection
     */
    public function testCreateReflection()
    {
        $reflection = $this->object->createReflection(50, 50, 50, true, '#505050');
        $this->assertInstanceOf('PHPThumb\Plugins\Reflection', $reflection);
    }
}