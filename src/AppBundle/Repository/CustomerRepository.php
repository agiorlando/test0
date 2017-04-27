<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Transaction;
use AppBundle\Exception\InsufficientFundsError;
use AppBundle\Exception\NotUniqueEmailError;
use Doctrine\DBAL\Connection;

class CustomerRepository
{
    private $dbal;

    /**
     * CustomerRepository constructor.
     *
     * @param Connection $dbal
     */
    public function __construct($dbal)
    {
        $this->dbal = $dbal;
    }

    /**
     * Persist a customer object to the database - this may either
     * insert or update depending on the persistence status of the object
     * provided.
     *
     * @param Customer $customer
     *
     * @return Customer
     */
    public function persist(Customer $customer)
    {
        $this->dbal->beginTransaction();

        try {
            $c = $this->getByEmail($customer->getEmail());

            if (!is_null($c) && $c->getEmail() == $customer->getEmail()) {
                if (is_null($customer->getId()) || $customer->getId() != $c->getId()) {
                    throw new NotUniqueEmailError();
                }
            }

            if (is_null($customer->getId())) {
                $customer = $this->insert($customer);
            } else {
                $customer = $this->update($customer);
            }

            $this->dbal->commit();
        } catch (\Exception $e) {
            // Roll back the current transaction, and rethrow the exception
            // to let the application decide what to do next
            $this->dbal->rollBack();
            throw $e;
        }

        return $customer;
    }

    /**
     * Insert a Customer row into the database, and write the inserted ID
     * back into the entity object.
     *
     * @param Customer $customer
     *
     * @return Customer
     */
    private function insert(Customer $customer)
    {
        $this->dbal->insert('customers', [
            'first_name' => $customer->getFirstName(),
            'last_name' => $customer->getLastName(),
            'gender' => $customer->getGender(),
            'email' => $customer->getEmail(),
            'country' => $customer->getCountry(),
            'balance' => $customer->getBalance(),
            'balance_bonus' => $customer->getBonusBalance(),
            'bonus_pct' => $customer->getBonusPercentage(),
        ]);

        $customer->setId(intval($this->dbal->lastInsertId()));

        return $customer;
    }

    /**
     * Update a Customer row in the database.
     *
     * @param Customer $customer
     *
     * @return Customer
     */
    private function update(Customer $customer)
    {
        $this->dbal->update('customers', [
            'first_name' => $customer->getFirstName(),
            'last_name' => $customer->getLastName(),
            'gender' => $customer->getGender(),
            'email' => $customer->getEmail(),
            'country' => $customer->getCountry(),
            'balance' => $customer->getBalance(),
            'balance_bonus' => $customer->getBonusBalance(),
        ], ['id' => $customer->getId()]);

        return $customer;
    }

    /**
     * Retrieve a single customer by ID or return NULL.
     *
     * @param int $customerId
     *
     * @return Customer
     */
    public function getById($customerId)
    {
        $row = $this->dbal->fetchAssoc('SELECT * FROM customers WHERE id = ?', [$customerId]);

        if ($row === false) {
            return null;
        }

        return $this->hydrateCustomer($row, new Customer());
    }

    /**
     * Retrieve a single customer by ID or return NULL.
     *
     * @param string $email
     *
     * @return Customer
     */
    public function getByEmail($email)
    {
        $row = $this->dbal->fetchAssoc('SELECT * FROM customers WHERE email = ?', [$email]);

        if ($row === false) {
            return null;
        }

        return $this->hydrateCustomer($row, new Customer());
    }

    /**
     * Hydrate a Customer entity based on an array representing a
     * row from the database table.
     *
     * @param array    $row
     * @param Customer $obj
     *
     * @return Customer
     */
    private function hydrateCustomer($row, Customer $obj)
    {
        $obj->setId($row['id']);
        $obj->setFirstName($row['first_name']);
        $obj->setLastName($row['last_name']);
        $obj->setGender($row['gender']);
        $obj->setEmail($row['email']);
        $obj->setCountry($row['country']);
        $obj->setBalance($row['balance']);
        $obj->setBonusBalance($row['balance_bonus']);
        $obj->setBonusPercentage($row['bonus_pct']);

        return $obj;
    }

    /**
     * Update the balance of a customer, but throw an exception if the balance
     * would become negative.
     *
     * @param Customer    $customer
     * @param Transaction $transaction
     *
     * @throws InsufficientFundsError
     */
    public function updateBalance(Customer $customer, Transaction $transaction)
    {
        if ($customer->getBalance() + $transaction->getAmount() < 0) {
            throw new InsufficientFundsError();
        }

        $customer->setBalance($customer->getBalance() + $transaction->getAmount());
        $customer->setBonusBalance($customer->getBonusBalance() + $transaction->getBonus());

        $this->update($customer);
    }
}
