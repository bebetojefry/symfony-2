<?php

namespace App\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Loginfailure
 *
 * @ORM\Table(name="Loginfailures")
 * @ORM\Entity(repositoryClass="App\FrontBundle\Entity\LoginfailureRepository")
 */
class Loginfailure
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="failures"))
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="failed_on", type="datetime")
     */
    private $failedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Loginfailure
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set failedOn
     *
     * @param \DateTime $failedOn
     *
     * @return Loginfailure
     */
    public function setFailedOn($failedOn)
    {
        $this->failedOn = $failedOn;

        return $this;
    }

    /**
     * Get failedOn
     *
     * @return \DateTime
     */
    public function getFailedOn()
    {
        return $this->failedOn;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Loginfailure
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
}
