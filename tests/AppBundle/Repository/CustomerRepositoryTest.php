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

        return $dbal;
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
}
