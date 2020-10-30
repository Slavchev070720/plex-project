<?php

namespace App\Service;

use App\Entity\Main\Movie;
use App\Repository\Main\MovieRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class MovieService
{
    const MOVIES_PER_PAGE = 8;

    /**
     * @var MovieRepository
     */
    private $movieRepository;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @param MovieRepository $movieRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(MovieRepository $movieRepository, PaginatorInterface $paginator)
    {
        $this->movieRepository = $movieRepository;
        $this->paginator = $paginator;
    }
    /**
     * @param Request $request
     *
     * @return PaginationInterface
     */
    public function getAllMoviesPaginator(Request $request): PaginationInterface
    {
        $query = $this->movieRepository->getAllMoviesQuery();
        $movies = $this->paginator->paginate($query, $request->query->getInt('page', 1), self::MOVIES_PER_PAGE);

        return $movies;
    }

    /**
     * @param int $movieId
     *
     * @return Movie
     */
    public function getMovieById(int $movieId): Movie
    {
        return $this->movieRepository->find($movieId);
    }

    /**
     * @param Request $request
     * @param string $title
     *
     * @return PaginationInterface
     */
    public function searchMovies(Request $request, string $title): PaginationInterface
    {
        $query = $this->movieRepository->getSearchMovieQuery($title);

        return $this->paginator->paginate($query, $request->query->getInt('page', 1), self::MOVIES_PER_PAGE);
    }
}
