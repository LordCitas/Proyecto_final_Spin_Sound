<?php

namespace App\Controller;

use App\Entity\Carrito;
use App\Entity\DetalleCarrito;
use App\Form\CarritoType;
use App\Repository\CarritoRepository;
use App\Repository\ViniloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/carrito')]
final class CarritoController extends AbstractController
{
    #[Route(name: 'app_carrito_index', methods: ['GET'])]
    public function index(CarritoRepository $carritoRepository): Response
    {
        return $this->render('carrito/index.html.twig', [
            'carritos' => $carritoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_carrito_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $carrito = new Carrito();
        $form = $this->createForm(CarritoType::class, $carrito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($carrito);
            $entityManager->flush();

            return $this->redirectToRoute('app_carrito_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carrito/new.html.twig', [
            'carrito' => $carrito,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carrito_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(Carrito $carrito): Response
    {
        return $this->render('carrito/show.html.twig', [
            'carrito' => $carrito,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_carrito_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Carrito $carrito, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarritoType::class, $carrito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_carrito_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carrito/edit.html.twig', [
            'carrito' => $carrito,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carrito_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Carrito $carrito, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carrito->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($carrito);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_carrito_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/demo', name: 'app_carrito_demo', methods: ['GET'])]
    public function demo(): Response
    {
        // Pasamos un carrito vacío (no persistido) para evitar el error cuando se renderiza la plantilla de demo
        $carrito = new Carrito();

        return $this->render('carrito/show.html.twig', [
            'carrito' => $carrito,
        ]);
    }

    #[Route('/add', name: 'app_carrito_add', methods: ['POST'])]
    public function add(Request $request, ViniloRepository $viniloRepository, EntityManagerInterface $em): Response
    {
        // CSRF
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add-to-cart', $token)) {
            $this->addFlash('error', 'Token CSRF inválido.');
            return $this->redirectToRoute('app_vinilo_index');
        }

        $viniloId = $request->request->get('vinilo_id');
        $vinilo = $viniloRepository->find($viniloId);
        if (!$vinilo) {
            $this->addFlash('error', 'Vinilo no encontrado.');
            return $this->redirectToRoute('app_vinilo_index');
        }

        $session = $request->getSession();
        $user = $this->getUser();
        $carrito = null;

        if ($user) {
            // Resolver la entidad Cliente desde el user (si es necesario)
            if ($user instanceof \App\Entity\Cliente) {
                $cliente = $user;
            } else {
                // intentar resolver por identificador (email)
                $identifier = method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : (method_exists($user, 'getUsername') ? $user->getUsername() : null);
                $cliente = null;
                if ($identifier) {
                    $usuarioRepo = $em->getRepository(\App\Entity\Usuario::class);
                    $usuarioEntity = $usuarioRepo->findOneBy(['email' => $identifier]);
                    if ($usuarioEntity instanceof \App\Entity\Cliente) {
                        $cliente = $usuarioEntity;
                    }
                }
            }

            if ($cliente) {
                $carrito = $cliente->getCarrito();
                if (!$carrito) {
                    $carrito = new Carrito();
                    // setCliente() maneja la relación inversa
                    $carrito->setCliente($cliente);
                    $em->persist($carrito);
                }
            }
        }

        // Si no hay usuario/cliente, usar carrito en sesión (anónimo)
        if (!$carrito) {
            $cartId = $session->get('cart_id');
            if ($cartId) {
                $carrito = $em->getRepository(Carrito::class)->find($cartId);
            }

            if (!$carrito) {
                $carrito = new Carrito();
                $em->persist($carrito);
            }
        }

        // Verificar si ya existe un DetalleCarrito para ese vinilo y carrito
        $existe = null;
        foreach ($carrito->getDetalleCarritos() as $detalle) {
            if ($detalle->getVinilo() && $detalle->getVinilo()->getId() === $vinilo->getId()) {
                $existe = $detalle;
                break;
            }
        }

        if ($existe) {
            $cantidadActual = $existe->getCantidad() ?? 0;
            $existe->setCantidad($cantidadActual + 1);
        } else {
            $detalle = new DetalleCarrito();
            $detalle->setCantidad(1);
            $detalle->setPrecio($vinilo->getPrecio());
            $detalle->setVinilo($vinilo);
            $detalle->setCarrito($carrito);
            $em->persist($detalle);
            $carrito->addDetalleCarrito($detalle);
        }

        $em->flush();

        // Guardar el id del carrito en sesión para usuarios anónimos
        if (!$user) {
            $session->set('cart_id', $carrito->getId());
        }

        $this->addFlash('success', 'Vinilo añadido al carrito.');

        // Redirigir a la página anterior o al índice de vinilos
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_vinilo_index');
    }
}
