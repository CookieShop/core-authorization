<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Authorization\Repository;

/**
 * Description of OauthUsersRepository
 *
 * @author dev
 */
use Doctrine\ORM\EntityRepository;

class OauthUsersRepository extends EntityRepository
{
    /**
     * 
     * @param type $username
     * @return type
     */
    public function hasEnabledUser($username)
    {        
        $currentRepo = $this;        
        return $this->_em->transactional(function () use($currentRepo, $username) {
            //Get Core User
            $user = $currentRepo
                ->createQueryBuilder('U')
                ->where('U.username LIKE :username') 
                ->setParameter('username', $username, \Doctrine\DBAL\Types\Type::STRING)
                ->andWhere('U.enabled = :enabled') 
                ->setParameter('enabled', 1)
                ->andWhere('U.deletedAt is NULL')             
                ->getQuery()
                ->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
            return $user;
        });
    }
    
    /**
     * 
     * @param type $username
     * @return type
     */
    public function fetchByOne($username)
    {
        return $this
                ->createQueryBuilder('U')->select('R.role,U.id')
                ->join('U.role', 'R')
                ->where('U.username = :username')->setParameter('username', $username)
                ->getQuery()->getSingleResult();
    }       
}
