<?php

namespace App\Service;

use App\API\ApiInterface;
use App\API\OMDbApi;
use App\API\TheMovieDbApi;
use App\API\TheTvDbApi;
use App\API\TvMazeApi;
use App\Entity\Main\Episode;
use App\Entity\Main\Movie;
use App\Entity\Main\Series;
use App\Entity\SQLite\MetadataItems;
use App\Repository\Main\EpisodeRepository;
use App\Repository\Main\MovieRepository;
use App\Repository\Main\SeriesRepository;
use App\Repository\SQLite\MetadataItemsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class AnalyseService
{
    const AUDIO_STREAM_TYPE = 1;
    const SUBTITLE_STREAM_TYPE = 3;

    const METADATA_TYPE_MOVIE = 1;
    const METADATA_TYPE_SERIAL = 2;

    const STATUS_OK = 1;
    const STATUS_LANGUAGE_MISSING = 2;
    const STATUS_EPISODE_MISSING = 3;

    const DATA_CHUNK_LIMIT = 300;
    const DATA_CHUNK_LIMIT_SERIES = 1;

    const NATIVE_LANG = 'bul';
    const MILLISEC_TO_MIN = 60000;

    const TMDB_COOLDOWN_PERIOD = 10;
    const API_ANALYSE_TYPE = 'apiCalls';
    const MAX_API_CALLS_TMDB = 40;

    const TMDB_ID_PARAM_KEY = 'tmdbId';
    const TTVDB_ID_PARAM_KEY = 'ttvdbId';

    /**
     * @var ApiService
     */
    private $apiService;

    /**
     * @var MovieRepository
     */
    private $movieRepository;

    /**
     * @var SeriesRepository
     */
    private $seriesRepository;

    /**
     * @var EpisodeRepository
     */
    private $episodeRepository;

    /**
     * @var MetadataItemsRepository
     */
    private $metadataItemsRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $timeStart = 0;

    /**
     * @var int
     */
    private $timeEnd = 0;


    /**
     * @param ApiService $apiService
     * @param MovieRepository $movieRepository
     * @param SeriesRepository $seriesRepository
     * @param EpisodeRepository $episodeRepository
     * @param MetadataItemsRepository $metadataItemsRepository
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ApiService $apiService,
        MovieRepository $movieRepository,
        SeriesRepository $seriesRepository,
        EpisodeRepository $episodeRepository,
        MetadataItemsRepository $metadataItemsRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->apiService = $apiService;
        $this->movieRepository = $movieRepository;
        $this->seriesRepository = $seriesRepository;
        $this->episodeRepository = $episodeRepository;
        $this->metadataItemsRepository = $metadataItemsRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @return bool
     * @throws ExceptionInterface
     */
    public function analyseSQLiteDB(): bool
    {
        $this->analyseMovies();
        $this->analyseSeries();
        array_map('unlink', glob('/var/www/app/public/SQLiteUpload/*.*'));

        return true;
    }

    /**
     * @param MetadataItems $metadataItem
     * @param string $type
     *
     * @return array
     */
    private function setApiParams(MetadataItems $metadataItem, string $type): array
    {
        $apiArray = [];

        if ($type === 'movie') {
            $TMDbID = $this->extractTHMbID($metadataItem->getGuid());
            $apiArray[ApiService::TMDB_API] = [
                ApiService::API_PARAM_KEY => ApiService::TMDB_API,
                TheMovieDbApi::URL_SEARCH_PARAM_KEY => TheMovieDbApi::URL_SEARCH_MOVIE,
                self::TMDB_ID_PARAM_KEY => $TMDbID
            ];

            $year = ($metadataItem->getOriginallyAvailableAt() == null) ? null
                : $metadataItem->getOriginallyAvailableAt()->format('Y');
            $apiArray[ApiService::OMDB_API] = [
                ApiService::API_PARAM_KEY => ApiService::OMDB_API,
                OMDbApi::TITLE_SEARCH_KEY => $metadataItem->getTitle(),
                OMDbApi::YEAR_SEARCH_KEY => $year,
                'type' => $type
            ];
        } elseif ($type === 'series') {
            $TTVDBID = $this->extractTTVDbID($metadataItem->getGuid());
            if($TTVDBID != null){
                $apiArray[ApiService::TTVDB_API] = [
                    ApiService::API_PARAM_KEY => ApiService::TTVDB_API,
                    TheTvDbApi::URI_SEARCH_EPISODES_KEY => TheTvDbApi::URI_SEARCH_EPISODES,
                    self::TTVDB_ID_PARAM_KEY => $TTVDBID
                ];

            }
            $apiArray[ApiService::TVM_API] =  [
                TvMazeApi::SEARCH_SERIAL_KEY => $metadataItem->getTitle(),
                ApiService::API_PARAM_KEY => ApiService::TVM_API
            ];

            $apiArray[ApiService::OMDB_API] = [
            OMDbApi::TITLE_SEARCH_KEY => $metadataItem->getTitle(),
            ApiService::API_PARAM_KEY => ApiService::OMDB_API,
            'type' => $type];
        }

        return $apiArray;
    }

    /**
     * @param int $apiCallsTMDb
     *
     * @return int
     */
    private function apiCallsTMDVOptimization($apiCallsTMDb): int
    {
        $apiCallsTMDb++;
        if ($apiCallsTMDb === 1) {
            $this->timeStart = time();
        }
        if ($apiCallsTMDb === self::MAX_API_CALLS_TMDB) {
            $this->timeEnd = time();
            $time = $this->timeEnd - $this->timeStart;
            if (self::TMDB_COOLDOWN_PERIOD - ($time) >= 0) {
                sleep(self::TMDB_COOLDOWN_PERIOD - ($time));
            }
            $apiCallsTMDb = 0;
        }

        return $apiCallsTMDb;
    }

    /**
     * @param MetadataItems $metadataItem
     *
     * @return Movie
     */
    private function getMovie(MetadataItems $metadataItem): Movie
    {
        $imdbID = $this->extractImdbID($metadataItem->getGuid());
        if($imdbID != null){
          $movie = $this->movieRepository->findOneBy(['imdb_id' => $imdbID]);
          if(is_object($movie)){
              $this->logger->info('EXIST -- ' . $movie->getTitle());
              return $movie;
          }
        }
        $movie = $this->movieRepository->findOneBy(['title' => $metadataItem->getTitle()]);
        if(is_object($movie)){
            $this->logger->info('EXIST -- ' . $movie->getTitle());
            return $movie;
        }
        $this->logger->info('DO NOT EXIST -- ' . $metadataItem->getTitle());

        return new Movie;
    }

    /**
     * @return bool
     * @throws ExceptionInterface
     */
    private function analyseMovies(): bool
    {
        $offset = 0;

        do {
            $metadataItems = $this->metadataItemsRepository->findAllMovies(
                self::DATA_CHUNK_LIMIT,
                $offset);
            $offset += self::DATA_CHUNK_LIMIT;
            $count = count($metadataItems);
            foreach ($metadataItems as $metadataItem) {
                $movie = $this->getMovie($metadataItem);
                $movie = $this->prepareMovieEntity($movie, $metadataItem);
                if ($movie->getImdbId() == null) {
                    $apisParams = $this->setApiParams($metadataItem, 'movie');
                    $movie = $this->useMovieApiService($movie, $apisParams);
                }
                $this->entityManager->persist($movie);
                $this->logger->info('ADDED/UPDATED -- ' . $movie->getTitle());
            }
            $this->entityManager->flush();
            $this->entityManager->clear();
        } while ($count === self::DATA_CHUNK_LIMIT);

        return true;
    }

    /**
     * @param Movie $movie
     * @param array $apisParams
     *
     * @return Movie
     * @throws ExceptionInterface
     */
    private function useMovieApiService(Movie $movie, array $apisParams): Movie
    {
        $apiEntity = null;
        $apiCallsTMDb = 0;
        foreach ($apisParams as $apiParams) {
            try {
                if ($apiParams[ApiService::API_PARAM_KEY] == ApiService::TMDB_API) {
                    $apiCallsTMDb = $this->apiCallsTMDVOptimization($apiCallsTMDb);
                }

                $apiEntity = $this->apiService->findMovieInfo($apiParams);

                if ($apiEntity->getIMDbID() != null) {
                    $movie = $this->setApiMovieInfo($movie, $apiEntity);
                    break;
                }
            } catch (\Exception $exception) {
                $this->logger->info('API EXCEPTION -- ' . $exception->getMessage());
            }
        }

        return $movie;
    }

    /**
     * @param Series $series
     * @param $apisParams
     *
     * @return Series
     * @throws ExceptionInterface
     */
    private function useSeriesApiService(Series $series, array $apisParams): Series
    {
        $apiEntity = null;
        foreach ($apisParams as $apiParams) {
            try {
                $apiEntity = $this->apiService->findSerialInfo($apiParams);
                if(array_key_last($apisParams) === $apisParams){
                    if(!empty($apiEntity->getEpisodes())){
                        $episodes = $this->arraySetKeysEpisodes($apiEntity->getEpisodes());
                        $series = $this->setApiEpisodesInfo($series, $episodes);
                        return $series;
                    }
                }
                if ($apiEntity->getIMDbID() != null) {
                    $series = $this->setApiSeriesInfo($series, $apiEntity);
                    if(!empty($apiEntity->getEpisodes())){
                        $episodes = $this->arraySetKeysEpisodes($apiEntity->getEpisodes());
                        $series = $this->setApiEpisodesInfo($series, $episodes);
                        return $series;
                    }
                }
            } catch (\Exception $exception) {
                $this->logger->info('API EXCEPTION -- ' . $exception->getMessage());
            }
        }

        return $series;
    }

    /**
     * @param MetadataItems $metadataItem
     *
     * @return Series|null
     */
    private function getSeries(MetadataItems $metadataItem): ?Series
    {
        $imdbID = $this->extractImdbID($metadataItem->getGuid());
        if($imdbID != null){
            $series = $this->seriesRepository->findOneBy(['imdb_id' => $imdbID]);
            if(is_object($series)){
                $this->logger->info('EXIST  -- ' . $series->getTitle());
                return $series;
            }
        }
        $series = $this->seriesRepository->findOneBy(['title' => $metadataItem->getTitle()]);
        if(is_object($series)){
            $this->logger->info('EXIST  -- ' . $series->getTitle());
            return $series;
        }
        $this->logger->info('DO NOT EXIST  -- ' . $metadataItem->getTitle());

        return new Series();
    }

    /**
     * @return bool
     * @throws ExceptionInterface
     */
    private function analyseSeries(): bool
    {
        $offset = null;
        do {
            $metadataItems = $this->metadataItemsRepository->findAllSeries(
                self::DATA_CHUNK_LIMIT_SERIES,
                $offset);
            $offset += self::DATA_CHUNK_LIMIT_SERIES;
            $count = count($metadataItems);
            foreach ($metadataItems as $metadataItem) {
                $series = $this->getSeries($metadataItem);
                $series = $this->prepareSeriesEntity($series, $metadataItem);
                $series = $this->prepareEpisodeEntities($series, $metadataItem);
                $apisParams = $this->setApiParams($metadataItem, $type = 'series');
                $series = $this->useSeriesApiService($series, $apisParams);
                $this->entityManager->persist($series);
                $this->logger->info('ADDED/UPDATED  -- ' . $series->getTitle());
            }
            $this->entityManager->flush();
            $this->entityManager->clear();
        } while ($count === self::DATA_CHUNK_LIMIT_SERIES);

        return true;
    }

    /**
     * @param  ApiInterface[]
     *
     * @return ApiInterface[]
     */
    private function arraySetKeysEpisodes(array $episodes): array
    {
        $assocEpisodes = [];
        foreach ($episodes as $key => $episode) {
            $seasonNumber = $episode->getSeason();
            if($seasonNumber == 0){
                continue;
            }
            $episodeNumber = $episode->getEpisode();
            if($episodeNumber == 0 ){
                continue;
            }
            $newKey = $seasonNumber . '/' . $episodeNumber;
            $assocEpisodes[$newKey] = $episode;
        }

        return $assocEpisodes;
    }

    /**
     * @param string $guid
     *
     * @return string|null
     */
    private function extractTTVDbID(string $guid): ?string
    {
        $helpArray = explode('thetvdb://', $guid);
        if(!isset($helpArray[1])){
            return null;
        }

        return substr($helpArray[1], 0, strpos($helpArray[1], '?'));
    }

    /**
     * @param Movie $movie
     * @param MetadataItems $item
     *
     * @return Movie
     */
    private function prepareMovieEntity(Movie $movie, MetadataItems $item): Movie
    {
        $mediaStreams = $item->getMediaItems()->getMediaStreams();
        $imdbID = ($this->extractImdbID($item->getGuid()) !== null) ? $this->extractImdbID($item->getGuid()) : $movie->getImdbId();
        $movie->setImdbId($imdbID)
            ->setTitle($item->getTitle())
            ->setDescription($item->getSummary())
            ->setReleaseDate($item->getOriginallyAvailableAt())
            ->setSubtitleLang($this->getLanguage($mediaStreams, self::SUBTITLE_STREAM_TYPE))
            ->setAudioLang($this->getLanguage($mediaStreams, self::AUDIO_STREAM_TYPE))
            ->setWriter($item->getTagsWriter())
            ->setDirector($item->getTagsDirector())
            ->setPosterUrl($movie->getPosterUrl())
            ->setRating(round($item->getRating(), 2, PHP_ROUND_HALF_UP))
            ->setDuration($item->getDuration() / self::MILLISEC_TO_MIN)
            ->setStatus($this->checkLanguageStatus($movie->getSubtitleLang(), $movie->getErrorAudio()))
            ->setErrorSubtitle(true)
            ->setErrorAudio(true);

        return $movie;
    }

    /**
     * @param Movie $movie
     * @param ApiInterface $apiEntity
     *
     * @return Movie
     */
    private function setApiMovieInfo(Movie $movie, ApiInterface $apiEntity): Movie
    {
        $movie->setImdbId($apiEntity->getIMDbID())
            ->setPosterUrl($movie->getPosterUrl() != null ? $movie->getPosterUrl() : $apiEntity->getPoster())
            ->setDescription($movie->getDescription() != null ? $movie->getDescription() : $apiEntity->getDescription());

        return $movie;
    }


    /**
     * @param Series $series
     * @param MetadataItems $item
     *
     * @return Series
     */
    private function prepareSeriesEntity(Series $series, MetadataItems $item): Series
    {
        $series->setImdbId($this->extractImdbID($item->getGuid()))
            ->setTitle($item->getTitle())
            ->setDescription($item->getSummary())
            ->setReleaseDate($item->getOriginallyAvailableAt())
            ->setPosterUrl(null)
            ->setRating(round($item->getRating(), 2, PHP_ROUND_HALF_UP))
            ->setDuration($item->getDuration());

        return $series;
    }

    /**
     * @param Series $series
     * @param ApiInterface|null $apiEntity
     *
     * @return Series
     */
    private function setApiSeriesInfo(Series $series, ?ApiInterface $apiEntity): Series
    {
        if($apiEntity !== null) {
            $series->setImdbId($apiEntity->getIMDbID() == null ? null : $apiEntity->getIMDbID())
                ->setDescription($apiEntity->getDescription() == null ? $series->getDescription() : $apiEntity->getDescription())
                ->setPosterUrl($apiEntity->getPoster() == null ? $series->getPosterUrl() : $apiEntity->getPoster());
        }

        return $series;
    }

    /**
     * @param Series $series
     * @param MetadataItems $metadataItem
     *
     * @return Series
     */
    private function prepareEpisodeEntities(Series $series, MetadataItems $metadataItem): Series
    {
        $metadataItemSeasons = $metadataItem->getChildren();
        foreach ($metadataItemSeasons as $metadataItemSeason) {
            $seasonNumber = $metadataItemSeason->getIndex();
            $metadataItemEpisodes = $metadataItemSeason->getChildren();
            foreach ($metadataItemEpisodes as $metadataItemEpisode) {
                $episodeNumber = $metadataItemEpisode->getIndex();
                $episode = new Episode;
                foreach ($series->getEpisodes() as $theEpisode){
                    if($episode->getSeason() === $seasonNumber && $episode->getEpisode() === $episodeNumber){
                        $episode = $theEpisode;
                        break;
                    }
                }
                $episode->setImdbId($episode->getImdbId() == null ? null : $episode->getImdbId())
                    ->setSeries($series)
                    ->setTitle($metadataItemEpisode->getTitle())
                    ->setDescription($metadataItemEpisode->getSummary())
                    ->setReleaseDate($metadataItemEpisode->getOriginallyAvailableAt())
                    ->setSeason($seasonNumber)
                    ->setEpisode($episodeNumber)
                    ->setSubtitleLang($this->getLanguage($metadataItemEpisode->getMediaItems()->getMediaStreams(),
                        self::SUBTITLE_STREAM_TYPE))
                    ->setAudioLang($this->getLanguage($metadataItemEpisode->getMediaItems()->getMediaStreams(),
                        self::AUDIO_STREAM_TYPE))
                    ->setWriter($metadataItemEpisode->getTagsWriter())
                    ->setDirector($metadataItemEpisode->getTagsDirector())
                    ->setPosterUrl(null)
                    ->setRating(round($metadataItemEpisode->getRating(), 2, PHP_ROUND_HALF_UP))
                    ->setDuration($metadataItem->getDuration())
                    ->setStatus($this->checkLanguageStatus($episode->getSubtitleLang(), $episode->getErrorAudio()))
                    ->setErrorSubtitle(true)
                    ->setErrorAudio(true);
                $series->addEpisode($episode);
            }
        }

        return $series;
    }

    /**
     * @param Series $series
     * @param array $apiEpisodes
     *
     * @return Series
     */
    private function setApiEpisodesInfo(Series $series, array $apiEpisodes): Series
    {
        foreach ($series->getEpisodes() as $episode) {
            $key = $episode->getSeason() . '/' . $episode->getEpisode();
            if(array_key_exists($key, $apiEpisodes)){
                $episode->setIMDbID($apiEpisodes[$key]->getIMDbID() == null ? $episode->getImdbId() : $apiEpisodes[$key]->getIMDbID())
                    ->setPosterUrl($apiEpisodes[$key]->getPoster() == null ? $episode->getPosterUrl() : $apiEpisodes[$key]->getPoster())
                    ->setDescription($apiEpisodes[$key]->getDescription() == null ? $episode->getDescription() : $apiEpisodes[$key]->getDescription());
                unset($apiEpisodes[$key]);
            }
        }
        $missingEpisodes = $this->prepareMissingEpisodes($apiEpisodes);
        foreach ($missingEpisodes as $missingEpisode)
        {
            $series->addEpisode($missingEpisode);
        }

        return $series;
    }


    /**
     * @param ApiInterface[]
     *
     * @return ApiInterface[]
     */
    private function prepareMissingEpisodes(array $apiEpisodes): array
    {
        $missingEpisodes = [];
        foreach ($apiEpisodes as $key => $apiEpisode) {
            $missingEpisode = new Episode();
            $helpArray = explode('/', $key);
            $season = $helpArray[0];
            $episode = $helpArray[1];
            $missingEpisode->setTitle($apiEpisode->getTitle() === null ? '' : $apiEpisode->getTitle())
                ->setImdbId($apiEpisode->getIMDbID() == null ? null : $apiEpisode->getIMDbID())
                ->setDescription($apiEpisode->getDescription())
                ->setPosterUrl($apiEpisode->getPoster())
                ->setEpisode($episode)
                ->setSeason($season)
                ->setStatus(self::STATUS_EPISODE_MISSING)
                ->setErrorSubtitle(true)
                ->setErrorAudio(true);
            $missingEpisodes[] = $missingEpisode;
        }

        return $missingEpisodes;
    }

    /**
     * @param string $guid
     *
     * @return string|null
     */
    private function extractTHMbID(string $guid): ?string
    {
        $helpArray = explode('themoviedb://', $guid);

        if(!isset($helpArray[1])){
            return null;
        }

        return substr($helpArray[1], 0, strpos($helpArray[1], '?'));
    }

    /**
     * @param string $guid
     *
     * @return string|null
     */
    private function extractImdbID(string $guid): ?string
    {
        $helpArray = explode('imdb://', $guid);

        return isset($helpArray[1]) ? substr($helpArray[1], 0, 9) : null;
    }

    /**
     * @param Collection $mediaStreams
     * @param int $streamType
     *
     * @return string|null $subtitleLang
     */
    private function getLanguage(Collection $mediaStreams, int $streamType): ?string
    {
        $subtitleLang = "";
        foreach ($mediaStreams as $mediaStream) {
            if ($mediaStream->getStreamTypeId() === $streamType) {
                if ($subtitleLang === "") {
                    $subtitleLang = $mediaStream->getLanguage();
                } else {
                    $subtitleLang .= '|' . $mediaStream->getLanguage();
                }
            }
        }

        return $subtitleLang;
    }

    /**
     * @param string|null $subtitleLang
     * @param string|null $audioLang
     *
     * @return int
     */
    private function checkLanguageStatus(?string $subtitleLang, ?string $audioLang): int
    {
        if ($this->checkLanguage($subtitleLang) || $this->checkLanguage($audioLang)) {
            return self::STATUS_OK;
        }

        return self::STATUS_LANGUAGE_MISSING;
    }

    /**
     * @param string|null $language
     *
     * @return bool
     */
    private function checkLanguage(?string $language): bool
    {
        if ($language == null) {
            return false;
        }
        $helpArray = explode('|', $language);
        foreach ($helpArray as $item) {
            if ($item === self::NATIVE_LANG) {
                return true;
            }
        }

        return false;
    }
}
