<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Customer;
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
        $c = $this->getByEmail($customer->getEmail());

        if (!is_null($c) && $c->getEmail() == $customer->getEmail()) {
            throw new \Exception('E-mail already exists.');
        }

        $this->dbal->beginTransaction();

        try {
            if (is_null($customer->getId())) {
                $customer = $this->insert($customer);
            } else {
                $customer = $this->update($customer);
            }

            $this->dbal->commit();
        } catch (\Exception $e) {
            $this->dbal->rollBack();
        }

        return $customer;
    }

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

    private function update(Customer $customer)
    {
        $this->dbal->update('customers', [
            'fist_name' => $customer->getFirstName(),
            'last_name' => $customer->getLastName(),
            'gender' => $customer->getGender(),
            'email' => $customer->getEmail(),
            'country' => $customer->getCountry(),
        ], [$customer->getId()]);

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
}