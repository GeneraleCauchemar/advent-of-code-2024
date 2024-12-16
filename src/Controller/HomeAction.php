<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\DayType;
use App\Form\Type\YearType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_home')]
class HomeAction extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        $form = $this->createFormBuilder()
                     ->add('year', YearType::class)
                     ->add('day', DayType::class)
                     ->add('create', SubmitType::class)
                     ->add('test', SubmitType::class)
                     ->add('resolve', SubmitType::class)
                     ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('resolve')->isClicked()) {
                // TODO redirect
            }

            if ($form->get('create')->isClicked()) {
                // TODO make solver and grab input
            }
        }

        return $this->render('/index.html.twig', [
            'form' => $form,
        ]);
    }
}
