<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Url;

class UrlRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param \DateTime $date
     * @return Url[]
     */
    public function deleteAllBeforeDateTime(\DateTime $date)
    {
        $queryBuilder = $this->createQueryBuilder('url');
        $queryBuilder
            ->delete()
            ->where($queryBuilder->expr()->lte('url.createdAt', ':date'))
            ->setParameters([
                'date' => $date,
            ])
        ;

        return $queryBuilder->getQuery()->execute();
    }
}
