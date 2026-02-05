<?php

namespace App\Controller;

use App\Entity\FinancialProfile;
use App\Form\FinancialProfileType;
use App\Repository\FinancialProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/edit', name: 'profile_edit')]
    public function edit(
        Request $request,
        FinancialProfileRepository $repository,
        EntityManagerInterface $em,
    ): Response {
        $profile = $repository->getOrCreate();
        $form = $this->createForm(FinancialProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', '¡Perfil guardado exitosamente!');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
            'profile' => $profile,
        ]);
    }

    #[Route('/profile/reset', name: 'profile_reset', methods: ['POST'])]
    public function reset(
        Request $request,
        FinancialProfileRepository $repository,
        EntityManagerInterface $em,
    ): Response {
        if (!$this->isCsrfTokenValid('profile_reset', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF inválido.');
            return $this->redirectToRoute('profile_edit');
        }

        $profile = $repository->getOrCreate();

        $profile->setName('Mi Perfil');
        $profile->setAge(30);
        $profile->setIncome(['employment' => 0, 'investments' => 0, 'other' => 0]);
        $profile->setExpenses([
            'housing' => 0, 'transportation' => 0, 'food' => 0,
            'utilities' => 0, 'insurance' => 0, 'healthcare' => 0,
            'entertainment' => 0, 'personal' => 0, 'other' => 0,
        ]);
        $profile->setFinancialAssets([]);
        $profile->setPhysicalAssets([]);
        $profile->setLiabilities([]);

        $em->flush();
        $this->addFlash('info', 'Perfil reseteado a valores por defecto.');

        return $this->redirectToRoute('profile_edit');
    }
}
