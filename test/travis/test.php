<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

include 'src/miyuki.php';

class Test extends PHPUnit_Framework_TestCase
{
    function __construct()
    {
        $this->miyuki = new Miyuki('test/travis/test.png');
    }

    function testCheckType()
    {
        //$this->fail('');
        return false;
    }
}

?>