<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Customer
{
    /**
     * @var int
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "Field 'firstName' cannot be left blank.")
     *
     * @var string
     */
    private $firstName;

    /**
     * @Assert\NotBlank(message = "Field 'lastName' cannot be left blank.")
     *
     * @var string
     */
    private $lastName;

    /**
     * @Assert\NotBlank(message = "Field 'gender' cannot be left blank.")
     * @Assert\Choice(choices = {"m", "f", "o"}, message = "Choose a valid gender.", strict = true)
     *
     * @var string
     */
    private $gender;

    /**
     * @Assert\NotBlank(message = "Field 'email' cannot be left blank.")
     *
     * @var string
     */
    private $email;

    /**
     * @Assert\NotBlank(message = "Field 'country' cannot be left blank.")
     * @Assert\Length(min = 2, max = 2, exactMessage = "Country code should be exactly 2 characters.")
     *
     * @var string
     */
    private $country;

    /**
     * @var int
     */
    private $balance;

    /**
     * @var int
     */
    private $bonusBalance;

    /**
     * @var int
     */
    private $bonusPercentage;

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
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param int $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return int
     */
    public function getBonusBalance()
    {
        return $this->bonusBalance;
    }

    /**
     * @param int $bonusBalance
     */
    public function setBonusBalance($bonusBalance)
    {
        $this->bonusBalance = $bonusBalance;
    }

    /**
     * @return int
     */
    public function getBonusPercentage()
    {
        return $this->bonusPercentage;
    }

    /**
     * @param int $bonusPercentage
     */
    public function setBonusPercentage($bonusPercentage)
    {
        $this->bonusPercentage = $bonusPercentage;
    }

    public function __construct()
    {
        $this->balance = 0;
        $this->bonusBalance = 0;
        $this->bonusPercentage = rand(5, 20);
    }
}
