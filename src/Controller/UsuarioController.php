<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/usuario')]
final class UsuarioController extends AbstractController
{
    #[Route(name: 'app_usuario_index', methods: ['GET'])]
    public function index(UsuarioRepository $usuarioRepository): Response
    {
        // Require authentication
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $current = $this->getUser();

        // If the security user is the Usuario entity, use it directly
        if ($current instanceof Usuario) {
            $user = $current;
        } else {
            // Otherwise try to resolve the Usuario entity by the user identifier (usually email)
            $identifier = method_exists($current, 'getUserIdentifier') ? $current->getUserIdentifier() : (method_exists($current, 'getUsername') ? $current->getUsername() : null);
            if (!$identifier) {
                throw $this->createNotFoundException('No se pudo determinar el identificador del usuario autenticado.');
            }

            $user = $usuarioRepository->findOneBy(['email' => $identifier]);
            if (!$user) {
                throw $this->createNotFoundException('Usuario no encontrado.');
            }
        }

        return $this->render('usuario/index.html.twig', [
            'usuario' => $user,
        ]);
    }

    #[Route('/new', name: 'app_usuario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($usuario);
            $entityManager->flush();

            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_usuario_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(): Response
    {
        return $this->redirectToRoute('app_usuario_index');
    }

    #[Route('/{id}/edit', name: 'app_usuario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('usuario/edit.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/avatar', name: 'app_usuario_avatar', methods: ['POST'])]
    public function uploadAvatar(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        $avatarFile = $request->files->get('avatar');
        
        if ($avatarFile) {
            $newFilename = uniqid() . '.' . $avatarFile->guessExtension();
            $avatarFile->move(
                $this->getParameter('kernel.project_dir') . '/public/img/avatars',
                $newFilename
            );
            
            $usuario->setAvatar('/img/avatars/' . $newFilename);
            $entityManager->flush();
            
            $this->addFlash('success', 'Avatar actualizado correctamente');
        }
        
        return $this->redirectToRoute('app_usuario_index');
    }

    #[Route('/{id}', name: 'app_usuario_delete', methods: ['POST'])]
    public function delete(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($usuario);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
    }
}
