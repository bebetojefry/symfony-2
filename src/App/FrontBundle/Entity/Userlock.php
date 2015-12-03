<?php

namespace App\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Userlock
 *
 * @ORM\Table(name="Userlocks")
 * @ORM\Entity(repositoryClass="App\FrontBundle\Entity\UserlockRepository")
 */
class Userlock
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="locks"))
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lockedOn", type="datetime")
     */
    private $lockedOn;


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
     * @return Userlock
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
     * Set ip
     *
     * @param string $ip
     *
     * @return Userlock
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

    /**
     * Set lockedOn
     *
     * @param \DateTime $lockedOn
     *
     * @return Userlock
     */
    public function setLockedOn($lockedOn)
    {
        $this->lockedOn = $lockedOn;

        return $this;
    }

    /**
     * Get lockedOn
     *
     * @return \DateTime
     */
    public function getLockedOn()
    {
        return $this->lockedOn;
    }
}

