<?php

namespace ContinuezLHistoire\SiteBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * HistoireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HistoireRepository extends EntityRepository
{
    public function findByCommentaireUtilisateur($histoireId, $userId)
    {
        // On passe par le QueryBuilder vide de l'EntityManager pour l'exemple
        $qb = $this->_em->createQueryBuilder();
        
        $qb->select('a')
           ->from('ContinuezLHistoireSiteBundle:Note', 'n')
           ->join('ContinuezLHistoireSiteBundle:AvisUtilisateur', 'a')
           ->where('n.histoire = :histoire')
           ->andWhere('n.user = :user')
           ->setParameter('histoire', $histoireId)
           ->setParameter('user', $userId);
        
        return $qb->getQuery()
              ->getResult();
    }
    
    public function findOneByCommentaireUtilisateur($histoireId, $userId)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM ContinuezLHistoireSiteBundle:Note n inner join ContinuezLHistoireSiteBundle:AvisUtilisateur a where n.histoire = :histoire and n.user = :user'
            )
            ->setParameter('histoire', $histoireId)
            ->setParameter('user', $userId)
            ->getSingleResult();
    }
}
