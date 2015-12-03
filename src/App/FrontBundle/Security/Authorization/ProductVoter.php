<?php
namespace App\FrontBundle\Security\Authorization;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\FrontBundle\Entity\User;

class ProductVoter extends AbstractVoter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    protected function getSupportedAttributes()
    {
        return array(self::VIEW, self::EDIT, self::DELETE);
    }

    protected function getSupportedClasses()
    {
        return array('App\FrontBundle\Entity\Product');
    }

    protected function isGranted($attribute, $product, $user = null)
    {
        if(!$product->getUser()){
            return true;
        }

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof User) {
            throw new \LogicException('The user is somehow not the User class!');
        }

        switch($attribute) {
            case self::VIEW:
                if (!$product->isPrivate()) {
                    return true;
                }

                break;
            case self::EDIT:
            case self::DELETE:
                if ($product->isAuthor($user)) {
                    return true;
                }

                break;     
        }

        return false;
    }
}