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

    /**
     * Get a transaction overview for a certain amount of days.
     *
     * @param int $days
     *
     * @return array
     */
    public function getReport($days)
    {
        // Step 1: Gather the report of sums - as much as can be queried at once
        $query = '
          SELECT 
            DATE(event_date) as date, 
            COUNT(DISTINCT(user_id)) as total_customers, 
            country, 
            -SUM(CASE WHEN amount<0 THEN amount ELSE 0 END) as total_withdrawn,
            0 AS withdrawal_count,
            SUM(CASE WHEN amount>=0 THEN amount ELSE 0 END) as total_deposited,
            0 AS deposit_count,
            SUM(bonus) as bonus_earned 
          FROM
            transactions
          WHERE 
            event_date BETWEEN (NOW() - INTERVAL %d DAY) AND NOW()
          GROUP BY 
            DATE(event_date), 
            country        
        ';

        $data = $this->dbal->fetchAll(sprintf($query, intval($days)));

        // Step 2: Merge in the count of withdrawals for the same period
        $query = '
          SELECT 
            DATE(event_date) as date, 
            country, 
            COUNT(id) as times_withdrawn 
          FROM 
            transactions 
          WHERE 
            amount <0 
            AND event_date BETWEEN (NOW() - INTERVAL %d DAY) AND NOW()
          GROUP BY
            DATE(event_date), 
            country
        ';

        $withdrawalCount = $this->dbal->fetchAll(sprintf($query, intval($days)));

        foreach ($withdrawalCount as $row) {
            foreach ($data as $k => $v) {
                if ($v['date'] == $row['date'] && $v['country'] == $row['country']) {
                    $data[$k]['withdrawal_count'] = $row['times_withdrawn'];
                }
            }
        }

        // Step 3: Merge in the count of deposits for the same period
        $query = '
          SELECT 
            DATE(event_date) as date, 
            country, 
            COUNT(id) as times_deposited 
          FROM 
            transactions 
          WHERE 
            amount >0 
            AND event_date BETWEEN (NOW() - INTERVAL %d DAY) AND NOW()
          GROUP BY
            DATE(event_date), 
            country
        ';

        $depositCount = $this->dbal->fetchAll(sprintf($query, intval($days)));

        foreach ($depositCount as $row) {
            foreach ($data as $k => $v) {
                if ($v['date'] == $row['date'] && $v['country'] == $row['country']) {
                    $data[$k]['deposit_count'] = $row['times_deposited'];
                }
            }
        }

        return $data;
    }
}
