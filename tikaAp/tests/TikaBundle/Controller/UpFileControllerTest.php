<?php

/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 05.03.17
 * Time: 22:25
 */
namespace tests\TikaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpFileControllerTest extends WebTestCase
{

    /**
     * @test
     */
    public function displayFileList()
    {
        $client = self::createClient();
        $url = $client->getContainer()->get('router')->generate('file_index');
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @test
     */
    public function displayFileListWithDefaultSlug()
    {
        $client = self::createClient();
        $url = $client->getContainer()->get('router')->generate('file_index_by_name');
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @test
     */
    public function addNewFile()
    {
        $client = self::createClient();
        $url = $client->getContainer()->get('router')->generate('file_new');
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @test
     */
    public function showUpFile()
    {
        $file = new \TikaBundle\Entity\UpFile();
        $file->setMetadata('metadataJson');
        $this->assertEquals(json_decode('metadataJson'), $file->getMetadata());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = $this->createClient();
        $url = $client->getContainer()->get('router')->generate('file_index');
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @return array
     */
    public function urlProvider()
    {
        return array(
            array('/index'),
            array('/new/'),
            array('/'),
        );
    }
}
