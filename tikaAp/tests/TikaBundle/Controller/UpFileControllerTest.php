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


    // tests/AppBundle/ApplicationAvailabilityFunctionalTest.php

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        //$client->request('GET', $url);

        $url = $client->getContainer()->get('router')->generate('file_new');
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return array(
            array('/1'),
            //array('/file/1'),
        );
    }
}
