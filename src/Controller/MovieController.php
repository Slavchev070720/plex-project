<?php

namespace App\Controller;

use App\Entity\Main\Movie;
use App\Form\EditMovieType;
use App\Service\MovieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/movies", name="movies")
     * @param MovieService $movieService
     * @param Request $request
     *
     * @return Response
     */
    public function moviesView(MovieService $movieService, Request $request): Response
    {
        $title = $request->query->get('q');
        if(strlen($title) > 2){
            $movies = $movieService->searchMovies($request, $title);
        } else {
            $movies = $movieService->getAllMoviesPaginator($request);
        }

        return $this->render('movies.html.twig', ['movies' => $movies]);
    }

    /**
     * @Route("/movies/{id}", name="movie", methods={"GET"})
     * @param Movie $movie
     *
     * @return Response
     */
    public function movieView(Movie $movie): Response
    {
        $form = $this->createForm(EditMovieType::class, $movie);

        return $this->render('movie.html.twig', ['movie' => $movie, 'form' => $form->createView()]);
    }

    /**
     * @Route("/movies/{id}", name="movieEdit", methods={"POST"})
     * @param Movie $movie
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function movieEdit(Movie $movie, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditMovieType::class, $movie);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movie->setImdbId($form->getData()->getImdbId());
            $movie->setErrorAudio($form->getData()->getErrorAudio());
            $entityManager->flush();
        }

        return $this->render('movie.html.twig', ['movie' => $movie, 'form' => $form->createView()]);
    }
}
