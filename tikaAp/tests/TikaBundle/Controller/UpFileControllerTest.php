<?php

namespace tests\TikaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UpFileControllerTest
 * @package tests\TikaBundle\Controller
 */
class UpFileControllerTest extends WebTestCase
{

    /**
     * @test
     */
    public function displayValidMainPageTitleH1()
    {
        $client = static::createClient();
        $url = $client->getContainer()->get('router')->generate('file_new');
        $crawler = $client->request('GET', $url);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("File creation")')->count());
    }


    /**
     * @test
     */
    public function displayValidJsonResponse()
    {
        $client = static::createClient();
        $url = $client->getContainer()->get('router')->generate('file_index_by_name');
        $crawler = $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type','application/json'));
    }


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
    public function isValidReturnedDataFromShowUpFile()
    {
        $file = new \TikaBundle\Entity\UpFile();
        $file->setMetadata('metadataJson');
        $this->assertEquals(json_decode('metadataJson'), $file->getMetadata());
    }

    /**
     * @test
     */
    public function sendingFileByMainForm()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/new/');
    }
    

   //----------
    /**
     * @dataProvider urlProvider
     * @param url $url
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
