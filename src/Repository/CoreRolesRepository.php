<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Adteam\Core\Authorization\Repository;

/**
 * Description of CoreRolesRepository
 *
 * @author dev
 */
use Doctrine\ORM\EntityRepository;
use Adteam\Core\Authorization\Entity\CoreRoles;

class CoreRolesRepository extends EntityRepository{

    /**
     * 
     * @param type $data
     * @return type
     */
    public function create($data)
    {       
        return $this->_em->transactional(
            function ($em) use($data) {
                $CoreRoles = new CoreRoles();
                $CoreRoles->setRole($data->role);
                $em->persist($CoreRoles); 
                $em->flush();
                $id = $CoreRoles->getId();
                return $id;
            }
        );         
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function fetch($id)
    {
        return $this->createQueryBuilder('O')
               ->select("O.id,O.role")
               ->Where('O.id = :id') 
               ->setParameter('id', $id)
               ->getQuery()->getResult();
    }
    
    /**
     * 
     * @param type $params
     * @return type
     */
    public function fetchAll($params)
    {
        return $this->createQueryBuilder('O')
               ->select("O.id,O.role")
               ->getQuery()->getResult(); 
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function delete($id)
    {
        if(!$this->isDelete($id)){
            $currentRepo = $this;
            return $this->_em->transactional(
                function ($em) use($currentRepo,$id) {
                    $currentRepo->createQueryBuilder('o')
                    ->delete(CoreRoles::class,'o')
                    ->where('o.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()->execute();  
              return true;               
            });
        }        
    }
    
    /**
     * 
     * @param type $id
     * @param type $data
     * @return type
     */
    public function update($id, $data)
    {
        $currentRepo = $this;
        return $this->_em->transactional(
            function ($em) use($currentRepo,$id, $data) {
                    $currentRepo->createQueryBuilder('o')
                    ->update(CoreRoles::class,'o')
                    ->set('o.role',':role')  
                    ->setParameter('role', $data->role)
                    ->where('o.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()->execute(); 
            return true;               
        });        
    }
    
    /**
     * 
     * @param type $id
     * @return boolean
     * @throws \InvalidArgumentException
     */
    private function isDelete($id)
    {
        $isDelete = true;
        try{
            $result = $this
                    ->createQueryBuilder('O')
                    ->select("O.id,O.role")
                    ->Where('O.id = :id') 
                    ->setParameter('id', $id)
                    ->getQuery()->getSingleResult(); 
            if(isset($result['role'])||is_null($result['role'])){
                $isDelete = false;
            }
            return $isDelete;            
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException(
                        'Entity not found.'); 
        }
    }  
    
    public function getRoles()
    {
        return $this
                ->createQueryBuilder('U')->select('U.id, U.role')->getQuery()
                ->getArrayResult();
    }
}
