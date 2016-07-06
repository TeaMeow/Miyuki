<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

include 'miyuki.php';

class MiyukiTest extends PHPUnit_Framework_TestCase
{
    function __construct()
    {
        $this->miyuki = new Miyuki('test/travis/test.png');
    }

    function testCheckType()
    {
        if($this->miyuki->checkType('jpg'))
            $this->fail('Miyuki allowed an image with the type which shouldn\'t be allowed.');

        if(!$this->miyuki->checkType('png'))
            $this->fail('Miyuki not allowed an image with the type which should be allowed.');
    }

    function testCheckFilesize()
    {
        if($this->miyuki->checkFilesize(10))
            $this->fail('Miyuki allowed an image with the wrong filesize.');
    }

    function testCheckSize()
    {
        if(!$this->miyuki->checkSize(112, 142) || $this->miyuki->checkSize(1, 1))
            $this->fail('Miyuki having problem on detect the size of the image.');
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