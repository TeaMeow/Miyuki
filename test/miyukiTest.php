<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

include 'miyuki.php';

class MiyukiTest extends PHPUnit_Framework_TestCase
{
    function __construct()
    {
        $this->Miyuki = new Miyuki();
        $this->Miyuki->create('test/test.png');
    }

    function testCreate()
    {
        $this->Miyuki->create('test/test.png');
        $this->Miyuki->create('test/test.jpg');
        $this->Miyuki->create('test/test.jpeg');
        $this->Miyuki->create('test/test.webp');
        $this->Miyuki->create('test/test.bmp');
    }

    function testCheckType()
    {
        $this->assertFalse($this->Miyuki->checkType('jpg'));
        $this->assertTrue($this->Miyuki->checkType('png'));
    }

    function testCheckFilesize()
    {
        $this->assertFalse($this->Miyuki->checkFilesize(10));
    }

    function testCheckSize()
    {
        $this->assertTrue($this->Miyuki->checkSize(112, 142));
        $this->assertFalse($this->Miyuki->checkSize(1, 1));
    }

    function testResize()
    {
        $this->Miyuki->resize(20, 20);
    }

    function testScale()
    {
        $this->Miyuki->scale(20, 20);
    }

    function testThumbnail()
    {
        $this->Miyuki->thumbnail(20, 20);
    }

    function testCrop()
    {
        $this->Miyuki->crop(20, 20, 20, 20);
    }

    function testAspectRatio()
    {
        $this->Miyuki->aspectRatio(999, 999, 999, 999);
        $this->Miyuki->aspectRatio(20, 20, 40, 40);
        $this->Miyuki->aspectRatio(40, 40, 20, 20);
        $this->Miyuki->aspectRatio(1, 1, 1, 1);
        $this->Miyuki->aspectRatio(-1, -1, -1, -1);
    }

    function testSetQuality()
    {
        $this->Miyuki->setQuality(0.1);
    }

    function testSetImageCompression()
    {
        $this->Miyuki->setImageCompression(Imagick::COMPRESSION_JPEG);
    }

    function testSetCompression()
    {
        $compressions = ['UNDEFINED', 'NO', 'BZIP', 'FAX', 'GROUP4', 'JPEG', 'JPEG2000', 'LOSSLESSJPEG', 'LZW', 'RLE', 'ZIP'];

        foreach($compressions as $compression)
            $this->Miyuki->setCompression($compression);
    }

    function testSetFilter()
    {
        $filters = ['LANCZOS', 'POINT'];

        foreach($filters as $filter)
            $this->Miyuki->setFilter($filter);
    }

    function testSetType()
    {
        $types = ['png', 'png8', 'jpeg'];

        foreach($types as $type)
            $this->Miyuki->setType($type);
    }

    function testGetSize()
    {
        $size = $this->Miyuki->getSize();

        $this->assertEquals($size['width'], 112);
        $this->assertEquals($size['height'], 142);
    }

    function testWrite()
    {
        $this->Miyuki->create('test/test.png');
        $this->Miyuki->write(false);

        $this->Miyuki->create('test/test.png');
        $this->Miyuki->write(true);

        $this->Miyuki->create('test/test.png');
        $this->Miyuki->write('/tmp/test.png');
    }

    function testGetType()
    {
        $this->Miyuki->create('test/test.png');
        $this->assertEquals($this->Miyuki->getType(), 'png');

        $this->Miyuki->create('test/test.jpg');
        $this->assertEquals($this->Miyuki->getType(), 'jpg');

        $this->Miyuki->create('test/test.jpeg');
        $this->assertEquals($this->Miyuki->getType(), 'jpeg');

        $this->Miyuki->create('test/test.webp');
        $this->assertEquals($this->Miyuki->getType(), 'webp');

        $this->Miyuki->create('test/test.bmp');
        $this->assertEquals($this->Miyuki->getType(), 'bmp');
    }
}

?>