<?php

namespace App\API;

class TvMazeApi implements ApiInterface
{
    const ROOT_URL = 'http://api.tvmaze.com/singlesearch/shows';

    const SEARCH_INCLUDE_EPISODES = ['embed' => 'episodes'];
    const SEARCH_SERIAL_KEY = 'q';

    /**
     * @var array
     */
    public $externals;

    /**
     * @var string|null
     */
    public $name;

    /**
     * @var array|null
     */
    public $image;

    /**
     * @var array|TvMazeApi[]
     */
    public $_embedded = [];

    /**
     * @var int
     */
    public $season;

    /**
     * @var TvMazeApi[]
     */
    public $seasons = [];

    /**
     * @var int|null
     */
    public $number;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getSeason(): ?int
    {
        return $this->season;
    }

    /**
     * @return TvMazeApi[]
     */
    public function getSeasons(): iterable
    {
        return $this->seasons;
    }

    /**
     * @return int|null
     */
    public function getTotalSeasons()
    {
        return count($this->seasons);
    }

    /**
     * @return TvMazeApi[]
     */
    public function getEpisodes(): iterable
    {
        return $this->_embedded;
    }

    /**
     * @param TvMazeApi[]
     */
    public function setEpisodes(array $episodes): void
    {
        $this->_embedded = $episodes;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getIMDbID(): ?string
    {
        return $this->externals['imdb'];
    }

    /**
     * @return string|null
     */
    public function getPoster(): ?string
    {
        return $this->image['original'];
    }

    /**
     * @return int|null
     */
    public function getEpisode(): ?int
    {
        return $this->number;
    }

    /**
     * @param array $urlParams
     *
     * @return string
     */
    public function getUrl(array $urlParams = []): string
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
        $query = [ self::SEARCH_SERIAL_KEY => $queryParams[self::SEARCH_SERIAL_KEY]];

        return array_merge($query, self::SEARCH_INCLUDE_EPISODES);
    }
}
