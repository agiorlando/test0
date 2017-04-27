<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Transaction;
use AppBundle\Repository\TransactionRepository;
use Doctrine\DBAL\Connection;

class TransactionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNextBonusForCustomerWithActualBonus()
    {
        $dbal = $this->createMock(Connection::class);
        $dbal
            ->method('fetchAssoc')
            ->willReturn(['tx_count' => 2]);

        $repo = new TransactionRepository($dbal, 3);

        $customer = new Customer();
        $customer->setBonusPercentage(10);

        $tx = new Transaction($customer);
        $tx->setAmount(1000);

        $this->assertEquals(100, $repo->getNextBonusForCustomer($customer, $tx));
    }

    public function testGetNextBonusForCustomerWithActualBonus2()
    {
        $dbal = $this->createMock(Connection::class);
        $dbal
            ->method('fetchAssoc')
            ->willReturn(['tx_count' => 5]);

        $repo = new TransactionRepository($dbal, 3);

        $customer = new Customer();
        $customer->setBonusPercentage(10);

        $tx = new Transaction($customer);
        $tx->setAmount(1000);

        $this->assertEquals(100, $repo->getNextBonusForCustomer($customer, $tx));
    }

    public function testGetNextBonusForCustomerWithoutActualBonus()
    {
        $dbal = $this->createMock(Connection::class);
        $dbal
            ->method('fetchAssoc')
            ->willReturn(['tx_count' => 0]);

        $repo = new TransactionRepository($dbal, 3);

        $customer = new Customer();
        $customer->setBonusPercentage(10);

        $tx = new Transaction($customer);
        $tx->setAmount(1000);

        $this->assertEquals(0, $repo->getNextBonusForCustomer($customer, $tx));
    }
}