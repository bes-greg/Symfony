<?php

namespace App\Controller;

use App\Entity\Anime;
use App\Entity\AnimeVu;
use App\Entity\AnimeAVoir;
use App\Repository\AnimeRepository;
use App\Repository\AnimeVuRepository;
use App\Repository\AnimeAVoirRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AnimeRepository $animeRepository): Response
    {
        $animes = $animeRepository->findAll();
        return $this->render('home/index.html.twig', [
            'animes' => $animes,
        ]);
    }

    #[Route('/vu', name: 'app_home_vu')]
    public function vu(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $animes = [];
        foreach ($user->getAnimeVus() as $animeVu) {
            $animes[] = $animeVu->getAnime();
        }

        return $this->render('home/vu.html.twig', [
            'animes' => $animes,
        ]);
    }

    #[Route('/a_voir', name: 'app_home_aVoir')]
    public function aVoir(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $animes = [];
        foreach ($user->getAnimeAVoir() as $animeAVoir) {
            $animes[] = $animeAVoir->getAnime();
        }

        return $this->render('home/avoir.html.twig', [
            'animes' => $animes,
        ]);
    }

    #[Route('/anime/{id}/add-vu', name: 'app_anime_add_vu', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addVu(
        Anime $anime,
        EntityManagerInterface $entityManager,
        AnimeVuRepository $animeVuRepository,
        AnimeAVoirRepository $animeAVoirRepository
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // 1. Vérifier s'il est déjà dans "Vu"
        $existingVu = $animeVuRepository->findOneBy(['user' => $user, 'anime' => $anime]);
        if ($existingVu) {
            // S'il y est déjà, on ne fait rien et on redirige
            return $this->redirectToRoute('app_home_vu');
        }

        // 2. S'il est dans "À voir", on le supprime de cette table
        $existingAVoir = $animeAVoirRepository->findOneBy(['user' => $user, 'anime' => $anime]);
        if ($existingAVoir) {
            $entityManager->remove($existingAVoir);
        }

        // 3. On l'ajoute dans "Vu"
        $animeVu = new AnimeVu();
        $animeVu->setUser($user);
        $animeVu->setAnime($anime);
        $entityManager->persist($animeVu);

        // 4. On sauvegarde tout en BDD
        $entityManager->flush();

        return $this->redirectToRoute('app_home_vu');
    }

    #[Route('/anime/{id}/add-avoir', name: 'app_anime_add_avoir', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addAVoir(
        Anime $anime,
        EntityManagerInterface $entityManager,
        AnimeVuRepository $animeVuRepository,
        AnimeAVoirRepository $animeAVoirRepository
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // 1. Vérifier s'il est déjà dans "À voir"
        $existingAVoir = $animeAVoirRepository->findOneBy(['user' => $user, 'anime' => $anime]);
        if ($existingAVoir) {
            return $this->redirectToRoute('app_home_aVoir');
        }

        // 2. S'il est dans "Vu", on le supprime de cette table
        $existingVu = $animeVuRepository->findOneBy(['user' => $user, 'anime' => $anime]);
        if ($existingVu) {
            $entityManager->remove($existingVu);
        }

        // 3. On l'ajoute dans "À voir"
        $animeAVoir = new AnimeAVoir();
        $animeAVoir->setUser($user);
        $animeAVoir->setAnime($anime);
        $entityManager->persist($animeAVoir);

        // 4. On sauvegarde tout en BDD
        $entityManager->flush();

        return $this->redirectToRoute('app_home_aVoir');
    }
}
