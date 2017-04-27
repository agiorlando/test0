<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransactionControllerTest extends WebTestCase
{
    public function testTransactionReportAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/transactions');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateTransactionAction()
    {
        $client = static::createClient();

        $crawler = $client->request('PUT', '/customers/3/transactions');
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }
}
