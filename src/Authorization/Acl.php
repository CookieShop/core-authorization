<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Adteam\Core\Authorization\Authorization;
/**
 * Description of Acl
 *
 * @author dev
 */
use Adteam\Core\Authorization\Entity\CoreResources;
use Adteam\Core\Authorization\Entity\CoreRoles;
use Adteam\Core\Authorization\Entity\CorePermissions;
use ZF\MvcAuth\MvcAuthEvent;
use Doctrine\ORM\EntityManager;

class Acl 
{
    /**
     *
     * @var type 
     */
    protected $authorization;
    
    /**
     *
     * @var type 
     */
    protected $aclmap;
    
    /**
     * 
     * @param MvcAuthEvent $mvcAuthEvent
     * @return type
     */
    protected $mvcAuthEvent;
    
    public function __construct(MvcAuthEvent $mvcAuthEvent)
    {
        $this->mvcAuthEvent = $mvcAuthEvent;
        $this->authorization = $this->mvcAuthEvent->getAuthorizationService(); 
    }

    /**
     * inicializa arreglos de permisos
     * 
     */
    public function setAcl($role,$resource)
    {
        $this->setRoles();
        $this->setResource();
        $this->setPermissions();
        $this->addRoles();
        $this->addResources();
        $this->addPermissions();
        try{
            $isAutorized = $this->authorization->isAllowed(
                    $role,$resource['resource'],$resource['method']);
        } catch (\Exception $ex) {               
            $isAutorized = false;
        }   
        return $isAutorized;
    }

    /**
     * inicializa objeto de roles de acl
     * 
     */
    private function addRoles()
    {
        foreach ($this->aclmap['roles'] as $entity){
            $this->authorization->addRole($entity['role']);
            $this->authorization->deny($entity['role'], null, null);
        }        
    }
    
    /**
     * 
     */
    private function addResources()
    {
        foreach ($this->aclmap['resources'] as $entity){
            if (null !== $entity['resource'] 
                    && 
                    (! $this->authorization->hasResource($entity['resource']) )
               ) {
                $this->authorization->addResource($entity['resource']);
            }          
        }        
    }
    
    /**
     * inicializa objeto de permisos de acl
     * 
     */
    public function addPermissions()
    {  
        foreach ($this->aclmap['permissions'] as $entity){
            $role = $this->getSearchArrayByKey(
                    'id',$entity['roleId'] , 'roles');
            $resource = $this->getSearchArrayByKey(
                    'id',$entity['resourceId'] , 'resources');     

            if($role!==false && $resource!==false && $role['role']!=='public')
            {
                if($entity['permission']==='allow')
                {
                    $this->authorization
                    ->allow(
                            $role['role'], 
                            $resource['resource'], 
                            $resource['methodhttp']
                            );
                }else
                {
                    $this->authorization
                    ->deny(
                            $role['role'],
                            $resource['resource'],
                            $resource['methodhttp']
                           );
                }                
            }
        }
    }
    
    /**
     * inicializa arreglo de roles
     * 
     */
    private function setRoles()
    {
        $em = $this->getEntityManager();        
        $resultSet = $em->getRepository(CoreRoles::class)->getRoles();
        $this->aclmap['roles'] = $resultSet;
    }
    
    /**
     * inicializa arreglo de resource
     * 
     */
    private function setResource()
    {
        $em = $this->getEntityManager();        
        $resultSet = $em->getRepository(CoreResources::class)->getResource();    
        $this->aclmap['resources'] = $resultSet;
        
    }
    
    /**
     * inicializa arreglo de permisos
     * 
     */
    private function setPermissions()
    {
        $em = $this->getEntityManager();        
        $resultSet = $em->getRepository(CorePermissions::class)
                ->getPermissions();    
        $this->aclmap['permissions'] = $resultSet;        
    }

    /**
     * Realiza Busquedas en un Arreglo
     * 
     * @param type $key
     * @param type $value
     * @param type $keyacl
     * @return type
     */
    private function getSearchArrayByKey($key,$value,$keyacl)
    {
        $entity = false;
        $keyr = array_search($value,array_column($this->aclmap[$keyacl],$key));      
        if($keyr!==false){
            $entity = $this->aclmap[$keyacl][$keyr];
        }
        return $entity;        
    } 
    
    /**
     * 
     * @return type
     */
    private function getServiceManager()
    {
        $mvcEvent = $this->mvcAuthEvent->getMvcEvent();
        return $mvcEvent->getApplication()->getServiceManager();                
    }
    
    /**
     * 
     * @return type
     */
    private function getEntityManager()
    {
        return $this->getServiceManager()->get(EntityManager::class); 
    }    
}
