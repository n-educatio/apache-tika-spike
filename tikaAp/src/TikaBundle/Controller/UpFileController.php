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
 *
 * @Route("file")
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
     * @Route("/", name="file_new")
     * @Method({"GET", "POST"})
     * @Template("TikaBundle::base.html.twig")
     * @param Request $request
     * @return type
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
                $newFile->setMetadata($this->metaRead($realPath));
                $newFile->setFileName($file[$index]->getClientOriginalName());
                $newFile->setPath($path);

                $em->persist($newFile);
                $em->flush($newFile);
            }
           // return $this->redirectToRoute('file_new');
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

    /**
     * Read metadata from Apache Tika server
     *
     * @param type $fileName
     * @return type
     * @throws RuntimeException
     */
    private function metaRead($filePath)
    {

        $url = "http://tika_java:9998/meta";

        $image = fopen($filePath, "rb");

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_PUT, 1);
        curl_setopt($curl, CURLOPT_INFILE, $image);
        curl_setopt($curl, CURLOPT_INFILESIZE, filesize($filePath));

        $result = curl_exec($curl);

        curl_close($curl);
        if ($result) {
            return ($result);
        }
    }
}
