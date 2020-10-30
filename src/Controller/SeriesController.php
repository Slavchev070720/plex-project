<?php

namespace App\Controller;

use App\Entity\Main\Episode;
use App\Entity\Main\Series;
use App\Service\SerieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class SeriesController extends AbstractController
{
    /**
     * @Route("/series", name="series")
     * @param Request $request
     * @param SerieService $serieService
     *
     * @return Response
     */
    public function seriesView(Request $request, SerieService $serieService): Response
    {
        $title = $request->query->get('q');
        if(strlen($title) > 2){
            $series = $serieService->searchSeries($request, $title);
        } else {
            $series = $serieService->getAllSeriesPaginator($request);
        }

        return $this->render('series.html.twig', ['series' => $series]);
    }

    /**
     * @Route("/series/{id}", name="serie")
     * @param Series $serie
     * @param SerieService $serieService
     *
     * @return Response
     */
    public function serieView(Series $serie, SerieService $serieService): Response
    {
        $seasons = $serieService->getSeasonsStatus($serie);

        return $this->render('serie.html.twig', ['serie' => $serie, 'seasons' => $seasons]);
    }

    /**
     * @Route("/series/{id}/{season}", name="season")
     * @param Series $serie
     * @param SerieService $seriesService
     * @param int $season
     *
     * @return Response
     */
    public function seasonEpisodesListView(Series $serie, SerieService $seriesService, int $season): Response
    {
        $episodes = $seriesService->getAllepisodesPerSeason($serie, $season);

        return $this->render('seasonEpisodesList.html.twig', ['episodes' => $episodes]);
    }

    /**
     * @Route("/episode/{id}", name="episode")
     * @param Episode $episode
     *
     * @return Response
     */
    public function episodeInfoView(Episode $episode): Response
    {
        return $this->render('episodeInfo.html.twig', ['episode' => $episode]);
    }
}
