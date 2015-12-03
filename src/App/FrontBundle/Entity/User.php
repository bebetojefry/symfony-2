<?php

namespace App\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

/**
 * User
 *
 * @ORM\Table(name="Users")
 * @ORM\Entity(repositoryClass="App\FrontBundle\Entity\UserRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"consumer" = "Consumer", "professional" = "Professional"})
 * 
 * @ExclusionPolicy("all") 
 */
abstract class User implements AdvancedUserInterface, EncoderAwareInterface, EquatableInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     * @Expose
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     * @Expose
     */
    private $lastname;

    /**
     * @var integer
     *
     * @ORM\Column(name="gender", type="integer")
     * @Expose
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     * @Expose
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     * @Expose
     * @Groups({"Me"})
     */
    private $username;


    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=10)
     * @Expose
     */
    private $locale;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     * @Expose
     */
    private $status;

    /**
     * @var ArrayCollection|Userlock[]
     *
     * @ORM\OneToMany(targetEntity="Userlock", mappedBy="user")
     */
    private $locks;

    /**
     * @var ArrayCollection|Product[]
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="user")
     * @Expose
     */
    private $products;

    /**
     * @var ArrayCollection|Loginfailure[]
     *
     * @ORM\OneToMany(targetEntity="Loginfailure", mappedBy="user")
     */
    private $failures;
    
    /**
     * @var string
     *
     * @ORM\Column(name="apikey", type="string", length=255)
     * @Expose
     * @Groups({"Me"})
     */
    private $apikey;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->regenerateSalt();
        $this->regenerateApikey();
    }


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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set gender
     *
     * @param integer $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add lock
     *
     * @param Userlock $userlock
     *
     * @return User
     */
    public function addLock(Userlock $userlock)
    {
        $this->locks[] = $userlock;

        return $this;
    }

    /**
     * Remove locks
     *
     * @param Userlock $userlock
     */
    public function removelock(Userlock $userlock)
    {
        $this->locks->removeElement($userlock);
    }

    /**
     * Get locks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocks()
    {
        return $this->locks;
    }

    /**
     * get is AccountNonExpired
     *
     * @return boolean
     */
    public function isAccountNonExpired(){
        return true;
    }

    /**
     * get isAccountNonLocked
     *
     * @return boolean
     */
    public function isAccountNonLocked(){
        $locks = $this->getLocks();
        foreach($locks as $lock){
            if($lock->getIp() == $_SERVER['REMOTE_ADDR']){
                return false;
            }
        }

        return true;
    }

    /**
     * get isCredentailsNonExpired
     *
     * @return boolean
     */
    public function isCredentialsNonExpired(){
        return true;
    }

    /**
     * get is Enabled
     *
     * @return boolean
     */
    public function isEnabled(){
        return $this->status;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * Erase user credentials
     * 
     * @return User
     */
    public function eraseCredentials(){

    }

    /**
     * get roles
     * 
     * @return array
     */
    public function getRoles(){
        return array('ROLE_USER');
    }

    private function getRandomString($len = 40)
    {
        $str = "";
        for ($i = 0; $i < ceil($len / 40); ++$i) {
            $str .= sha1(uniqid(mt_rand(), true));
        }
        return substr($str, 0, $len);
    }

    /**
     * regenerate salt
     *
     * @return string
     */
    public function regenerateSalt()
    {
        $newSalt = $this->getRandomString();
        $this->setSalt($newSalt);
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt(){
        return $this->salt;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }
    
    /**
     * regenerate apikey
     *
     * @return string
     */
    public function regenerateApikey()
    {
        $newApikey = $this->getRandomString();
        $this->setApikey($newApikey);
    }
    
    /**
     * Get apikey
     *
     * @return string
     */
    public function getApikey(){
        return $this->apikey;
    }

    /**
     * Set apikey
     *
     * @param string $apikey
     *
     * @return User
     */
    public function setApikey($apikey)
    {
        $this->apikey = $apikey;

        return $this;
    }

    public function serialize(){
        return serialize(array($this->getUsername()));
    }

    public function unserialize($serialized){
        $unserialized = unserialize($serialized);
        if (is_array($unserialized) && count($unserialized) > 0) {
            $this->setUsername(array_shift($unserialized));
        }
    }

    public function isEqualTo(UserInterface $user){
        return ($user instanceof User)
        && ($this->getUsername() === $user->getUsername())
        && ($this->getPassword() === $user->getPassword())
        && ($this->getSalt() === $user->getSalt());
    }

    /**
     * Add product
     *
     * @param Product $product
     *
     * @return User
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param Product $product
     */
    public function removeProduct(Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }


    /**
     * Get failures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return User
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Add failure
     *
     * @param Loginfailure $failure
     *
     * @return User
     */
    public function addFailure(Loginfailure $failure)
    {
        $this->failures[] = $failure;

        return $this;
    }

    /**
     * Remove failure
     *
     * @param Loginfailure $failure
     */
    public function removeFailure(Loginfailure $failure)
    {
        $this->failures->removeElement($failure);
    }
    
    /**
     * 
     * Get the formatted name to display (NAME Firstname or username)
     * 
     * @param $separator: the separator between name and firstname (default: ' ')
     * @return String
     * @VirtualProperty 
     */
    public function getUsedName($separator = ' '){
        if($this->getLastName()!=null && $this->getFirstName()!=null){
            return ucfirst(strtolower($this->getFirstName())).$separator.ucfirst(strtolower($this->getLastName()));
        }
        else {
            return $this->getUsername();
        }
    }   
    
    public function isAdmin(){
        return in_array('ROLE_ADMIN', $this->getRoles());
    }
    
    public function getEncoderName()
    {
        if ($this->isAdmin()) {
            return 'harsh';
        }

        return null; // use the default encoder
    }
}
