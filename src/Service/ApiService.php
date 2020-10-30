<?php

namespace App\Service;

use App\API\ApiInterface;
use App\API\OMDbApi;
use App\API\TheMovieDbApi;
use App\API\TheTvDbApi;
use App\API\TvMazeApi;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Unirest\Exception;
use Unirest\Request;
use Unirest\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ApiService
{
    const OMDB_API = 'OMDbApi';
    const TMDB_API = 'TheMovieDbApi';
    const TTVDB_API = 'TheTvDbApi';
    const TVM_API = 'TvMazeApi';

    const API_NAMESPACE = 'App\API';
    const API_PARAM_KEY = 'api';

    const URL_REQUEST_PARAM_KEY = 'url';
    const QUERY_REQUEST_PARAM_KEY = 'query';
    const HEADERS_REQUEST_PARAM_KEY = 'headers';

    /**
     * @var string
     */
    public $theTvDbToken;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param $token
     */
    public function __construct($token)
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->theTvDbToken = $token;
    }

    /**
     * @param array $params
     * @return ApiInterface
     *
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function findSerialInfo(array $params): ApiInterface
    {
        if($params[self::API_PARAM_KEY] === self::TTVDB_API){
            return $this->TTVDbApi($params);
        } elseif ($params[self::API_PARAM_KEY] === self::TVM_API){
            return $this->TVMazeApi($params);
        } elseif ($params[self::API_PARAM_KEY] === self::OMDB_API) {
            return $this->OMDbApi($params);
        } else {
            throw new Exception('Bad API params!');
        }
    }

    /**
     * @param array $params
     * @return ApiInterface
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function findMovieInfo(array $params): ApiInterface
    {
        if($params[self::API_PARAM_KEY] === self::TMDB_API){
            return $this->TMDbApi($params);
        }  elseif ($params[self::API_PARAM_KEY] === self::OMDB_API) {
            return $this->OMDbApi($params);
        } else {
            throw new Exception('Bad API params!');
        }
    }

    /**
     * @param array $params
     * @return ApiInterface
     *
     * @throws ExceptionInterface
     */
    private function TTVDbApi(array $params): ApiInterface
    {
        $TTVDbApi = $this->createApiEntity(self::API_NAMESPACE, self::TTVDB_API);
        $requestParams[self::URL_REQUEST_PARAM_KEY] = $params;
        $requestParams[self::HEADERS_REQUEST_PARAM_KEY] = ['Authorization' => 'Bearer ' . $this->theTvDbToken];
        $apiResponse = $this->makeApiCall($TTVDbApi, $requestParams);
        $TTVDbApi = $this->serializer->denormalize($apiResponse->body->data, TheTvDbApi::class);

        $requestParams[self::URL_REQUEST_PARAM_KEY]['secondCall'] = true;
        $episodes = [];
        $resultEpisodes = [];
        $page = 1;
        do{
            $apiResponse = $this->makeApiCall($TTVDbApi, $requestParams);
            $responseEpisodes = $apiResponse->body->data;
            $requestParams[self::QUERY_REQUEST_PARAM_KEY] = ['page' => ++$page];
            $resultEpisodes = array_merge($resultEpisodes, $responseEpisodes);
        } while ($page <= $apiResponse->body->links->last);

        foreach ($resultEpisodes as $episode) {
            $episodes[]  = $this->serializer->denormalize($episode, TheTvDbApi::class);
        }
        $TTVDbApi->episodes = $episodes;

        return $TTVDbApi;
    }

    /**
     * @param array $params
     * @return ApiInterface
     *
     * @throws Exception
     * @throws ExceptionInterface
     */
    private function OMDbApi(array $params): ApiInterface
    {
        $OMDbApi = $this->createApiEntity(self::API_NAMESPACE, self::OMDB_API);
        if ($params['type'] === 'series') {
            $requestParams[self::QUERY_REQUEST_PARAM_KEY] = $params;
            $apiResponse = $this->makeApiCall($OMDbApi, $requestParams);
            if ($apiResponse->code === 200 && $apiResponse->body->Response !== 'False' && $apiResponse->body->Type === 'series') {
                $OMDbApi = $this->serializer->deserialize($apiResponse->raw_body, OMDbApi::class, 'json');
                $totalSeasons = intval($apiResponse->body->totalSeasons);
                $requestParams[self::QUERY_REQUEST_PARAM_KEY][OMDbApi::IMDB_ID_SEARCH_KEY] = $OMDbApi->getIMDbID();
                $totalEpisodes = [];
                for ($season = 1; $season <= $totalSeasons; $season++) {
                    $requestParams[self::QUERY_REQUEST_PARAM_KEY][OMDbApi::SEASON_SEARCH_KEY] = $season;
                    $OMDbApiSeason = $this->createApiEntity(self::API_NAMESPACE, self::OMDB_API);
                    $apiResponse = $this->makeApiCall($OMDbApiSeason, $requestParams);
                    $OMDbApiSeason = $this->serializer->deserialize($apiResponse->raw_body, OMDbApi::class, 'json');
                    $episodes = $OMDbApiSeason->getEpisodes();
                    $OMDbApiEpisodes = [];
                    foreach ($episodes as $episode) {
                        $episode = $this->serializer->denormalize($episode, OMDbApi::class);
                        $episode->Season = $OMDbApiSeason->getSeason();
                        $OMDbApiEpisodes[] = $episode;
                    }
                    $totalEpisodes = array_merge($totalEpisodes, $OMDbApiEpisodes);
                }
                $OMDbApi->Episodes = $totalEpisodes;
            }
        } elseif ($params['type'] === 'movie') {
            $requestParams [self::QUERY_REQUEST_PARAM_KEY] = $params;
            $apiResponse = $this->makeApiCall($OMDbApi, $requestParams);
            if ($apiResponse->code === 200 && $apiResponse->body->Response !== 'False' && $apiResponse->body->Type === 'movie') {
                $OMDbApi = $this->serializer->deserialize($apiResponse->raw_body, OMDbApi::class, 'json');
            }
        } else {
            throw new Exception('Bad response OMDB Api!');
        }

        return $OMDbApi;
    }

    /**
     * @param array $params
     * @return ApiInterface
     *
     * @throws ExceptionInterface
     */
    private function TMDbApi(array $params): ApiInterface
    {
        $TMDbApi = $this->createApiEntity(self::API_NAMESPACE, $params['api']);
        $requestParams[self::URL_REQUEST_PARAM_KEY] = $params;
        $apiResponse = $this->makeApiCall($TMDbApi, $requestParams);
        $TMDbApi = $this->serializer->deserialize($apiResponse->raw_body, TheMovieDbApi::class, 'json');

        return $TMDbApi;
    }

    /**]
     * @param array $params
     * @return ApiInterface
     *
     * @throws Exception
     * @throws ExceptionInterface
     */
    private function TVMazeApi(array $params): ApiInterface
    {
        $apiEntity = $this->createApiEntity(self::API_NAMESPACE, $params[self::API_PARAM_KEY]);
        $requestParams[self::QUERY_REQUEST_PARAM_KEY] = $params;
        $apiResponse = $this->makeApiCall($apiEntity,$requestParams);
        if($apiResponse->code === 200){
            $apiEntity = $this->serializer->deserialize($apiResponse->raw_body, TvMazeApi::class, 'json');
            $episodes = $apiEntity->getEpisodes()['episodes'];
            $tvMazeEpisodes = [];
            foreach ($episodes as $episode) {
                $tvMazeEpisodes[]  = $this->serializer->denormalize($episode, TvMazeApi::class);
            }
            $apiEntity->_embedded = $tvMazeEpisodes;
        } else {
            throw new Exception('Bad response TvMaze Api!');
        }

        return $apiEntity;
    }

    /**
     * @param $nameSpace
     * @param $className
     *
     * @return ApiInterface
     */
    private function createApiEntity(string $nameSpace, string $className): ApiInterface
    {
        $apiEntity = $nameSpace . "\\" . $className;

        return new $apiEntity();
    }

    /**
     * @param ApiInterface $apiEntity
     * @param array $requestParams
     *
     * @return Response
     */
    private function makeApiCall(ApiInterface $apiEntity, array $requestParams): Response
    {
        $url = $apiEntity->getUrl($requestParams[self::URL_REQUEST_PARAM_KEY] ?? []);
        $headers = $apiEntity->getHeaders($requestParams[self::HEADERS_REQUEST_PARAM_KEY] ?? []);
        $query = $apiEntity->getQuery($requestParams[self::QUERY_REQUEST_PARAM_KEY] ?? []);

        return Request::get($url, $headers, $query);
    }
}
