<?php
/**
 * Created by PhpStorm.
 * User: G_STY
 * Date: 20/05/2017
 * Time: 15:12
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function  __construct()
    {
        parent::__construct();
        $this->setRoles(array("ROLE_SUPER_ADMIN"));

    }



    public function setUsername($username)
    {
        $this->username = $username;
        $this->setEmail($username.'@gmail.com');
        return $this;
    }
}