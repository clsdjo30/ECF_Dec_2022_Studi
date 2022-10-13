<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Entity\PartnerPermission;
use App\Entity\Subsidiary;
use App\Entity\User;
use App\Form\ModifyPartnerPermissionType;
use App\Form\PartnerEditType;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/partner')]
class PartnerController extends AbstractController
{
    #[Route('/new', name: 'partner_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ): Response {
        //On crée le formulaire pour le franchisé
        $partner = new Partner();
        $userPartner = new User();
        $partner->setUser($userPartner);

        //On ajoute les permissions globale
        $partnerPermission = new PartnerPermission();
        $partner->addGlobalPermission($partnerPermission);

        $subsidiary = new Subsidiary();
        $userRoomManager = new User();
        $subsidiary->setUser($userRoomManager);
        $partner->addSubsidiary($subsidiary);

        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on enregistre la date de creation
            $partner = ($form->getData())
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());

            $partnerPermission->setIsActive(true);
            //on hash le mot de passe du partner
            $partnerPlainTextPassword = $userPartner->getPassword();
            $partnerHashedPassword = $passwordHasher->hashPassword(
                $userPartner,
                $partnerPlainTextPassword
            );

            //On enregistre le user partner
            $userPartner
                ->setPassword($partnerHashedPassword)
                ->setRoles(["ROLE_PARTNER"]);

            //on charge le logo de la salle
            /** @var UploadedFile $logoUrl */
            $logo = $form->get('logo')->getData();

            if ($logo) {
                $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid('000', true).'.'.$logo->guessExtension();

                try {
                    $logo->move(
                        $this->getParameter('logo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Un Problème est survenu lors du téléchargement de votre fichier ! ');
                }
                $subsidiary->setLogoUrl($newFilename);
            }//on hash le mot de passe du manager
            $managerPlainTextPassword = $userRoomManager->getPassword();
            $managerHashedPassword = $passwordHasher->hashPassword(
                $userPartner,
                $managerPlainTextPassword
            );

            //On enregistre le user subsidiary
            $userRoomManager
                ->setPassword($managerHashedPassword)
                ->setRoles(["ROLE_SUBSIDIARY"]);

            //on enregistre la date de creation
            $subsidiary->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());


            $manager->persist($userPartner);
            $manager->persist($partnerPermission);
            $manager->persist($partner);
            $manager->persist($subsidiary);
            $manager->persist($userRoomManager);

            $manager->flush();

            $this->addFlash('success', 'Franchisé enregistré ! ');

            return $this->redirectToRoute('dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'partner_show', methods: ['GET'])]
    public function show(Partner $partner): Response
    {
        return $this->render('partner/show.html.twig', [
            'partner' => $partner,
        ]);
    }

    #[Route('/{id}/edit', name: 'partner_edit', methods: ['GET', 'POST'])]
    public function editPartner(Request $request, Partner $partner, PartnerRepository $partnerRepository): Response
    {
        $form = $this->createForm(PartnerEditType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partnerRepository->save($partner, true);

            $this->addFlash('success', 'Les modifications du franchisé ont bien été enregistrées ! ');

            return $this->redirectToRoute('partner_show', ['id' => $partner->getId()]);
        }

        return $this->renderForm('partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit-permissions', name: 'partner_edit_permissions', methods: ['GET', 'POST'])]
    public function editPermissions(Request $request, Partner $partner, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ModifyPartnerPermissionType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partner = ($form->getData());

            $manager->persist($partner);

            $manager->flush();

            $this->addFlash('success', 'Modifications de permissions enregistré ! ');

            //TODO renvoyer vers la page du franchisé
            return $this->redirectToRoute('partner_show', ['id' => $partner->getId()]);
        }

        return $this->renderForm('partner/edit-permissions.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }
}
