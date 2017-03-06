<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 20.02.17
 * Time: 12:56
 */

namespace TikaBundle;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 * @package TikaBundle
 */
class FileUploader
{

    private $targetDir;

    /**
     * FileUploader constructor.
     * @param target_dir $targetDir
     */
    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $path = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->targetDir, $path);

        return $path;
    }
}
