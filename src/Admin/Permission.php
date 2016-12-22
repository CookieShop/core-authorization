<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Authorization\Admin;

/**
 * Description of Permission
 *
 * @author dev
 */
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\EntityManager;
use Adteam\Core\Authorization\Entity\CoreRoles;
use Adteam\Core\Authorization\Entity\CoreResources;
use Adteam\Core\Authorization\Entity\CorePermissions;

class Permission 
{
    /**
     *
     * @var type 
     */
    protected $service;
        
    /**
     *
     * @var type 
     */
    protected $em;
    
    /**
     * 
     * @param ServiceManager $service
     */
    public function __construct(ServiceManager $service) {
        $this->service = $service;  
        $this->em = $service->get(EntityManager::class); 
    }    
    
    /**
     * 
     */
    public function create()
    {
        $roles = $this->getRoles();
        $resources = $this->getResource();
        foreach ($roles as $role){
            foreach ($resources as $resource){
                $entity = [
                    'role'=>$role['id'],
                     'resource'=>$resource['id']
                    ];
                $exist =$this->hasExistPermision($entity);
                if(!$exist){
                    $this->insert($entity);
                }
            }
        }
    }
    
    /**
     * 
     * @return type
     */
    private function getRoles()
    {
         return $this->em->getRepository(CoreRoles::class)->fetchAll([]);
    }
    
    /**
     * 
     * @return type
     */
    private function getResource()
    {
        return $this->em->getRepository(CoreResources::class)->fetchAll([]);
    }
    
    /**
     * 
     * @param type $entity
     * @return boolean
     */
    private function hasExistPermision($entity)
    {
        $isExist = false;
        $result = $this->em->getRepository(CorePermissions::class)
                    ->createQueryBuilder('O')
                    ->select("O.id")
                    ->innerJoin('O.role','R')
                    ->innerJoin('O.resource','U')
                    ->where('R.id = :role') 
                    ->setParameter('role', $entity['role'])
                    ->andWhere('U.id = :resource') 
                    ->setParameter('resource', $entity['resource'])                
                    ->getQuery()->getResult();  
        if(count($result)>0){
            $isExist = true;
        }
        return $isExist;        
    }
    
    /**
     * 
     * @param type $entity
     */
    private function insert($entity)
    {
        $CorePermissions = new CorePermissions();
        $role = $this->em->getReference(CoreRoles::class, $entity['role']);
        $resource = $this->em->getReference(
                CoreResources::class, $entity['resource']);
        $CorePermissions->setRole($role);
        $CorePermissions->setResource($resource);
        $CorePermissions->setPermission('allow');                        
        $this->em->persist($CorePermissions); 
        $this->em->flush();         
    }
  
}
