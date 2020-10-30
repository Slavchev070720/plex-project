<?php

namespace App\API;

interface ApiInterface
{
    /**
     * @return int|null
     */
    public function getSeason(): ?int;

    /**
     * @return iterable
     */
    public function getSeasons(): iterable;

    /**
     * @return int|null
     */
    public function getEpisode(): ?int;

    /**
     * @return ApiInterface[]
     */
    public function getEpisodes(): iterable;

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @return string|null
     */
    public function getIMDbID(): ?string;

    /**
     * @return string|null
     */
    public function getPoster(): ?string;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param array $urlParams
     *
     * @return string
     */
    public function getUrl(array $urlParams = []): string;

    /**
     * @param array $headersParams
     *
     * @return array
     */
    public function getHeaders(array $headersParams = []): array;

    /**
     * @param array $queryParams
     *
     * @return array
     */
    public function getQuery(array $queryParams = []): array;
}
