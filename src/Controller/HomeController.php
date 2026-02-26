<?php
/*
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
*/


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ViniloRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ViniloRepository $viniloRepository): Response
    {
        // Obtener las últimas 6 novedades por fecha de lanzamiento
        $novedades = $viniloRepository->findBy([], ['fecha_lanzamiento' => 'DESC'], 6);

        // Obtener 4 ofertas (los más baratos como ejemplo)
        $ofertas = $viniloRepository->findBy([], ['precio' => 'ASC'], 4);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'novedades' => $novedades,
            'ofertas' => $ofertas,
        ]);
    }

    #[Route('/sobre-nosotros', name: 'app_sobre_nosotros')]
    public function sobreNosotros(): Response
    {
        return $this->render('home/sobre_nosotros.html.twig');
    }

    #[Route('/contacto', name: 'app_contacto', methods: ['GET', 'POST'])]
    public function contacto(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->addFlash('success', 'Mensaje enviado correctamente.');
            return $this->redirectToRoute('app_contacto');
        }
        
        return $this->render('home/contacto.html.twig');
    }
}
