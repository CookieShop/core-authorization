<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Authorization\Repository;

/**
 * Description of CorePermissionsRepository
 *
 * @author dev
 */
use Doctrine\ORM\EntityRepository;
use Adteam\Core\Authorization\Entity\CoreRoles;
use Adteam\Core\Authorization\Entity\CoreResources;
use Adteam\Core\Authorization\Entity\CorePermissions;

class CorePermissionsRepository extends EntityRepository{
    /**
     * 
     * @param type $data
     * @return type
     */
    public function create($data)
    {       
        return $this->_em->transactional(
            function ($em) use($data) {
                $role = $em->getReference(CoreRoles::class, $data->roleid);
                $resource = $em->getReference(
                        CoreResources::class, $data->resourceid);            
                $CorePermissions = new CorePermissions();
                $CorePermissions->setRole($role);
                $CorePermissions->setResource($resource);
                $CorePermissions->setPermission($data->permission);                    
                $em->persist($CorePermissions); 
                $em->flush();
                $id = $CorePermissions->getId();
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
               ->select("O.id,R.id as roleId,R.role,E.id as resourceId, ".
                       "E.resource,E.alias,O.permission")
               ->innerJoin('O.role','R') 
               ->innerJoin('O.resource','E')
               ->where('O.id = :id') 
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
               ->select("O.id,R.id as roleId,R.role,E.id as resourceId, ".
                       "E.resource,E.alias,O.permission")
               ->innerJoin('O.role','R') 
               ->innerJoin('O.resource','E')                
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
                    ->delete(CorePermissions::class,'o')
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
                ->update(CorePermissions::class,'o')
                ->set('o.permission',':permission')  
                ->setParameter('permission', $data->permission)                        
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
                    ->select("O.id,R.id as roleId,E.id as resourceId, O.permission")
                    ->innerJoin('O.role','R') 
                    ->innerJoin('O.resource','E') 
                    ->Where('O.id = :id') 
                    ->setParameter('id', $id)
                    ->getQuery()->getSingleResult(); 
            if(isset($result['roleId'])||is_null($result['roleId'])){
                $isDelete = false;
            }
            return $isDelete;            
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException(
                        'Entity not found.'); 
        }
    } 
    
    public function getPermissions()
    {
        return $this
            ->createQueryBuilder('P')->select('P.id, R.id as roleId, RE.id as '.
                    'resourceId, P.permission, RE.methodhttp, RE.resource')
            ->innerJoin('P.role', 'R')
            ->innerJoin('P.resource', 'RE')
            ->getQuery()->getArrayResult();        
    }
}
