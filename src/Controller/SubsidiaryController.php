<?php

namespace App\Controller;

use App\Entity\Subsidiary;
use App\Form\SubsidiaryNewType;
use App\Repository\SubsidiaryRepository;
use App\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    #[Route('/{id}/edit', name: 'subsidiary_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Subsidiary $subsidiary,
        SubsidiaryRepository $subsidiaryRepository,
        FileUploader $fileUploader

    ): Response
    {
        $form = $this->createForm(SubsidiaryNewType::class, $subsidiary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on charge le logo de la salle
            /** @var UploadedFile $logoUrl */
            $logo = $form->get('logo')->getData();

            if ($logo) {
                $newFileName = $fileUploader->upload($logo);
                $subsidiary->setLogoUrl($newFileName);
            }
            $subsidiaryRepository->save($subsidiary, true);

            $this->addFlash('success', 'Modifications de la salle de sport bien enregistré ! ');
            return $this->redirectToRoute('partner');
        }

        return $this->renderForm('subsidiary/edit.html.twig', [
            'subsidform' => $form,
        ]);
    }

}
