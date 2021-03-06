<?php
declare(strict_types=1);

/*
 * Copyright 2018 by Michael Zapf.
 * Licensed under MIT. See file /LICENSE.
 */

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

// @TODO I think this can be refactored and simplified.
class OwnerType extends AbstractType
{
    /** @var EntityManager */
    protected $em;

    /** @var TokenStorage */
    protected $tokenStorage;

    /** @var mixed */
    protected $entity;

    public function __construct(EntityManager $em, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param mixed[]|array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        // save entity that should be owned
        $this->entity = $options['ownable'];

        if (null !== $this->entity) {
            // Event: While building set default value
            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event): void {
                    $event->setData($this->entity->getOwner()->getId());
                }
            );

            // Event: After submission, modify entity
            $builder->addEventListener(
                FormEvents::SUBMIT,
                function (FormEvent $event): void {
                    $entity = $this->entity;
                    $owner = $event->getData();

                    $aOwner = explode(':', $owner);
                    switch ($aOwner[0]) {
                        case 'user':
                            $entity->setOwner($this->em->find('AppBundle:User', $aOwner[1]));
                            break;
                        default:
                            // Error above. Use current user as default.
                            $user = $this->tokenStorage->getToken()->getUser();
                            $entity->setOwner($user);
                            break;
                    }
                }
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // TODO{s:0} Unterscheiden ob Admin oder normaler Nutzer
        // normal user
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $choices = ['me' => 'user:' . $user->getId()];

        /** @var User $orga */
        foreach ($user->getOrganizations() as $orga) {
            $choices['Organisation: ' . $orga->getName()] = 'orga:' . $orga->getId();
        }

        $resolver->setRequired('ownable');

        $resolver->setDefaults([
            'choices'   => $choices,
            'mapped'    => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
