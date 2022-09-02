<?php

namespace App\Controller\Backend;

use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/participation')]
class BackendParticipationController extends AbstractController
{
    #[Route('/', name: 'app_backend_participation_index', methods: ['GET'])]
    public function index(ParticipationRepository $participationRepository): Response
    {
		//$participation = $participationRepository->findOneBy(['idTransaction'=>'1662131416.396']); dd($participation);
        return $this->render('backend_participation/index.html.twig', [
            'participations' => $participationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_backend_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ParticipationRepository $participationRepository): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participationRepository->add($participation, true);

            return $this->redirectToRoute('app_backend_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('backend_participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, ParticipationRepository $participationRepository): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participationRepository->add($participation, true);

            return $this->redirectToRoute('app_backend_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend_participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, ParticipationRepository $participationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->request->get('_token'))) {
            $participationRepository->remove($participation, true);
        }

        return $this->redirectToRoute('app_backend_participation_index', [], Response::HTTP_SEE_OTHER);
    }
}
