<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Transaction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var \DateTime
     */
    private $eventDate;

    /**
     * @Assert\NotBlank(message = "Field 'country' cannot be left blank.")
     * @Assert\Length(min = 2, max = 2, exactMessage = "Country code should be exactly 2 characters.")
     *
     * @var string
     */
    private $country;

    /**
     * @Assert\NotBlank(message = "Field 'amount' cannot be left blank.")
     *
     * @var int
     */
    private $amount;

    /**
     * @var int
     */
    private $bonus;

    /**
     * Transaction constructor.
     *
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->setEventDate(new \DateTime());
        $this->setCustomer($customer);
        $this->setBonus(0);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return \DateTime
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * @param \DateTime $eventDate
     */
    public function setEventDate($eventDate)
    {
        $this->eventDate = $eventDate;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * @param int $bonus
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;
    }
}
