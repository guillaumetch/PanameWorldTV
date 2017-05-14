<?php
/**
 * Created by PhpStorm.
 * User: G_STY
 * Date: 13/05/2017
 * Time: 21:57
 */

namespace AppBundle\Entity\Security;


use AppBundle\Entity\User;

class UserProvider
{

    public function loadUserByUsername($username)
    {
        // make a call to your webservice here
        $user = $this->findOneByUsernameOrEmail($username);

        if (!$user) {
            throw new UsernameNotFoundException('No user found for username '.$username);
        }

        return $user;
    }

}