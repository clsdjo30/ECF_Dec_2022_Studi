<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Entity\PartnerPermission;
use App\Entity\Subsidiary;
use App\Entity\User;
use App\Form\ModifyPartnerPermissionType;
use App\Form\PartnerEditType;
use App\Form\PartnerType;
use App\Form\SubsidiaryNewType;
use App\Repository\PartnerRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/partner')]
class PartnerController extends AbstractController
{

    #[Route('/', name: 'partner')]
    public function index(PartnerRepository $partnerRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_TECH');

        return $this->render('partner/index.html.twig', [
            'partners' => $partnerRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'partner_new', methods: ['GET', 'POST']), isGranted('ROLE_TECH')]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher,
        FileUploader $fileUploader
    ): Response {
        //On crée le formulaire pour le franchisé
        $partner = new Partner();
        $userPartner = new User();
        $partner->setUser($userPartner);

        //On ajoute les permissions globales
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
            /** @var UploadedFile $logoPartner */
            $logoPartner = $form->get('logo')->getData();

            if ($logoPartner) {
                $newFileName = $fileUploader->upload($logoPartner);
                $subsidiary->setLogoUrl($newFileName);
            }

            //on crypte le mot de passe du manager
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

    #[Route('/{id}', name: 'partner_show', methods: ['GET']), isGranted('ROLE_STRUCTURE')]
    public function show(Partner $partner): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('partner/show.html.twig', [
            'partner' => $partner,
        ]);
    }

    #[Route('/{id}/edit', name: 'partner_edit', methods: ['GET', 'POST']), isGranted('ROLE_TECH')]
    public function editPartner(
        Request $request,
        Partner $partner,
        PartnerRepository $partnerRepository,
        FileUploader $fileUploader,
        Subsidiary $subsidiary,
        EntityManagerInterface $manager
    ): Response
    {
        $form = $this->createForm(PartnerEditType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $logoUrl */
            $logo = $form->get('logo')->getData();

            if ($logo) {
                $newFileName = $fileUploader->upload($logo);
                $subsidiary->setLogoUrl($newFileName);
            }
            $manager->persist($subsidiary);
            $partnerRepository->save($partner, true);

            $this->addFlash('success', 'Les modifications du franchisé ont bien été enregistrées ! ');

            return $this->redirectToRoute('partner_show', ['id' => $partner->getId()]);
        }

        return $this->renderForm('partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit-permissions', name: 'partner_edit_permissions', methods: ['GET', 'POST']), isGranted('ROLE_TECH')]
    public function editPermissions(
        Request $request,
        Partner $partner,
        EntityManagerInterface $manager
    ): Response
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

    #[Route('/{id}/nouvelle-salle-de-sport', name: 'partner_new_subsidiary', methods: ['GET', 'POST']), isGranted('ROLE_TECH')]
    public function addNewSubsidiary(
        Partner $partner,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher,
        FileUploader $fileUploader
    ): Response {

        $subsidiary = new Subsidiary();
        $userRoomManager = new User();
        $subsidiary->setUser($userRoomManager);
        $partner->addSubsidiary($subsidiary);

        $subsidiaryForm = $this->createForm(SubsidiaryNewType::class, $subsidiary);
        $subsidiaryForm->handleRequest($request);

        if ($subsidiaryForm->isSubmitted() && $subsidiaryForm->isValid()) {

            //on enregistre la date de creation
            $subsidiary = ($subsidiaryForm->getData())
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());

            //on charge le logo de la salle
            /** @var UploadedFile $logoUrl */
            $logo = $subsidiaryForm->get('logo')->getData();

            if ($logo) {
                $newFileName = $fileUploader->upload($logo);
                $subsidiary->setLogoUrl($newFileName);
            }
            //on crypte le mot de passe du manager
            $managerPlainTextPassword = $userRoomManager->getPassword();
            $managerHashedPassword = $passwordHasher->hashPassword(
                $userRoomManager,
                $managerPlainTextPassword
            );

            //On enregistre le user subsidiary
            $userRoomManager
                ->setPassword($managerHashedPassword)
                ->setRoles(["ROLE_SUBSIDIARY"]);


            $manager->persist($subsidiary);
            $manager->persist($userRoomManager);
            $manager->persist($partner);

            $manager->flush();

            $this->addFlash('success', 'Franchisé enregistré ! ');

            return $this->redirectToRoute('dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partner/new-subsidiary.html.twig', [
            'partner' => $subsidiary,
            'subsidform' => $subsidiaryForm,
        ]);
    }
}
