<?php

namespace tests\TikaBundle\Entity;
use TikaBundle\Entity\UpFile;

/**
 * Class UpFileTest
 * @package TikaBundle\Tests\Entity
 */
class UpFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function isFilenameValidString()
    {
        //given
        $file = new UpFile();

        //when
        $file->setFileName('testing.txt');

        //then
        $this->assertEquals('testing.txt', $file->getFileName());
    }

    /**
     * @test
     */
    public function isMetadatavalidString(){
        //given
        $file = new UpFile();

        //when
        $file->setMetadata('metadataJson');

        //then
        $this->assertEquals(json_decode('metadataJson'), $file->getMetadata());
    }
}