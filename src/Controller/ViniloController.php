<?php

namespace App\Controller;

use App\Entity\Vinilo;
use App\Form\ViniloType;
use App\Repository\ViniloRepository;
use App\Service\DiscogsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/vinilo')]
final class ViniloController extends AbstractController
{
    #[Route(name: 'app_vinilo_index', methods: ['GET'])]
    public function index(ViniloRepository $viniloRepository): Response
    {
        return $this->render('vinilo/index.html.twig', [
            'vinilos' => $viniloRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vinilo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vinilo = new Vinilo();
        $form = $this->createForm(ViniloType::class, $vinilo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vinilo);
            $entityManager->flush();

            return $this->redirectToRoute('app_vinilo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vinilo/new.html.twig', [
            'vinilo' => $vinilo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vinilo_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(Vinilo $vinilo, DiscogsService $discogs): Response
    {
        // Buscar en Discogs por el primer artista y el título del vinilo
        $artista = $vinilo->getArtistas()->first();
        $nombreArtista = $artista ? $artista->getNombre() : '';

        $discogsData = $discogs->getReleaseData($nombreArtista, $vinilo->getTitulo());

        return $this->render('vinilo/show.html.twig', [
            'vinilo'      => $vinilo,
            'discogsData' => $discogsData,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vinilo_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Vinilo $vinilo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ViniloType::class, $vinilo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vinilo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vinilo/edit.html.twig', [
            'vinilo' => $vinilo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vinilo_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Vinilo $vinilo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vinilo->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vinilo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vinilo_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/demo', name: 'app_vinilo_demo', methods: ['GET'])]
    public function demo(): Response
    {
        return $this->render('vinilo/show.html.twig', [
            'vinilo'      => null,
            'discogsData' => null,
        ]);
    }
}
