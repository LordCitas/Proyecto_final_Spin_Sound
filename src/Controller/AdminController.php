<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ViniloRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/panel', name: 'app_admin_panel')]
    public function panel(ViniloRepository $viniloRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $vinilos = $viniloRepository->findAll();
        $novedades = $viniloRepository->findBy(['esNovedad' => true], ['fecha_lanzamiento' => 'DESC']);
        
        return $this->render('admin/panel.html.twig', [
            'vinilos' => $vinilos,
            'novedades' => $novedades,
        ]);
    }

    #[Route('/admin/vinilo/{id}/toggle-novedad', name: 'app_admin_toggle_novedad', methods: ['POST'])]
    public function toggleNovedad(int $id, ViniloRepository $viniloRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $vinilo = $viniloRepository->find($id);
        if (!$vinilo) {
            throw $this->createNotFoundException('Vinilo no encontrado');
        }

        $vinilo->setEsNovedad(!$vinilo->isEsNovedad());
        $em->flush();

        $this->addFlash('success', $vinilo->isEsNovedad() 
            ? 'Vinilo añadido a novedades' 
            : 'Vinilo quitado de novedades'
        );

        return $this->redirectToRoute('app_admin_panel');
    }
}
