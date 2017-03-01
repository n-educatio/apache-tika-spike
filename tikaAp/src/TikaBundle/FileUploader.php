<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 20.02.17
 * Time: 12:56
 */

namespace TikaBundle;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class FileUploader
{

    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $path = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->targetDir, $path);

        return $path;
    }
}