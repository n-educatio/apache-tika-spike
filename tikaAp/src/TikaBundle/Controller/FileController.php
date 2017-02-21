<?php

namespace TikaBundle\Controller;

use TikaBundle\Entity\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;

/**
 * File controller.
 * //@codingStandardsIgnoreStart
 * @Route("file")
 */
class FileController extends Controller
{
    /**
     * Lists all file entities.
     * @Route("/", name="file_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $files = $em->getRepository('TikaBundle:UploadedFile')->findAll();

        return $this->render('file/index.html.twig', array(
            'files' => $files,
        ));
    }

    /**
     * Creates a new file entity.
     *
     * @Route("/new", name="file_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $newFile = new UploadedFile();
        $form = $this->createForm('TikaBundle\Form\UploadedFileType', $newFile);
        $form->handleRequest($request);

        $newFile->setMetadata('1');

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $fileName = $form->getData()->getFileName()->getClientOriginalName();
            $file = $newFile->getFileName();

            $path = $this->get('app.file_uploader')->upload($file);

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
     */
    public function showAction(UploadedFile $file)
    {
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
     */
    public function deleteAction(Request $request, UploadedFile $file)
    {
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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(UploadedFile $file)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('file_delete', array('id' => $file->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    //// @codingStandardsIgnoreEnd
}
