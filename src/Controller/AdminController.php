<?php

namespace App\Controller;

use App\Entity\Artista;
use App\Entity\Genero;
use App\Entity\Pedido;
use App\Entity\Vinilo;
use App\Repository\ArtistaRepository;
use App\Repository\GeneroRepository;
use App\Repository\PedidoRepository;
use App\Repository\ViniloRepository;
use App\Service\DiscogsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/panel', name: 'app_admin_panel')]
    public function panel(ViniloRepository $viniloRepository, GeneroRepository $generoRepository, PedidoRepository $pedidoRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $busqueda = $request->query->get('busqueda', '');
        $generoFiltro = $request->query->get('genero', '');

        // Filtros para pedidos
        $filtroPedidoUsuario = $request->query->get('pedido_usuario', '');
        $filtroPedidoVinilo = $request->query->get('pedido_vinilo', '');

        if ($busqueda || $generoFiltro) {
            $vinilos = $viniloRepository->findByFilters($busqueda, $generoFiltro, '', null, '');
        } else {
            $vinilos = $viniloRepository->findBy(['deletedAt' => null]);
        }

        $novedades = $viniloRepository->findBy(['esNovedad' => true, 'deletedAt' => null], ['fecha_lanzamiento' => 'DESC']);
        $generos = $generoRepository->findBy([], ['nombre' => 'ASC']);

        // Lógica de filtrado de pedidos
        $qbPedidos = $pedidoRepository->createQueryBuilder('p')
            ->leftJoin('p.cliente', 'c')
            ->leftJoin('p.detalles', 'd')
            ->leftJoin('d.vinilo', 'v')
            ->where('p.deletedAt IS NULL');

        if ($filtroPedidoUsuario) {
            $qbPedidos->andWhere('LOWER(c.nombre) LIKE LOWER(:usuario) OR LOWER(c.email) LIKE LOWER(:usuario)')
                ->setParameter('usuario', '%' . $filtroPedidoUsuario . '%');
        }

        if ($filtroPedidoVinilo) {
            $qbPedidos->andWhere('LOWER(v.titulo) LIKE LOWER(:vinilo)')
                ->setParameter('vinilo', '%' . $filtroPedidoVinilo . '%');
        }

        $pedidos = $qbPedidos->orderBy('p.fecha', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/panel.html.twig', [
            'vinilos' => $vinilos,
            'novedades' => $novedades,
            'generos' => $generos,
            'pedidos' => $pedidos,
            'busqueda' => $busqueda,
            'generoFiltro' => $generoFiltro,
            'pedidoUsuarioFiltro' => $filtroPedidoUsuario,
            'pedidoViniloFiltro' => $filtroPedidoVinilo,
        ]);
    }

    #[Route('/admin/pedido/{id}/delete', name: 'app_admin_pedido_delete', methods: ['POST'])]
    public function deletePedido(int $id, PedidoRepository $pedidoRepository, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('delete-pedido' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF inválido');
            return $this->redirectToRoute('app_admin_panel');
        }

        $pedido = $pedidoRepository->find($id);
        if (!$pedido) {
            throw $this->createNotFoundException('Pedido no encontrado');
        }

        $pedido->setDeletedAt(new \DateTimeImmutable());
        $em->flush();

        $this->addFlash('success', 'Pedido eliminado (desactivado) correctamente');
        return $this->redirectToRoute('app_admin_panel');
    }

    #[Route('/admin/pedido/{id}/edit', name: 'app_admin_pedido_edit', methods: ['GET', 'POST'])]
    public function editPedido(int $id, Request $request, PedidoRepository $pedidoRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pedido = $pedidoRepository->find($id);
        if (!$pedido) {
            throw $this->createNotFoundException('Pedido no encontrado');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('edit-pedido' . $id, $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF inválido');
                return $this->redirectToRoute('app_admin_panel');
            }

            $total = $request->request->get('total');
            if ($total !== null) {
                $pedido->setTotal((float)$total);
            }

            $em->flush();
            $this->addFlash('success', 'Pedido actualizado correctamente');
            return $this->redirectToRoute('app_admin_panel');
        }

        return $this->render('admin/edit_pedido.html.twig', [
            'pedido' => $pedido,
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

    #[Route('/admin/discogs/search', name: 'app_admin_discogs_search', methods: ['GET'])]
    public function searchDiscogs(Request $request, DiscogsService $discogsService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $query = $request->query->get('q', '');
        $results = [];
        $error = null;

        if ($query) {
            try {
                $data = $discogsService->search($query, 20);
                $results = $data['results'] ?? [];

                // Si no hay resultados, devolver info útil
                if (empty($results)) {
                    $error = 'No se encontraron resultados. Puede que necesites configurar DISCOGS_TOKEN en .env';
                }
            } catch (\Exception $e) {
                $error = 'Error al buscar en Discogs: ' . $e->getMessage();
            }
        }

        return $this->json([
            'results' => $results,
            'error' => $error,
            'query' => $query
        ]);
    }

    #[Route('/admin/vinilo/{id}/delete', name: 'app_admin_vinilo_delete', methods: ['POST'])]
    public function deleteVinilo(int $id, ViniloRepository $viniloRepository, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('delete-vinilo' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF inválido');
            return $this->redirectToRoute('app_admin_panel');
        }

        $vinilo = $viniloRepository->find($id);
        if (!$vinilo) {
            throw $this->createNotFoundException('Vinilo no encontrado');
        }

        $vinilo->setDeletedAt(new \DateTimeImmutable());
        $em->flush();

        $this->addFlash('success', 'Vinilo eliminado (desactivado) correctamente');
        return $this->redirectToRoute('app_admin_panel');
    }

    #[Route('/admin/vinilos/bulk-update', name: 'app_admin_bulk_update', methods: ['POST'])]
    public function bulkUpdate(Request $request, ViniloRepository $viniloRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('bulk-update', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF inválido');
            return $this->redirectToRoute('app_admin_panel');
        }

        $ids = $request->request->all('ids');
        $action = $request->request->get('action');
        $precio = $request->request->get('precio');
        $stock = $request->request->get('stock');

        if (empty($ids)) {
            $this->addFlash('error', 'No se seleccionaron vinilos');
            return $this->redirectToRoute('app_admin_panel');
        }

        $count = 0;
        foreach ($ids as $id) {
            $vinilo = $viniloRepository->find($id);
            if (!$vinilo) continue;

            if ($action === 'delete') {
                $vinilo->setDeletedAt(new \DateTimeImmutable());
                $count++;
            } elseif ($action === 'update') {
                if ($precio !== null && $precio !== '') {
                    $vinilo->setPrecio((float) $precio);
                }
                if ($stock !== null && $stock !== '') {
                    $vinilo->setStock((int) $stock);
                }
                $count++;
            }
        }

        $em->flush();

        $message = $action === 'delete'
            ? "$count vinilos eliminados"
            : "$count vinilos actualizados";

        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_admin_panel');
    }

    #[Route('/admin/vinilo/add-from-discogs', name: 'app_admin_add_from_discogs', methods: ['POST'])]
    public function addFromDiscogs(
        Request $request,
        DiscogsService $discogsService,
        EntityManagerInterface $em,
        ArtistaRepository $artistaRepository,
        GeneroRepository $generoRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $discogsId = $request->request->get('discogs_id');
        $precio = (float) $request->request->get('precio');
        $stock = (int) $request->request->get('stock');

        if (!$discogsId || $precio <= 0 || $stock < 0) {
            $this->addFlash('error', 'Datos inválidos');
            return $this->redirectToRoute('app_admin_panel');
        }

        try {
            $releaseData = $discogsService->fetchRelease((int) $discogsId);

            if (!$releaseData) {
                $this->addFlash('error', 'No se pudo obtener información del álbum');
                return $this->redirectToRoute('app_admin_panel');
            }

            $vinilo = new Vinilo();
            $vinilo->setTitulo($releaseData['title'] ?? 'Desconocido');
            $vinilo->setPrecio($precio);
            $vinilo->setStock($stock);

            if (!empty($releaseData['year'])) {
                $vinilo->setFechaLanzamiento(new \DateTime($releaseData['year'] . '-01-01'));
            }

            $imageUrl = $discogsService->getImageUrl($releaseData);
            if ($imageUrl) {
                $vinilo->setImagen($imageUrl);
            }

            if (!empty($releaseData['artists'])) {
                foreach ($releaseData['artists'] as $artistData) {
                    $artistaNombre = $artistData['name'] ?? 'Desconocido';
                    $artista = $artistaRepository->findOneBy(['nombre' => $artistaNombre]);
                    if (!$artista) {
                        $artista = new Artista();
                        $artista->setNombre($artistaNombre);
                        $artista->setNacionalidad('Desconocida');
                        $em->persist($artista);
                    }
                    $vinilo->addArtista($artista);
                }
            }

            if (!empty($releaseData['genres'])) {
                $generoNombre = $releaseData['genres'][0];
                $genero = $generoRepository->findOneBy(['nombre' => $generoNombre]);
                if (!$genero) {
                    $genero = new Genero();
                    $genero->setNombre($generoNombre);
                    $em->persist($genero);
                }
                $genero->addGeneroVinilo($vinilo);
            }

            $em->persist($vinilo);
            $em->flush();

            $this->addFlash('success', 'Vinilo añadido correctamente');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al añadir vinilo: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_admin_panel');
    }
}
