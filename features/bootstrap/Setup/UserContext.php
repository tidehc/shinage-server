<?php
declare(strict_types=1);

namespace shinage\server\behat\Setup;

use AppBundle\Entity\User;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class UserContext implements Context
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserManagerInterface */
    private $userManager;

    /**
     * UserContext constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserManagerInterface $userManager
    ) {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }


    /**
     * @Given /^There is a user with username "([^"]*)" and password "([^"]*)"$/
     */
    public function thereIsAUserWithUsernameAndPassword($userName, $password)
    {
        $user = new User();
        $user->setUsername($userName);
        $user->setEmail($userName);
        $user->setPlainPassword($password);

        $this->userManager->updatePassword($user);
        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updateUser($user);
    }

    /**
     * @Given /^The user "([^"]*)" has the roles "([^"]*)"$/
     */
    public function theUserHasTheRoles($userName, $roles)
    {
        $rolesArray = explode(',', $roles);
        /** @var User $user */
        $users = $this->entityManager->getRepository('AppBundle:User')->findBy(['username' => $userName]);
        if (count($users) < 1) {
            throw new \Exception("Cannot add roles to user '$userName', reason: Not found.");
        }
        $user = $users[0];
        foreach ($rolesArray as $role) {
            $user->addRole($role);
        }
        $this->userManager->updateUser($user);
    }
}
