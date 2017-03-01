<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 01.03.17
 * Time: 13:50
 */

namespace TikaBundle;


class MetaFileReader
{
    /**
     * @param file_path $filePath
     * @return mixed
     */
    public function metaReader($filePath)
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