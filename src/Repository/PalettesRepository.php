<?php

namespace App\Repository;

use App\Entity\Palettes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Palettes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Palettes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Palettes[]    findAll()
 * @method Palettes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PalettesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Palettes::class);
    }


    // /**
    //  * @return Palettes[] Returns an array of Palettes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Palettes
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Récupère toutes les informations liées a la Palette demandée
     * @return Palettes
     */
    public function findPaletteByCodeHexWithInfosDQL($hexs)
    {
        $entityManager = $this->getEntityManager();

        $select = " SELECT p ";
        $from = " FROM App\Entity\Palettes p ";
        $join = "JOIN p.colors c ";

        $where = 'WHERE ';
        foreach ($hexs as $key => $hex) {
            if ($key !== 0) {
                $where .=  ' OR ';
            }

            $where .= 'c.hex = :hex'.($key+1);
        }

        $dqlQuery = $select . $from . $join .  $where ;
      
        $query = $entityManager->createQuery(
            $dqlQuery
        );
        // on va utiliser le DQL ( Doctrine Query Language)
        foreach ($hexs as $key => $hex) {
            $paramKey = 'hex'.($key+1);
            $query->setParameter($paramKey, '#'.$hex);
        }
        

        // returns the selected palettes Object
        return $query->getResult();
    }
    
    /**
     * Récupère toutes les informations liées a la Palette demandée
     * @return Palettes
     */
    //sort = new (from new to old ) ,filter = all, generated (where ownr id ==1), submission (where ownr id !=1),
    public function findNewByPagesByFilters($filter)
    {
        $entityManager = $this->getEntityManager();

        $dql = " SELECT p as palette, 0 as nbr_files FROM App\Entity\Palettes p WHERE p.public = 1 ";
        if ($filter === 1) {
            $and = " AND p.owner = 1 ";
            $dql .= $and;
        } elseif ($filter === 2) {
            $and = " AND p.owner != 1 ";
            $dql.=$and;
        }
        $groupBy = "GROUP BY p.id ORDER BY p.createdAt DESC  ";
        $dql.= $groupBy;

        $query = $entityManager->createQuery(
            $dql
        );
        

        // returns the selected palettes Object
        return $query->getResult();
    }

    /**
    * Récupère toutes les informations liées a la Palette demandée
    * @return Palettes
    */
    //sort = saved (from best to worst ) , filter = all, generated (where ownr id ==1), submission (where ownr id !=1),
    public function findSaveByPagesByFilters($filter)
    {
        $entityManager = $this->getEntityManager();
        $dql = "SELECT p as palette, count(p.id) as nbr_files FROM App\Entity\Palettes p INNER JOIN p.files f WHERE p.public = 1 ";

        if ($filter === 1) {
            $and = " AND p.owner = 1 ";
            $dql.= $and;
        } elseif ($filter === 2) {
            $and = " AND p.owner != 1 ";
            $dql.=$and;
        }
        $groupBy = "GROUP BY p.id ORDER BY nbr_files DESC ";
        $dql.= $groupBy;

       
        $query = $entityManager->createQuery(
            $dql
        );
        
        return $query->getResult();
    }
    
    /**
     * Récupère toutes les informations liées a la Palette demandée
     * @return Palettes
     */
    //sort = number of likes (from more to less ) , filter = all, generated (where ownr id ==1), submission (where ownr id !=1),
    public function findLikesByPagesByFilters($filter)
    {
        $entityManager = $this->getEntityManager();

        $dql = " SELECT p as palette, 0 as nbr_files FROM App\Entity\Palettes p WHERE p.public = 1 ";
        if ($filter === 1) {
            $and = " AND p.owner = 1 ";
            $dql .= $and;
        } elseif ($filter === 2) {
            $and = " AND p.owner != 1 ";
            $dql.=$and;
        }
        $groupBy = "GROUP BY p.id ORDER BY p.nbrLikes DESC ";
        $dql.= $groupBy;

        $query = $entityManager->createQuery(
            $dql
        );
        
        // returns the selected palettes Object
        return $query->getResult();
    }

    /**
     * Récupère toutes les informations liées a la Palette demandée
     * sort = number of likes (from more to less ) , filter = all, generated (where ownr id ==1), submission (where ownr id !=1)
     * @return Palettes
     */
    public function findByThemeBySortByPagesByFilters($theme, $sort, $filter)
    {
        $entityManager = $this->getEntityManager();

        if ($sort === 'likes') {
            $dql = " SELECT p as palette, 0 as nbr_files FROM App\Entity\Palettes p INNER JOIN p.themes t WHERE t.name = '$theme' AND p.public = 1 ";

            if($filter === 1){
                $and = " AND p.owner = 1 ";
                $dql .= $and;
            } elseif($filter === 2){
                $and = " AND p.owner != 1 ";
                $dql.=$and;
            }
                    
            $orderBy = "ORDER BY p.nbrLikes DESC";
            $dql.= $orderBy ;

            $query = $entityManager->createQuery(
                $dql
            );
            return $query->getResult();
            //dd($query->getSQL());
        } elseif ($sort === 'new') {
            $dql = " SELECT p as palette, 0 as nbr_files FROM App\Entity\Palettes p INNER JOIN p.themes t WHERE p.public = 1 AND t.name = '" . $theme . "'";

            if($filter === 1){
                $and = " AND p.owner = 1 ";
                $dql .= $and;
            } elseif($filter === 2){
                $and = " AND p.owner != 1 ";
                $dql.=$and;
            }

            $orderBy = "ORDER BY p.createdAt DESC ";
            $dql.= $orderBy;

            $query = $entityManager->createQuery(
                $dql
            );
            return $query->getResult();

        } elseif ($sort === 'save') {
            $dql = "SELECT p as palette, count(p.id) as nbr_files FROM App\Entity\Palettes p INNER JOIN p.themes t JOIN p.files f WHERE p.public = 1 AND t.name = '" . $theme . "'";

            if ($filter === 1) {
                $and = " AND p.owner = 1 ";
                $dql.= $and;
            } elseif ($filter === 2) {
                $and = " AND p.owner != 1 ";
                $dql.=$and;
            }
            $groupBy = "GROUP BY p.id ORDER BY nbr_files DESC ";
            $dql.= $groupBy;

        
            $query = $entityManager->createQuery(
                $dql
            );
            
            return $query->getResult();
        }
    }


    /**
     * Récupère toutes les palettes demandées si elles sont public
     * @return Palettes
     */
    public function findPaletteIfPublicSave($filter)
    {
        $entityManager = $this->getEntityManager();

        $dql = " SELECT p as palette, 0 as nbr_files FROM App\Entity\Palettes p LEFT JOIN p.files f WHERE p.public = 1 AND f.id IS NULL";
        if ($filter === 1) {
            $and = " AND p.owner = 1 ";
            $dql .= $and;
        } elseif ($filter === 2) {
            $and = " AND p.owner != 1 ";
            $dql.=$and;
        }
        
        $query = $entityManager->createQuery(
            $dql
        );
        return $query->getResult();
    }

    /**
     * Récupère toutes les palettes demandées si elles sont public
     * @return Palettes
     */
    public function findPaletteLikes($currentUser)
    {
        $entityManager = $this->getEntityManager();

        $dql = " SELECT p as palette FROM App\Entity\Palettes p JOIN p.likes l WHERE l.id = '$currentUser'";
        
        $query = $entityManager->createQuery(
            $dql
        );
        return $query->getResult();
    }

    /**
     * Récupère toutes les palettes demandées si elles sont public
     * @return Palettes
     */
    public function findPaletteIfPublicSaveThemes($filter, $theme)
    {
        $entityManager = $this->getEntityManager();

        $dql = " SELECT p as palette, 0 as nbr_files FROM App\Entity\Palettes p JOIN p.themes t LEFT JOIN p.files f WHERE p.public = 1 AND f.id IS NULL AND t.name = '$theme' ";
        if ($filter === 1) {
            $and = " AND p.owner = 1 ";
            $dql .= $and;
        } elseif ($filter === 2) {
            $and = " AND p.owner != 1 ";
            $dql.=$and;
        }
        
        $query = $entityManager->createQuery(
            $dql
        );
        return $query->getResult();
    }

    /**
     * Récupère toutes les palettes demandées si elles sont public
     * @return Palettes
     */
    public function findPaletteIfPublic()
    {
        $entityManager = $this->getEntityManager();

        $select = " SELECT p ";
        $from = " FROM App\Entity\Palettes p ";
        $join = "JOIN p.colors c ";
        $where = 'WHERE p.public = 1';
        
        $dqlQuery = $select . $from . $join .  $where ;
        $query = $entityManager->createQuery(
            $dqlQuery
        );
        // returns the selected palettes Object
        return $query->getResult();
    }

    /**
     * Récupère toutes les palettes demandées si elles sont public
     * @return Palettes
     */
    public function findPaletteIfPublicWithoutSaves()
    {
        $entityManager = $this->getEntityManager();

        $select = " SELECT p ";
        $from = " FROM App\Entity\Palettes p ";
        $join = "JOIN p.colors c ";
        $where = 'WHERE p.public = 1';
        
        $dqlQuery = $select . $from . $join .  $where ;
        $query = $entityManager->createQuery(
            $dqlQuery
        );
        // returns the selected palettes Object
        return $query->getResult();
    }

    /**
    * Récupère toutes les palettes demandées si elles sont public
    * @return Palettes
    */
    public function findPaletteIfPublicWithThemes()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p, c, t 
        FROM App\Entity\Palettes p
        JOIN p.colors c
        JOIN p.themes t
        WHERE p.public = 1'
        );

        // returns the selected palettes Object
        return $query->getResult();
    }

    /**
     * Récupère toutes les palettes demandées si elles sont public
     * @return Palettes
     */
    public function findPaletteIfPublicWithFiles()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p, c, f 
        FROM App\Entity\Palettes p
        JOIN p.colors c
        JOIN p.files f
        WHERE p.public = 1'
        );

        // returns the selected palettes Object
        return $query->getResult();
    }

    /**
     * Récupère toutes les palettes demandées si elles sont public
     * @return Palettes
     */
    public function findPaletteIfPublicWithUser()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p, c, u 
        FROM App\Entity\Palettes p
        JOIN p.colors c
        JOIN p.user u
        WHERE p.public = 1'
        );

        // returns the selected palettes Object
        return $query->getResult();
    }
}

