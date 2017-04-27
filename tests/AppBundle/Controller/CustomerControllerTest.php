<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerControllerTest extends WebTestCase
{
    public function testCreateCustomerActionSuccess()
    {
        return;
        $client = static::createClient();

        $client->request('PUT', '/customers', [
            'firstName' => 'Ruben',
            'lastName' => 'Knol',
            'gender' => 'm',
            'email' => 'c.minor6@gmail.com',
            'country' => 'DE',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testCreateCustomerActionMissingParams()
    {
        return;
        $client = static::createClient();

        $client->request('PUT', '/customers', []);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $this->assertEquals('Field \'firstName\' cannot be left blank.',
            json_decode($client->getResponse()->getContent(), true)['error']);

        $client->request('PUT', '/customers', [
            'firstName' => 'Ruben',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $this->assertEquals('Field \'lastName\' cannot be left blank.',
            json_decode($client->getResponse()->getContent(), true)['error']);

        $client->request('PUT', '/customers', [
            'firstName' => 'Ruben',
            'lastName' => 'Knol',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $this->assertEquals('Field \'gender\' cannot be left blank.',
            json_decode($client->getResponse()->getContent(), true)['error']);
    }

    public function testCreateCustomerEmailExists()
    {
        return;
        $client = static::createClient();

        $client->request('PUT', '/customers', [
            'firstName' => 'Ruben',
            'lastName' => 'Knol',
            'gender' => 'm',
            'email' => 'c.minor6@gmail.com',
            'country' => 'DE',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $this->assertEquals('E-mail address is already in use.',
            json_decode($client->getResponse()->getContent(), true)['error']);
    }

    public function testUpdateCustomerAction()
    {
        return;
        $client = static::createClient();

        $crawler = $client->request('PATCH', '/customers/3', [
            'firstName' => 'Ruben',
            'lastName' => 'Knol',
            'gender' => 'm',
            'email' => 'c.minor6@gmail.com',
            'country' => 'DE',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
