<?php

namespace TikaBundle\Controller;

use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SplFileInfo;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;
use TikaBundle\Entity\UploadedFile;

/**
 * File controller.
 * 
 * @Route("file")
 */
class FileController extends Controller {

    /**
     * Lists all file entities.
     * @Route("/", name="file_index")
     * @Method("GET")
     * @return type
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $files = $em->getRepository('TikaBundle:UploadedFile')->findAll();

        return $this->render('file/index.html.twig', array(
                    'files' => $files,
        ));
    }

    /**
     * Creates a new file entity with metadata from Apache Tika.
     *
     * @Route("/new", name="file_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return type
     */
    public function newAction(Request $request) {
        $newFile = new UploadedFile();
        $form = $this->createForm('TikaBundle\Form\UploadedFileType', $newFile);
        $form->handleRequest($request);

        $newFile->setMetadata('1');

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $fileName = $form->getData()->getFileName()->getClientOriginalName();
            $file = $newFile->getFileName();

            $path = $this->get('app.file_uploader')->upload($file);
            $realPath = $this->getParameter('uploadedfiles') . "/" . $path;
            
            $newFile->setMetadata($this->metaRead($realPath));
            $newFile->setFileName($fileName);
            $newFile->setPath($path);
            $em->persist($newFile);
            $em->flush($newFile);

            return $this->redirectToRoute('file_show', array('id' => $newFile->getId()));
        }

        return $this->render('file/new.html.twig', array(
                    'file' => $newFile,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a file entity.
     *
     * @Route("/{id}", name="file_show")
     * @Method("GET")
     * @param UploadedFile $file
     * @return type
     */
    public function showAction(UploadedFile $file) {
        $deleteForm = $this->createDeleteForm($file);

        return $this->render('file/show.html.twig', array(
                    'file' => $file,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a file entity.
     *
     * @Route("/{id}", name="file_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param UploadedFile $file
     * @return type
     */
    public function deleteAction(Request $request, UploadedFile $file) {
        $form = $this->createDeleteForm($file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($file);
            $em->flush($file);
        }

        return $this->redirectToRoute('file_index');
    }

    /**
     * Creates a form to delete a file entity.
     *
     * @param File $file The file entity
     *
     * @return Form The form
     */
    private function createDeleteForm(UploadedFile $file) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('file_delete', array('id' => $file->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * Read metadata from Apache Tika server
     * 
     * @param type $fileName
     * @return type
     * @throws RuntimeException
     */
    private function metaRead($filePath) {

        $url = "http://tika_java:9998/meta";

        $image = fopen($filePath, "rb");

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_HEADER, array("Accept: application/json"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_PUT, 1);
        curl_setopt($curl, CURLOPT_INFILE, $image);
        curl_setopt($curl, CURLOPT_INFILESIZE, filesize($filePath));

        $result = curl_exec($curl);

        curl_close($curl);
        if ($result) {
            return $result;
        }
    }
}
