<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Authorization\Repository;

/**
 * Description of CoreResourcesRepository
 *
 * @author dev
 */
use Doctrine\ORM\EntityRepository;
use Adteam\Core\Authorization\Entity\CoreResources;

class CoreResourcesRepository extends EntityRepository{
    
    /**
     * 
     * @param type $data
     * @return type
     */
    public function create($data)
    {       
        return $this->_em->transactional(
            function ($em) use($data) {
                $CoreResources = new CoreResources();
                $CoreResources->setAlias($data->alias);
                $CoreResources->setResource($data->resource);
                $CoreResources->setMethodhttp($data->methodhttp);
                if(isset($data->description)){
                    $CoreResources->setDescription($data->description);
                }else{
                    $CoreResources->setDescription('');
                }
                    
                $em->persist($CoreResources); 
                $em->flush();
                $id = $CoreResources->getId();
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
               ->select("O.id,O.alias,O.resource,O.methodhttp,O.description")
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
               ->select("O.id,O.alias,O.resource,O.methodhttp,O.description")
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
                    ->delete(CoreResources::class,'o')
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
                    $update = $currentRepo->createQueryBuilder('o')
                    ->update(CoreResources::class,'o')
                    ->set('o.alias',':alias')  
                    ->setParameter('alias', $data->alias)
                    ->set('o.resource',':resource')  
                    ->setParameter('resource', $data->resource)
                    ->set('o.methodhttp',':methodhttp')  
                    ->setParameter('methodhttp', $data->methodhttp);
                    if(isset($data->description)){
                        $update->set('o.description',':description') 
                           ->setParameter('description', $data->description);
                    }else{
                        $update->set('o.description',':description') 
                           ->setParameter('description', '');
                    }                    
                    $update                       
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
                    ->select("O.id,O.resource")
                    ->Where('O.id = :id') 
                    ->setParameter('id', $id)
                    ->getQuery()->getSingleResult(); 
            if(isset($result['resource'])||is_null($result['resource'])){
                $isDelete = false;
            }
            return $isDelete;            
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException(
                        'Entity not found.'); 
        }
    } 
    
    public function getResource()
    {
        return $this
                ->createQueryBuilder('U')
                ->select('U.id, U.resource, U.methodhttp')
                ->getQuery()->getArrayResult();        
    }
}
