<?php
declare(strict_types=1);

/*
 * Copyright 2018 by Michael Zapf.
 * Licensed under MIT. See file /LICENSE.
 */

namespace AppBundle\Entity;

use AppBundle\UserType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\User as BaseUser;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints as RollerworksPassword;

class User extends BaseUser
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $userType = UserType::USER_TYPE_USER;

    /** @var string */
    protected $name = '';

    /** @var Collection */
    private $organizations;

    /** @var Collection */
    private $users;

    /**
     * @var string
     *
     * @RollerworksPassword\PasswordRequirements(requireLetters=true, requireNumbers=true)
     */
    protected $password;

    /**
     * @var string
     *
     * @RollerworksPassword\PasswordRequirements(requireLetters=true, requireNumbers=true)
     */
    protected $plainPassword;

    public function __construct()
    {
        parent::__construct();
        $this->organizations = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        parent::setUsername($email);
        parent::setUsernameCanonical($email);
        return parent::setEmail($email);
    }

    /**
     * @return string[]|array
     */
    public function getAllowedPoolPaths(): array
    {
        // @TODO Remove from here and move to own service
        $r = [];
        $r[] = 'user-' . $this->id;

        $orgas = $this->getOrganizations();
        foreach ($orgas as $orga) { /* @var User $orga */
            $r[] = 'orga-' . $orga->getId();
        }

        return $r;
    }

    public function isPoolFileAllowed(string $path): bool
    {
        // @TODO Remove from here and move to own service
        $file = ltrim($path, "/\r\n\t ");
        $base = substr($file, 0, strpos($file, '/'));
        return \in_array($base, $this->getAllowedPoolPaths(), true);
    }

    public function isPresentationAllowed(Presentation $presentation): bool
    {
        // @TODO Remove from here and move to own service

        if ($presentation->getOwner() === $this) {
            return true;
        }

        $orgas = $this->getOrganizations();
        foreach ($orgas as $orga) { /** @var User $orga */
            if ($presentation->getOwner() === $orga) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Presentation[]|array
     */
    public function getPresentations(EntityManager $em): array
    {
        // @TODO Remove from here and move to own service
        $user = $this;
        $rep = $em->getRepository('AppBundle:Presentation');
        $pres = [];

        $pres_user = $rep->findBy(['owner' => $user]);

        foreach ($pres_user as $p) {
            $pres['me'][] = $p;
        }

        $orgas = $user->getOrganizations();
        foreach ($orgas as $orga) { /** @var User $orga */
            $pres_orga = $rep->findBy(['owner' => $orga]);
            foreach ($pres_orga as $p) {
                $pres[$orga->getName()][] = $p;
            }
        }

        return $pres;
    }

    public static function generateToken(): string
    {
        // @TODO Remove from here and move to own service
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function addOrganization(self $organization): self
    {
        $this->organizations[] = $organization;

        return $this;
    }

    public function removeOrganization(self $organization): void
    {
        $this->organizations->removeElement($organization);
    }

    public function getOrganizations(): Collection
    {
        return $this->organizations;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): self
    {
        $this->users = $users;
        return $this;
    }

    public function addUser(self $user): self
    {
        $this->users[] = $user;

        return $this;
    }

    public function removeUser(self $user): void
    {
        $this->users->removeElement($user);
    }
}
