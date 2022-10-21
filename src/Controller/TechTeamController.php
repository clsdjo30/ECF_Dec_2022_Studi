<?php

namespace App\Controller;

use App\Entity\TechTeam;
use App\Entity\User;
use App\Form\TechFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/technicien')]
class TechTeamController extends AbstractController
{


    #[Route('/new', name: 'new_technicien', methods: ['GET', 'POST']), isGranted('ROLE_TECH')]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher,
        ): Response {
        //On crée le formulaire pour le franchisé
        $technicien = new TechTeam();
        $userTech = new User();
        $technicien->setUser($userTech);

        $form = $this->createForm(TechFormType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $technicien = ($form->getData());

            //on hash le mot de passe du partner
            $techPlainTextPassword = $userTech->getPassword();
            $partnerHashedPassword = $passwordHasher->hashPassword(
                $userTech,
                $techPlainTextPassword
            );

            //On enregistre le user partner
            $userTech
                ->setPassword($partnerHashedPassword)
                ->setRoles(["ROLE_TECH"]);

            $manager->persist($technicien);
            $manager->persist($userTech);
            $manager->flush();

            $this->addFlash('success', 'Technicien enregistré ! ');

            return $this->redirectToRoute('partner', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tech_team/index.html.twig', [
            'form' => $form,
        ]);
    }
}
