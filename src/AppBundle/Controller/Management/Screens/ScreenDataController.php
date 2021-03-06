<?php
declare(strict_types=1);

/*
 * Copyright 2018 by Michael Zapf.
 * Licensed under MIT. See file /LICENSE.
 */

namespace AppBundle\Controller\Management\Screens;

use AppBundle\Entity\Screen;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ScreenDataController extends Controller
{
    /**
     * @Route("/manage/screen/{guid}", name="management-screen-data", requirements={"guid": "[^/]*"})
     */
    public function indexAction(string $guid): Response
    {
        // TODO Check if logged in user is allowed to edit screen.

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $screen = $em->find(Screen::class, $guid);

        return $this->render('manage/screens/data.html.twig', [
            'screen' => $screen,
        ]);
    }
}
