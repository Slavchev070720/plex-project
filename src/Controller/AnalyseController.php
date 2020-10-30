<?php

namespace App\Controller;

use App\Entity\Form\UploadForm;
use App\Form\UploadFormType;
use Cocur\BackgroundProcess\BackgroundProcess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AnalyseController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     *
     * @return Response
     */
    public function uploadSQLiteFile(Request $request): Response
    {
        $uploadFile = new UploadForm();
        $form = $this->createForm(UploadFormType::class, $uploadFile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $uploadFile->getSqlite();
            if (!is_null($file)) {
                $filename = 'plex.db';
                $path = $this->getParameter('kernel.project_dir') . "/public/SQLiteUpload";
                $file->move($path, $filename);
            }
        }

        return $this->render('upload.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/analyse", name="analyse", methods={"GET"})
     *
     * @return Response
     */
    public function analyse(): Response
    {
        $process = new BackgroundProcess( $this->getParameter('kernel.project_dir') . '/bin/console app:analyser');
        $process->run($this->getParameter('kernel.project_dir') . '/var/log/analyse-command-log.log');

        return $this->redirect($this->generateUrl('home'));
    }
}
