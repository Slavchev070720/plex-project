<?php

namespace App\Service;

use App\Entity\Main\Episode;
use App\Entity\Main\Series;
use App\Repository\Main\EpisodeRepository;
use App\Repository\Main\SeriesRepository;
use App\TwigHelper\TwigConstants;
use Doctrine\Common\Collections\Collection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class SerieService
{
    const SERIES_PER_PAGE = 8;

    const SEASON_STATUS_OK = 'OK';
    const SEASON_STATUS_MISSING = 'Missing';

    /**
     * @var SeriesRepository
     */
    private $seriesRepository;

    /**
     * @var EpisodeRepository
     */
    private $episodeRepository;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @param SeriesRepository $seriesRepository
     * @param EpisodeRepository $episodeRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        SeriesRepository $seriesRepository,
        EpisodeRepository $episodeRepository,
        PaginatorInterface $paginator
    ) {
        $this->seriesRepository = $seriesRepository;
        $this->episodeRepository = $episodeRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param Request $request
     *
     * @return PaginationInterface
     */
    public function getAllSeriesPaginator(Request $request): PaginationInterface
    {
        $query = $this->seriesRepository->getAllSeriesQuery();
        $series = $this->paginator->paginate($query, $request->query->getInt('page', 1), self::SERIES_PER_PAGE);

        return $series;
    }

    /**
     * @param Series $serie
     *
     * @return array
     */
    public function getSeasonsStatus(Series $serie): array
    {
        $totalSeasons = $serie->getEpisodes()->last()->getSeason();
        $seasons = [];
        for ($season = 1; $season <= $totalSeasons; $season++) {
            $episodes = $serie->getEpisodes()->filter(function (Episode $episode) use ($season) {
                return $episodes = (($episode->getSeason() == $season) && ($episode->getStatus() == TwigConstants::STATUS_ENTITY_MISSING));
            });
            if (empty($episodes->getValues())) {
                $seasons[$season] = self::SEASON_STATUS_OK;
            } else {
                $seasons[$season] = self::SEASON_STATUS_MISSING;
            }
        }

        return $seasons;
    }

    /**
     * @param Series $serie
     * @param int $season
     *
     * @return Collection Episodes
     */
    public function getAllepisodesPerSeason(Series $serie, int $season): Collection
    {
        return $episodes = $serie->getEpisodes()->filter(function (Episode $episode) use ($season) {
            return $episodes = ($episode->getSeason() == $season);
        });
    }

    /**
     * @param Request $request
     * @param string $title
     *
     * @return PaginationInterface
     */
    public function searchSeries(Request $request, string $title): PaginationInterface
    {
        $query = $this->seriesRepository->getSeriesQueryByTitle($title);
        return $this->paginator->paginate($query, $request->query->getInt('page', 1), self::SERIES_PER_PAGE);
    }
}
