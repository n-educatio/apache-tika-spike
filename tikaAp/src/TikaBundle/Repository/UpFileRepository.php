<?php

namespace TikaBundle\Repository;

/**
 * FileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UpFileRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllOrdered()
    {

        $query = $this->createQueryBuilder("file")
            ->orderBy("file.fileName")
            ->getQuery()
            ->getResult();

        return $query;
    }

    public function findByNameOrdered($names)
    {

        $query = $this->createQueryBuilder("file")
            ->where("file.fileName LIKE :name")
            ->orderBy("file.fileName")
            ->setParameter("name", "%". $names ."%")
            ->getQuery()
            ->getArrayResult();

        return $query;
    }
}
