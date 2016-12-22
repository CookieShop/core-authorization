<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Adteam\Core\Authorization\Admin;
/**
 * Description of Resource
 *
 * @author dev
 */
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\EntityManager;
use Adteam\Core\Authorization\Entity\CoreResources;

class Resource
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
     * @return type
     */
    public function create()
    {
        $config = $this->service->get('config');
        $whiteListRouter =$config['adteam_core_authorization']
                ['white_list_router'];
        $routerPublic =$config['adteam_core_authorization']
                ['router_public'];        
        $entites = [];
        foreach ($whiteListRouter as $router){            
            if(isset($router)){
                $resources = $this->getResource($config['router']
                            ['routes'][$router]['options']['defaults']
                            ['controller']);
                $alias = $this->getAlias($config['router']['routes']
                                    [$router]['options']['route']);
                $entites = $this->mergeEnties($resources,$entites,$alias);
            }
        }
        $this->inserOrUpdate($entites);        
        return $entites;
    }
    
    /**
     * 
     * @param type $resources
     * @param type $entites
     * @param type $alias
     * @return string
     */
    private function mergeEnties($resources,$entites,$alias)
    {       
        foreach ($resources as $resource){
            if(!$this->hasRouterPublic($resource)){
                $entites[] = [
                    'alias'=>  $alias,
                    'resource'=>  $resource['resource'],
                    'methodhttp'=>$resource['methodhttp'],
                    'description'=>'Autogenrado'
                ];                    
            }                    
        } 
        return $entites;
    }

    /**
     * 
     * @param type $resource
     * @return boolean
     */
    private function hasRouterPublic($resource)
    {
        $isPublic = false;
        $config = $this->service->get('config');
        $routerPublic =$config['adteam_core_authorization']
                ['router_public'];   
        foreach ($routerPublic as $item){
            if($item['resource']===$resource['resource']
                    &&$item['method']===$resource['methodhttp']){ 
                $isPublic = true;
                continue;
            }           
        }
        return $isPublic;
    }

    /**
     * 
     * @param type $alias
     * @return type
     */
    private function getAlias($alias)
    {
        $strremplace = str_replace("[/v:version]/", "", $alias);
        $preremplace = preg_replace('/(:\w+[a-z]_id)/',':id',$strremplace);
        return $preremplace;
    }
    
    /**
     * 
     * @param type $resource
     * @return string
     */
    private function getResource($resource)
    {
        $entities = [];
        $config = $this->service->get('config');
        $entityHttpMethods = $config['zf-rest'][$resource]
                ['entity_http_methods'];
        $collectionHttpMethods = $config['zf-rest'][$resource]
                ['collection_http_methods'];
        foreach ($entityHttpMethods as $method){
            $entities[] = [
                'resource'=>$resource.'::entity',
                'methodhttp'=>$method
            ];
        }        
       foreach ($collectionHttpMethods as $method){
            $entities[] = [
                'resource'=>$resource.'::collection',
                'methodhttp'=>$method
            ];
        }        
        return $entities;
    }
    
    /**
     * 
     * @param type $entities
     */
    private function inserOrUpdate($entities)
    {
        $cr = $this;
        $this->em->transactional(
            function ($em) use($entities,$cr) {
                foreach ($entities as $entity){                   
                    if($cr->hasExistResource($entity)){
                       $cr->update($entity); 
                    }else{
                        $cr->insert($entity);                       
                    }       
                }                
            }
        );
    }
    
    /**
     * 
     * @param type $entity
     * @return boolean
     */
    private function hasExistResource($entity)
    {
        $isExist = false;
        $result = $this->em->getRepository(CoreResources::class)
                    ->createQueryBuilder('O')
                    ->select("O.id")
                    ->where('O.resource = :resource') 
                    ->setParameter('resource', $entity['resource'])
                    ->andWhere('O.methodhttp = :methodhttp') 
                    ->setParameter('methodhttp', $entity['methodhttp'])                
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
        $CoreResources = new CoreResources();
        $CoreResources->setAlias($entity['alias']);
        $CoreResources->setResource($entity['resource']);
        $CoreResources->setMethodhttp($entity['methodhttp']);
        $CoreResources->setDescription($entity['description']);                         
        $this->em->persist($CoreResources); 
        $this->em->flush();         
    }
    
    /**
     * 
     * @param type $entity
     */
    private function update($entity)
    {
        $this->em->getRepository(CoreResources::class)
        ->createQueryBuilder('o')
        ->update(CoreResources::class,'o')
        ->set('o.alias',':alias')  
        ->setParameter('alias',$entity['alias'])
        ->set('o.resource',':resource')  
        ->setParameter('resource',$entity['resource'])  
        ->set('o.methodhttp',':methodhttp')  
        ->setParameter('methodhttp',$entity['methodhttp']) 
        ->set('o.description',':description')  
        ->setParameter('description',$entity['description'])                                 
        ->where('o.resource = :resource') 
        ->setParameter('resource', $entity['resource'])
        ->andWhere('o.methodhttp = :methodhttp') 
        ->setParameter('methodhttp', $entity['methodhttp'])    
        ->getQuery()->execute();         
    }
    
}
