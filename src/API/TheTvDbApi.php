<?php

namespace App\API;

use App\Service\AnalyseService;

class TheTvDbApi implements ApiInterface
{
    const ROOT_URL = 'https://api.thetvdb.com/series/';
    const URI_SEARCH_EPISODES = '/episodes';
    const URI_SEARCH_EPISODES_KEY = 'episodes';
    const URI_SEARCH_TTV_ID_KEY = 'ttvID';
    const URL_ROOT_IMAGE = 'http://thetvdb.com/banners/';

    /**
     * @var string|null
     */
    public $episodeName;

    /**
     * @var string|null
     */
    public $seriesName;

    /**
     * @var string|null
     */
    public $filename;

    /**
     * @var string|null
     */
    public $banner;

    /**
     * @var string|null
     */
    public $imdbId;

    /**
     * @var TheTvDbApi[]
     */
    public $episodes = [];

    /**
     * @var TheTvDbApi[]
     */
    public $seasons = [];

    /**
     * @var int|null
     */
    public $airedSeason;

    /**
     * @var int|null
     */
    public $airedEpisodeNumber;

    /**
     * @var string|null
     */
    public $overview;

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
    public function getSeason(): ?int
    {
        return $this->airedSeason;
    }

    /**
     * @return TheTvDbApi[]
     */
    public function getSeasons(): iterable
    {
        return $this->seasons;
    }

    /**
     * @return TheTvDbApi[]
     */
    public function getEpisodes(): iterable
    {
        return $this->episodes;
    }

    /**
     * @param array $episodes
     */
    public function setEpisodes(array $episodes): void
    {
        $this->episodes = $episodes;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        if($this->seriesName != null){
            return $this->seriesName;
        }

        return $this->episodeName;
    }

    /**
     * @return string|null
     */
    public function getIMDbID(): ?string
    {
        return $this->imdbId;
    }

    /**
     * @return string|null
     */
    public function getPoster(): ?string
    {
        if($this->banner != null){
            return self::URL_ROOT_IMAGE . $this->banner;
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getEpisode(): ?int
    {
        return $this->airedEpisodeNumber;
    }

    /**
     * @param array $urlParams
     *
     * @return string
     */
    public function getUrl(array $urlParams = []): string
    {
        $url = self::ROOT_URL;
        if (isset($urlParams[AnalyseService::TTVDB_ID_PARAM_KEY])) {
            $url .= $urlParams[AnalyseService::TTVDB_ID_PARAM_KEY];
        }
        if (isset($urlParams[self::URI_SEARCH_EPISODES_KEY]) && isset($urlParams['secondCall'])) {
            $url .= $urlParams[self::URI_SEARCH_EPISODES_KEY];
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
        return array_merge(['Accept' => 'application/json'], $headersParams);
    }

    /**
     * @param array $queryParams
     *
     * @return array
     */
    public function getQuery(array $queryParams = []): array
    {
        return $queryParams;
    }
}
