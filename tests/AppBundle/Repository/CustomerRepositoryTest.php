<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Transaction;
use AppBundle\Repository\CustomerRepository;
use Doctrine\DBAL\Connection;

class CustomerRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private function createCustomer($balance = 0)
    {
        $c = new Customer();
        $c->setFirstName('Ruben');
        $c->setLastName('Knol');
        $c->setEmail('c.minor6@gmail.com');
        $c->setGender('m');
        $c->setCountry('de');
        $c->setBonusPercentage(10);
        $c->setBalance(1000);
        $c->setBonusBalance(100);
        $c->setId(99);

        return $c;
    }

    private function createDbalMock()
    {
        $dbal = $this->createMock(Connection::class);
        $dbal
            ->method('update')
            ->will($this->returnSelf());

        $dbal
            ->method('insert')
            ->will($this->returnSelf());

        $dbal
            ->method('beginTransaction')
            ->will($this->returnSelf());

        $dbal
            ->method('commit')
            ->will($this->returnSelf());

        $dbal
            ->method('rollback')
            ->will($this->returnSelf());

        return $dbal;
    }

    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testUpdateBalancePositive()
    {
        $dbal = $this->createDbalMock();
        $c = $this->createCustomer();

        $tx = new Transaction($c);
        $tx->setAmount(150);
        $tx->setBonus(0);

        $repo = new CustomerRepository($dbal);
        $repo->updateBalance($c, $tx);

        $this->assertEquals(1150, $c->getBalance());
        $this->assertEquals(100, $c->getBonusBalance());
    }

    public function testUpdateBalanceNegative()
    {
        $dbal = $this->createDbalMock();
        $c = $this->createCustomer();

        $tx = new Transaction($c);
        $tx->setAmount(-150);
        $tx->setBonus(0);

        $repo = new CustomerRepository($dbal);
        $repo->updateBalance($c, $tx);

        $this->assertEquals(850, $c->getBalance());
        $this->assertEquals(100, $c->getBonusBalance());
    }

    /**
     * @expectedException \AppBundle\Exception\InsufficientFundsError
     */
    public function testInsufficientBalanceTx()
    {
        $dbal = $this->createDbalMock();
        $c = $this->createCustomer();
        $c->setBalance(100);

        $tx = new Transaction($c);
        $tx->setAmount(-150);
        $tx->setBonus(0);

        $repo = new CustomerRepository($dbal);
        $repo->updateBalance($c, $tx);

        $this->assertEquals(850, $c->getBalance());
        $this->assertEquals(100, $c->getBonusBalance());
    }

    public function testHydrateCustomer()
    {
        $row = [
            'id' => 999,
            'first_name' => 'Ruben',
            'last_name' => 'Knol',
            'gender' => 'm',
            'email' => 'c.minor6@gmail.com',
            'country' => 'de',
            'balance' => 1800,
            'balance_bonus' => 100,
            'bonus_pct' => 5,
        ];

        $dbal = $this->createDbalMock();
        $repo = new CustomerRepository($dbal);

        $customer = $this->invokeMethod($repo, 'hydrateCustomer', [$row, new Customer()]);

        $this->assertEquals('Ruben', $customer->getFirstName());
        $this->assertEquals('Knol', $customer->getLastName());
        $this->assertEquals('m', $customer->getGender());
        $this->assertEquals('c.minor6@gmail.com', $customer->getEmail());
        $this->assertEquals('de', $customer->getCountry());
        $this->assertEquals(1800, $customer->getBalance());
        $this->assertEquals(100, $customer->getBonusBalance());
        $this->assertEquals(5, $customer->getBonusPercentage());
    }

    /**
     * @expectedException \AppBundle\Exception\NotUniqueEmailError
     */
    public function testUniqueEmailException()
    {
        $dbal = $this->createDbalMock();
        $c = $this->createCustomer();

        $dbal
            ->method('fetchAssoc')
            ->willReturn([
                'id' => 999,
                'first_name' => 'Ruben',
                'last_name' => 'Knol',
                'gender' => 'm',
                'email' => 'c.minor6@gmail.com',
                'country' => 'de',
                'balance' => 1800,
                'balance_bonus' => 100,
                'bonus_pct' => 5,
            ]);

        $repo = new CustomerRepository($dbal);
        $repo->persist($c);
    }

    public function testUniqueEmailSameUser()
    {
        $dbal = $this->createDbalMock();
        $c = $this->createCustomer();

        $dbal
            ->method('fetchAssoc')
            ->willReturn([
                'id' => 99,
                'first_name' => 'Ruben',
                'last_name' => 'Knol',
                'gender' => 'm',
                'email' => 'c.minor6@gmail.com',
                'country' => 'de',
                'balance' => 1800,
                'balance_bonus' => 100,
                'bonus_pct' => 5,
            ]);

        $repo = new CustomerRepository($dbal);
        $repo->persist($c);
    }
}
