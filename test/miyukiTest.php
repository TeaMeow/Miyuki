<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

include 'miyuki.php';

class MiyukiTest extends PHPUnit_Framework_TestCase
{
    function __construct()
    {
        $this->miyuki = new Miyuki();
        $this->miyuki->create('test/travis/test.png');
    }

    function testCheckType()
    {
        $this->assertFalse($this->miyuki->checkType('jpg'));
        $this->assertTrue($this->miyuki->checkType('png'));
    }

    function testCheckFilesize()
    {
        $this->assertFalse($this->miyuki->checkFilesize(10));
    }

    function testCheckSize()
    {
        $this->assertTrue($this->miyuki->checkSize(112, 142));
        $this->assertFalse($this->miyuki->checkSize(1, 1));
    }

    function testResize()
    {
        $this->miyuki->resize(20, 20);
    }

    function testScale()
    {
        $this->miyuki->scale(20, 20);
    }

    function testThumbnail()
    {
        $this->miyuki->thumbnail(20, 20);
    }

    function testAspectRatio()
    {
        $this->miyuki->aspectRatio(20, 20, 40, 40);
        $this->miyuki->aspectRatio(1, 1, 1, 1);
    }

    function testSetQuality()
    {
        $this->miyuki->setQuality(0.1);
    }

    function testSetCompression()
    {
        $this->miyuki->setCompression('ZIP');
    }

    function testSetFilter()
    {
        $this->miyuki->setFilter('LANCZOS');
    }

    function testSetType()
    {
        $this->miyuki->setType('jpeg');
    }

    function testWrite()
    {
        $this->miyuki->write(false);
    }
}

?>