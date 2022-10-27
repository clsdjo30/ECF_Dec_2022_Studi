<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Partner;
use App\Entity\PartnerPermission;
use App\Entity\Subsidiary;
use App\Entity\SubsidiaryPermission;
use App\Entity\User;
use App\Form\ModifyPartnerPermissionType;
use App\Form\PartnerEditType;
use App\Form\PartnerType;
use App\Form\SearchFormType;
use App\Form\SubsidiaryNewType;
use App\Repository\PartnerRepository;
use App\Repository\PermissionRepository;
use App\Repository\SubsidiaryRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;


#[Route('/partner')]
class PartnerController extends AbstractController
{
    use ResetPasswordControllerTrait;


    #[Route('/', name: 'partner')]
    public function index(
        PartnerRepository $partnerRepository,

        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_TECH');

        //en envoi la pagination
        $data = new SearchData();
        $data->page = $request->get('page', 1);

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $partners = $partnerRepository->findPartnerBySearch($data);

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView(
                    'components/dashboard/content-dashboard/_partner-card.html.twig',
                    ['partners' => $partners]
                ),
                'sorting' => $this->renderView('partner/_sorting.html.twig', ['partners' => $partners]),

            ]);
        }

        return $this->render('partner/index.html.twig', [
            'partners' => $partners,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ResetPasswordExceptionInterface
     */
    #[Route('/new', name: 'partner_new', methods: ['GET', 'POST']), isGranted('ROLE_TECH')]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher,
        ResetPasswordController $resetPasswordController,
        MailerInterface $mailer,
        PermissionRepository $repository

    ): Response {
        //On crée le formulaire pour le franchisé
        $partner = new Partner();
        $userPartner = new User();
        $partner->setUser($userPartner);

        //On ajoute les permissions globales
        $partnerPermission = new PartnerPermission();
        $permission = $repository->findOneBy(['name' => 'permission.name']);
        $partnerPermission->setPermission($permission);
        $partner->addGlobalPermission($partnerPermission);

        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on enregistre la date de creation
            $partner = ($form->getData())
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());

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

            $manager->persist($userPartner);
            $manager->persist($partnerPermission);
            $manager->persist($partner);
            $manager->flush();


            $resetToken = $resetPasswordController->resetPasswordHelper->generateResetToken($partner->getUser());
            $connexionEmail = $partner->getUser()->getEmail();
            $email = (new TemplatedEmail())
                ->from(new Address('contact@c-and-com.studio', 'Lions Fitness Club'))
                ->to($userPartner->getEmail())
                ->subject('Your password reset request')
                ->htmlTemplate('reset_password/email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'connexionId' => $connexionEmail,
                ]);

            $mailer->send($email);
            $this->setTokenObjectInSession($resetToken);


            $this->addFlash('success', 'Franchisé enregistré ! ');

            return $this->redirectToRoute('partner_show', ['id' => $partner->getId()]);
        }

        return $this->renderForm('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'partner_show', methods: ['GET']), isGranted('ROLE_PARTNER')]
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
        PartnerRepository $partnerRepository
    ): Response {
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

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/edit-permissions', name: 'partner_edit_permissions', methods: [
        'GET',
        'POST',
    ]), isGranted('ROLE_TECH')]
    public function editPermissions(
        Request $request,
        Partner $partner,
        EntityManagerInterface $manager,
        MailerInterface $mailer
    ): Response {
        $form = $this->createForm(ModifyPartnerPermissionType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partner = ($form->getData());
            $partnerEmail = $partner->getUser()->getEmail();
            $partnerName = $partner->getUser();
            $permissions = $partner->getGlobalPermissions();


            $modificationPermissionEmail = (new TemplatedEmail())
                ->from(new Address('contact@c-and-com.studio', 'Lions Fitness Club'))
                ->to($partnerEmail)
                ->subject('Vos permission ont été modifié !')
                ->context([
                    'partner' => $partnerEmail,
                    'partnerName' => $partnerName,
                    'permissions' => $permissions,
                ])
                ->text('Pour plus de renseignements, merci de contacté notre équipe par mail.!')
                ->htmlTemplate('email/permissions-modification.html.twig');


            $manager->persist($partner);
            $mailer->send($modificationPermissionEmail);

            $manager->flush();

            $this->addFlash('success', 'Modifications de permissions enregistré ! ');


            return $this->redirectToRoute('partner_show', ['id' => $partner->getId()]);
        }

        return $this->renderForm('partner/edit-permissions.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/subsidiary/{id}', name: 'subsidiary_show', methods: ['GET']), isGranted('ROLE_SUBSIDIARY')]
    public function showSubsidiary(SubsidiaryRepository $subsidiaryRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $sub = $subsidiaryRepository->findOneBy([
            'user' => $user,
        ]);


        return $this->render('partner/show.html.twig', [
            'subsidiary' => $sub,
        ]);
    }

    /**
     * @throws ResetPasswordExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/nouvelle-salle-de-sport', name: 'partner_new_subsidiary', methods: [
        'GET',
        'POST',
    ]), isGranted('ROLE_TECH')]
    public function addNewSubsidiary(
        Partner $partner,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher,
        FileUploader $fileUploader,
        ResetPasswordController $resetPasswordController,
        MailerInterface $mailer,

    ): Response {

        $subsidiary = new Subsidiary();
        $userRoomManager = new User();

        foreach ($partner->getGlobalPermissions() as $globalActivePermission) {

            if (!$globalActivePermission->isIsActive()) {
                $addingPermissions = new SubsidiaryPermission();
                $addingPermissions->setPartnerPermission($globalActivePermission);
                $subsidiary->addSubsidiaryPermission($addingPermissions);

            }
        }


        $subsidiary->setUser($userRoomManager);

        $subsidiaryForm = $this->createForm(SubsidiaryNewType::class, $subsidiary);
        $subsidiaryForm->handleRequest($request);

        if ($subsidiaryForm->isSubmitted() && $subsidiaryForm->isValid()) {
            //on enregistre la date de creation
            $subsidiary = ($subsidiaryForm->getData())
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());

            $partner->addSubsidiary($subsidiary);

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


            $manager->persist($userRoomManager);
            $manager->persist($addingPermissions);
            $manager->persist($partner);
            $manager->persist($subsidiary);


            $manager->flush();

            $resetToken = $resetPasswordController->resetPasswordHelper->generateResetToken($subsidiary->getUser());
            $connexionEmail = $subsidiary->getUser()->getEmail();
            $email = (new TemplatedEmail())
                ->from(new Address('contact@c-and-com.studio', 'Lions Fitness Club'))
                ->to($userRoomManager->getEmail())
                ->subject('Your password reset request')
                ->htmlTemplate('reset_password/email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'connexionId' => $connexionEmail,
                ]);

            $mailer->send($email);
            $this->setTokenObjectInSession($resetToken);

            $this->addFlash('success', 'Une nouvelle salle de sport a bien été ajouté au franchisé! ');

            return $this->redirectToRoute('partner_show', ['id' => $partner->getId()]);
        }

        return $this->renderForm('partner/new-subsidiary.html.twig', [
            'partner' => $subsidiary,
            'form' => $subsidiaryForm,
        ]);
    }
}
