<?php

namespace App\Controller;

use App\Entity\Subsidiary;
use App\Form\ModifySubsidiaryPermissionType;
use App\Form\SubsidiaryEditType;
use App\Repository\SubsidiaryRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/subsidiary')]
class SubsidiaryController extends AbstractController
{
    #[Route('/', name: 'subsidiary_index', methods: ['GET'])]
    public function index(SubsidiaryRepository $subsidiaryRepository): Response
    {
        return $this->render('subsidiary/show.html.twig', [
            'subsidiaries' => $subsidiaryRepository->findAll(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/edit', name: 'subsidiary_edit', methods: ['GET', 'POST']), isGranted('ROLE_TECH')]
    public function edit(
        Request $request,
        Subsidiary $subsidiary,
        SubsidiaryRepository $subsidiaryRepository,
        FileUploader $fileUploader,
        MailerInterface $mailer,
        EntityManagerInterface $manager

    ): Response {

        $form = $this->createForm(SubsidiaryEditType::class, $subsidiary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subsidiary = $form->getData();
            //on charge le logo de la salle
            /** @var UploadedFile $logoUrl */
            $logo = $form->get('logo')->getData();

            if ($logo) {
                $newFileName = $fileUploader->upload($logo);
                $subsidiary->setLogoUrl($newFileName);
            }

            $manager->persist($subsidiary);
            $manager->flush();
            $subsidiaryEmail = $subsidiary->getUser()->getEmail();
            $subsidiaryName = $subsidiary->getUser();
            $permissions = $subsidiary->getSubsidiaryPermissions();
            $modificationPermissionEmail = (new TemplatedEmail())
                ->from(new Address('contact@c-and-com.studio', 'Lions Fitness Club'))
                ->to($subsidiaryEmail)
                ->subject('Vos permission ont été modifié !')
                ->context([
                    'partner' => $subsidiaryEmail,
                    'partnerName' => $subsidiaryName,
                    'permissions' => $permissions,
                ])
                ->text('Pour plus de renseignements, merci de contacté notre équipe par mail.!')
                ->htmlTemplate('email/permissions-modification.html.twig');

            $mailer->send($modificationPermissionEmail);

            $this->addFlash('success', 'Modifications de la salle de sport bien enregistré ! ');

            return $this->redirectToRoute('partner_show', ['id' => $subsidiary->getPartner()->getId()]);
        }

        return $this->renderForm('subsidiary/edit.html.twig', [
            'form' => $form,
            'subsidiary' => $subsidiary,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/edit-permissions', name: 'subsidiary_edit_permissions', methods: [
        'GET',
        'POST',
    ]), isGranted('ROLE_TECH')]
    public function editPermissions(
        Request $request,
        Subsidiary $subsidiary,
        EntityManagerInterface $manager,
        MailerInterface $mailer
    ): Response {


        $form = $this->createForm(ModifySubsidiaryPermissionType::class, $subsidiary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subsidiary = ($form->getData());
            $subsidiaryEmail = $subsidiary->getUser()->getEmail();
            $subsidiaryName = $subsidiary->getUser();
            $permissions = $subsidiary->getSubsidiaryPermissions();


            $modificationPermissionEmail = (new TemplatedEmail())
                ->from(new Address('contact@c-and-com.studio', 'Lions Fitness Club'))
                ->to($subsidiaryEmail)
                ->subject('Vos permission ont été modifié !')
                ->context([
                    'partner' => $subsidiaryEmail,
                    'partnerName' => $subsidiaryName,
                    'permissions' => $permissions,
                ])
                ->text('Pour plus de renseignements, merci de contacté notre équipe par mail.!')
                ->htmlTemplate('email/permissions-modification.html.twig');


            $manager->persist($subsidiary);
            $mailer->send($modificationPermissionEmail);

            $manager->flush();

            $this->addFlash('success', 'Modifications de permissions enregistré ! ');


            return $this->redirectToRoute('partner_show', ['id' => $subsidiary->getPartner()->getId()]);
        }

        return $this->renderForm('subsidiary/edit-permissions.html.twig', [
            'partner' => $subsidiary,
            'form' => $form,
        ]);
    }

}
