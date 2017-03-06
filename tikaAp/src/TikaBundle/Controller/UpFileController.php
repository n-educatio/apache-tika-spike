<?php

namespace TikaBundle\Controller;

use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SplFileInfo;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use TikaBundle\Entity\UpFile;

/**
 * File controller.
 */
class UpFileController extends Controller
{

    /**
     * Lists all file entities.
     * @Route("/index", name="file_index")
     * @Method("GET")
     * @Template
     * @return type
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $files = $em->getRepository('TikaBundle:UpFile')->findAllOrdered();

        return array(
            'files' => $files,
        );
    }

    /**
     * Lists all file entities.
     * @Route("/{name}", name="file_index_by_name", defaults={"name"=""})
     * @Method("GET")
     * @Template("TikaBundle::base.html.twig")
     * @return type
     * @param object $name
     */
    public function indexByNameAction($name)
    {
        $em = $this->getDoctrine()->getManager();

        $files = $em->getRepository('TikaBundle:UpFile')->findByNameOrdered($name);

        return new JsonResponse($files);
    }

    /**
     * Creates a new file entity with metadata from Apache Tika.
     *
     * @Route("/new/", name="file_new")
     * @Method({"GET", "POST"})
     * @Template("TikaBundle::base.html.twig")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $newFile = new UpFile();
        $form = $this->createForm('TikaBundle\Form\UpFileType', $newFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $filesData = $form->getData()->getFileName();
            $file = $newFile->getFileName();

            foreach ($filesData as $index => $filesNames) {
                $newFile = new UpFile();
                $path = $this->get('app.file_uploader')->upload($file[$index]);
                $realPath = $this->getParameter('uploadedfiles')."/".$path;
                $newFile->setMetadata($this->get('app.meta_file_reader')->metaReader($realPath));
                $newFile->setFileName($file[$index]->getClientOriginalName());
                $newFile->setPath($path);

                $em->persist($newFile);
                $em->flush($newFile);
            }

            //return $this->redirectToRoute('file_new');
        }

        return array(
            'file' => $newFile,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a file entity.
     *
     * @Route("/index/{id}", name="file_show")
     * @Method("GET")
     * @Template
     * @param UpFile $file
     * @return JsonResponse
     */
    public function showAction(UpFile $file)
    {
        $metaData = $file->getMetadata();
        if (!$metaData) {
            return new JsonResponse(["error" => " no metadata for this file "]);
        }

        return new JsonResponse($metaData);
    }
}
