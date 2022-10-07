<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/partner')]
class PartnerController extends AbstractController
{
    #[Route('/', name: 'partner_index', methods: ['GET'])]
    public function index(PartnerRepository $partnerRepository): Response
    {
        return $this->render('partner/index.html.twig', [
            'partners' => $partnerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_partner_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PartnerRepository $partnerRepository): Response
    {
        $partner = new Partner();
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partnerRepository->save($partner, true);

            return $this->redirectToRoute('partner_index', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/{id}/edit', name: 'partner_edit_permissions', methods: ['GET', 'POST'])]
    #[ParamConverter('id', options: ['id' => 'partner_id'])]
    public function editPermissions(Request $request, Partner $partner, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partner = ($form->getData());

            $manager->persist($partner);

            $manager->flush();

            $this->addFlash('success', 'Modifications de permissions enregistré ! ');
                //TODO renvoyer vers la page du franchisé
            return $this->redirectToRoute('partner_index');
        }

        return $this->renderForm('partner/edit-permissions.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_partner_delete', methods: ['POST'])]
    public function delete(Request $request, Partner $partner, PartnerRepository $partnerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partner->getId(), $request->request->get('_token'))) {
            $partnerRepository->remove($partner, true);
        }

        return $this->redirectToRoute('partner_index', [], Response::HTTP_SEE_OTHER);
    }
}
