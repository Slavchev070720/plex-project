<?php

namespace App\API;

use App\Service\AnalyseService;

class TheMovieDbApi implements ApiInterface
{
    const API_KEY = 'e50849398d1bed86ed657f3e25a2c04f';
    const API_PARAM_KEY = 'api_key';

    const ROOT_URL = 'https://api.themoviedb.org/3';
    const IMG_ROOT_URL = 'https://image.tmdb.org/t/p/w500/';
    const URL_SEARCH_MOVIE = 'movie';
    const URL_SEARCH_PARAM_KEY = 'type';

    /**
     * @var string|null
     */
    public $imdb_id;

    /**
     * @var string|null
     */
    public $original_title;

    /**
     * @var string|null
     */
    public $overview;

    /**
     * @var string|null
     */
    public $original_name;

    /**
     * @var TheMovieDbApi[]
     */
    public $seasons = [];

    /**
     * @var int|null
     */
    public $totalSeasons;

    /**
     * @var int|null
     */
    public $number_of_seasons;

    /**
     * @var TheMovieDbApi[]
     */
    public $episodes = [];

    /**
     * @var string|null
     */
    public $poster_path;

    /**
     * @var int|null
     */
    public $season_number;

    /**
     * @var int|null
     */
    public $episode;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->overview;
    }

    /**
     * @return int|null
     */
    public function getTotalSeasons(): ?int
    {
        return $this->number_of_seasons;
    }

    /**
     * @return int|null
     */
    public function getSeason(): ?int
    {
        return $this->season_number;
    }

    /**
     * @return TheMovieDbApi[]
     */
    public function getSeasons(): iterable
    {
        return $this->seasons;
    }

    /**
     * @return TheMovieDbApi[]
     */
    public function getEpisodes(): iterable
    {
        return $this->episodes;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        if ($this->original_title !== null) {
            return $this->original_title;
        }

        return $this->original_name;
    }

    /**
     * @return string|null
     */
    public function getIMDbID(): ?string
    {
        return $this->imdb_id;
    }

    /**
     * @return string|null
     */
    public function getPoster(): ?string
    {
        if ($this->poster_path == null) {
            return null;
        }

        return self::IMG_ROOT_URL . $this->poster_path;
    }

    /**
     * @return int|null
     */
    public function getEpisode(): ?int
    {
        return $this->episode;
    }

    /**
     * @param array $urlParams
     *
     * @return string
     */
    public function getUrl(array $urlParams = []): string
    {
        $url = self::ROOT_URL;
        if (isset($urlParams[self::URL_SEARCH_PARAM_KEY])) {
            $url .= '/' . $urlParams[self::URL_SEARCH_PARAM_KEY];
        }
        if (isset($urlParams[AnalyseService::TMDB_ID_PARAM_KEY])) {
            $url .= '/' . $urlParams[AnalyseService::TMDB_ID_PARAM_KEY];
        }

        return $url;
    }

    /**
     * @param array $headersParams
     *
     * @return array
     */
    public function getHeaders(array $headersParams = []): array
    {
        return ['Accept' => 'application/json'];
    }

    /**
     * @param array $queryParams
     *
     * @return array
     */
    public function getQuery(array $queryParams = []): array
    {
        return [self::API_PARAM_KEY => self::API_KEY];
    }
}
