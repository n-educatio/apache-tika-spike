<?php

/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 05.03.17
 * Time: 22:25
 */
namespace tests\TikaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UpFileControllerTest
 * @package tests\TikaBundle\Controller
 */
class UpFileControllerTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     * @param url $url
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        //$client->request('GET', $url);

        $url = $client->getContainer()->get('router')->generate('file_index');
        $client->request('POST', $url);

        //$this->assertTrue($client->getResponse()->isServerError());
        $this->assertTrue($client->getResponse()->isClientError());
        //$this->assertTrue($client->getResponse()->isSuccessful());
        //die($client->getResponse()->getContent());
    }

    /**
     * @return array
     */
    public function urlProvider()
    {
        return array(
            array('/'),
        );
    }
}
