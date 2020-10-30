<?php

namespace App\API;

class OMDbApi implements ApiInterface
{
    const API_KEY = 'e1a32b18';
    const API_PARAM_KEY = 'apiKey';
    const ROOT_URL = "http://www.omdbapi.com/";

    const IMDB_ID_SEARCH_KEY = 'i';
    const TITLE_SEARCH_KEY = 't';
    const YEAR_SEARCH_KEY = 'y';
    const SEASON_SEARCH_KEY = 'season';

    /**
     * @var string|null
     */
    public $Title;

    /**
     * @var string|null
     */
    public $imdbID;

    /**
     * @var string|null
     */
    public $plot;

    /**
     * @var OMDbApi[]
     */
    public $Episodes = [];

    /**
     * @var int|null
     */
    public $Episode;

    /**
     * @var OMDbApi[]
     */
    public $Seasons = [];

    /**
     * @var int|null
     */
    public $Season;

    /**
     * @var int|null
     */
    public $totalSeasons;

    /**
     * @var string|null
     */
    public $Poster;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->plot;
    }
    /**
     * @return int|null
     */
    public function getSeason(): ?int
    {
        return $this->Season;
    }

    /**
     * @return OMDbApi[]
     */
    public function getSeasons(): iterable
    {
        return $this->Seasons;
    }

    /**
     * @return int
     */
    public function getTotalSeasons(): ?int
    {
        return $this->totalSeasons;
    }

    /**
     * @return OMDbApi[]
     */
    public function getEpisodes(): iterable
    {
        return $this->Episodes;
    }

    /**
     * @param OMDbApi[]
     */
    public function setEpisodes(array $episodes): void
    {
        $this->Episodes = $episodes;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->Title;
    }

    /**
     * @return string|null
     */
    public function getIMDbID(): ?string
    {
        return $this->imdbID;
    }

    /**
     * @return string|null
     */
    public function getPoster(): ?string
    {
        return $this->Poster;
    }

    /**
     * @return int|null
     */
    public function getEpisode(): ?int
    {
        return $this->Episode;
    }

    /**
     * @param array $queryParams
     *
     * @return string
     */
    public function getUrl(array $queryParams = []): string
    {
        return self::ROOT_URL;
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
        $query = [];
        $query[self::API_PARAM_KEY] = self::API_KEY;
        if (isset($queryParams[self::IMDB_ID_SEARCH_KEY])) {
            if(isset($queryParams[self::SEASON_SEARCH_KEY])){
                $query[self::SEASON_SEARCH_KEY] = $queryParams[self::SEASON_SEARCH_KEY];
            }
            $query[self::IMDB_ID_SEARCH_KEY] = $queryParams[self::IMDB_ID_SEARCH_KEY];

            return $query;
        }

        if (isset($queryParams[self::TITLE_SEARCH_KEY]) && isset($queryParams[self::YEAR_SEARCH_KEY])) {
            $query[self::TITLE_SEARCH_KEY] = $queryParams[self::TITLE_SEARCH_KEY];
            $query[self::YEAR_SEARCH_KEY] = $queryParams[self::YEAR_SEARCH_KEY];

            return $query;
        }

        if (isset($queryParams[self::TITLE_SEARCH_KEY])) {
            $query[self::TITLE_SEARCH_KEY] = $queryParams[self::TITLE_SEARCH_KEY];

            return $query;
        }
        dump($query);die;

        return $query;
    }
}
