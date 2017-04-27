<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Transaction;
use Doctrine\DBAL\Connection;

class TransactionRepository
{
    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @var int
     */
    private $bonusIteration;

    /**
     * TransactionRepository constructor.
     *
     * @param Connection $dbal
     * @param int        $bonusIteration
     */
    public function __construct($dbal, $bonusIteration = 3)
    {
        $this->dbal = $dbal;
        $this->bonusIteration = $bonusIteration;
    }

    /**
     * Wrapper function to Doctrine DBAL's Connection::transactional() to
     * expose the functionality without passing around connection objects.
     *
     * @param \Closure $func
     */
    public function transactional(\Closure $func)
    {
        $this->dbal->transactional($func);
    }

    /**
     * Get the next bonus amount for a transaction.
     *
     * The business logic is as follows:
     *
     * If the transaction is a deposit (amount > 0), every x'th transaction yields
     * a bonus based on individual customer's percentage.
     *
     * @param Customer    $customer
     * @param Transaction $transaction
     *
     * @return float|int
     */
    public function getNextBonusForCustomer(Customer $customer, Transaction $transaction)
    {
        if ($transaction->getAmount() > 0) {
            $res = $this->dbal->fetchAssoc(
                'SELECT count(id) AS tx_count FROM transactions WHERE user_id = ?',
                [$customer->getId()]
            );

            if ($res['tx_count'] > 0 && ($res['tx_count'] + 1) % $this->bonusIteration === 0) {
                return round($transaction->getAmount() * ($customer->getBonusPercentage() / 100));
            }
        }

        return 0;
    }

    /**
     * Persist a transaction entity into the database.
     *
     * @param Transaction $transaction
     */
    public function persist(Transaction $transaction)
    {
        $this->dbal->insert('transactions', [
            'user_id' => $transaction->getCustomer()->getId(),
            'event_date' => $transaction->getEventDate(),
            'country' => $transaction->getCountry(),
            'amount' => $transaction->getAmount(),
            'bonus' => $transaction->getBonus(),
        ], [
            \PDO::PARAM_INT,
            'datetime',
            \PDO::PARAM_STR,
            \PDO::PARAM_INT,
            \PDO::PARAM_INT,
        ]);
    }
}
