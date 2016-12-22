<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Authorization;

/**
 * Description of Coreresources
 *
 * @author dev
 */
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\EntityManager;
use Adteam\Core\Authorization\Entity\CoreResources as EntityCoreResources;

class Coreresources {
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
     * @param type $data
     * @return type
     */
    public function create($data)
    {       
        return $this->em->getRepository(EntityCoreResources::class)->create($data);
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function fetch($id)
    {
        return $this->em->getRepository(EntityCoreResources::class)->fetch($id); 
    }
    
    /**
     * 
     * @param type $params
     * @return type
     */
    public function fetchAll($params)
    {
        return $this->em->getRepository(EntityCoreResources::class)->fetchAll($params);
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function delete($id)
    {
      return $this->em->getRepository(EntityCoreResources::class)->delete($id);
    }
    
    /**
     * 
     * @param type $id
     * @param type $data
     * @return type
     */
    public function update($id, $data)
    {
        return $this->em->getRepository(EntityCoreResources::class)->update($id, $data);
    }   
}
