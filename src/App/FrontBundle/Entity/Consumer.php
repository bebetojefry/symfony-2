<?php

namespace App\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Consumer
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\FrontBundle\Entity\UserRepository")
 */
class Consumer extends User
{  
    /**
     * Constructor
     */
    public function __construct($fname = '', $lname = '', $gender = '') {
        $this->setFirstname($fname);
        $this->setLastname($lname);
        $this->setGender($gender);
        parent::__construct();
    }
    
    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_CONSUMER', 'ROLE_USER');
    }
}
