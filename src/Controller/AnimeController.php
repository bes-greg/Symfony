<?php

namespace App\Controller;

use App\Entity\Anime;
use App\Form\AnimeType;
use App\Repository\AnimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/anime')]
final class AnimeController extends AbstractController
{
    #[Route(name: 'app_anime_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(AnimeRepository $animeRepository): Response
    {
        return $this->render('anime/index.html.twig', [
            'animes' => $animeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_anime_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $anime = new Anime();
        $form = $this->createForm(AnimeType::class, $anime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/animes',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si l'upload échoue
                }

                // On stocke le chemin relatif dans la base de données
                $anime->setImage('uploads/animes/'.$newFilename);
            }

            $entityManager->persist($anime);
            $entityManager->flush();

            return $this->redirectToRoute('app_anime_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('anime/new.html.twig', [
            'anime' => $anime,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_anime_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Anime $anime): Response
    {
        return $this->render('anime/show.html.twig', [
            'anime' => $anime,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_anime_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Anime $anime, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AnimeType::class, $anime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/animes',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si l'upload échoue
                }

                $anime->setImage('uploads/animes/'.$newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_anime_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('anime/edit.html.twig', [
            'anime' => $anime,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_anime_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Anime $anime, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$anime->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($anime);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_anime_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/{id}', name: 'show_json', methods: ['GET'])]
    public function showJson(Anime $anime)
    {
        // On renvoie un tableau associatif proprement formaté.
        // Évite de renvoyer directement l'objet $anime complet pour éviter les boucles de relations (circular references) Doctrine.
        return $this->json([
            'id' => $anime->getId(),
            'name' => $anime->getName(),
            'description' => $anime->getDescription(),
            'image' => $anime->getImage() ? $this->generateUrl('app_home') . $anime->getImage() : null, // adapte selon ton chemin d'image
            'author' => $anime->getAuthor(),
            'date' => $anime->getDate(),
        ]);
    }
}
