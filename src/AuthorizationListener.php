<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Authorization;

use ZF\MvcAuth\MvcAuthEvent;
use Zend\Stdlib\ArrayUtils;
use Adteam\Core\Authorization\Entity\OauthUsers;
use Doctrine\ORM\EntityManager;
use Zend\Json\Json;
use Adteam\Core\Authorization\Authorization\Acl;
/**
 * Description of AuthorizationListener
 *
 * @author dev
 */
class AuthorizationListener {  
   
    /**
     * 
     * @param MvcAuthEvent $mvcAuthEvent
     * @return type
     */
    protected $mvcAuthEvent;
    
    /**
     * 
     * @param MvcAuthEvent $mvcAuthEvent
     * @return type
     */
    public function __invoke(MvcAuthEvent $mvcAuthEvent)
    {
        $this->mvcAuthEvent = $mvcAuthEvent;
        $isAutorized = $this->isAutorized();
        return $isAutorized;
    }
    
    /**
     * 
     * @return boolean
     */
    private function isAutorized()
    {
        $currect = $this->getCurrentResource();
        if($this->isPublic($currect['resource'],$currect['method'])){
            return true;
        }
        
        if($this->hasResourceAuth($currect['resource'],$currect['method'])){
            return $this->hasUserEnabled();
        }
        return $this->hasAutorized();
    }     

    /**
     * 
     * @return boolean
     */
    private function hasAutorized()
    {
        $username = $this->getUsername();
        $role = $this->getIdentityRole($username);
        $resource = $this->getCurrentResource();
        $userenabled = $this->hasUserEnabled();
        if(!$userenabled){
            return false;
        }
        $acl = new Acl($this->mvcAuthEvent);
        $isAutorized = $acl->setAcl($role,$resource);
        return $isAutorized;        
    }

    /**
     * 
     * @param type $username
     * @return type
     */
    private function getIdentityRole($username)
    {
        try{
            $em = $this->getEntityManager();        
            $resultSet = $em->getRepository(OauthUsers::class)
                    ->fetchByOne($username);  

        } catch (\Exception $ex) {
            return null;
        }
        return isset($resultSet['role'])?$resultSet['role']:null;
    }
    
    /**
     * 
     * @return boolean
     */
    private function hasUserEnabled()
    {
        $username = $this->getUsername();
        $isUserEnabled = false;
        if(!is_null($username)){
            try{
                $isUserEnabled = $this->getEntityManager()
                        ->getRepository(OauthUsers::class)
                        ->hasEnabledUser($username); 
                if(is_array($isUserEnabled)){
                    $isUserEnabled = true;
                }else{
                    $isUserEnabled = false;
                }
            } catch (\Exception $ex) {
                $isUserEnabled = false;
            } 
        }
        return $isUserEnabled;
    }

    /**
     * 
     * @return type
     */
    private function getUsername()
    {
        $username = null;
        $mvcAuthEvent = $this->mvcAuthEvent;
        $identity = $mvcAuthEvent->getIdentity()
                ->getAuthenticationIdentity();
        $mvcEvent = $mvcAuthEvent->getMvcEvent();
        $params  = $mvcEvent->getRequest()->getPost()->toArray();
        $json = $this->getUserNameFromStringJson();        
        if(count($params)>0){
            $username = isset($params['username'])?$params['username']:'';
        }
        
        if(!is_null($identity)){
            $username = isset($identity['user_id'])?$identity['user_id']:'';
        }
        
        if(!is_null($json)&&isset($json['username'])){
            $username = isset($json['username'])?$json['username']:'';
        }
        return $username;
    }

    /**
     * 
     * @return type
     */
    private function getCurrentResource()
    {
        $resource = $this->mvcAuthEvent->getResource();
        $mvcEvent = $this->mvcAuthEvent->getMvcEvent();
        $method = $mvcEvent->getRequest()->getMethod();
        return ['resource'=>$resource,'method'=>$method]; 
    }
    
    /**
     * 
     * @param type $resource
     * @param type $method
     * @return boolean
     */
    private function hasResourceAuth($resource,$method)
    {
        $ispublic =  false;
        $public =  [
            ['resource'=>'ZF\OAuth2\Controller\Auth::token','method'=>'POST'],
            ['resource'=>'ZF\OAuth2\Controller\Auth::token','method'=>'GET'],
            ['resource'=>'ZF\OAuth2\Controller\Auth::revoke','method'=>'POST']           
        ];
        foreach ($public as $item){
            if($item['resource']===$resource &&$item['method']===$method){
                $ispublic =  true;     
                continue;
            }
        }
        return $ispublic;        
    }
    
    /**
     * Determina si el resource tipo
     * ZF\OAuth2\Controller\Auth::token es
     * publico
     * 
     * @param type $resource
     * @param type $method
     * @return boolean
     */
    private function isPublic($resource,$method)
    {
        $ispublic =  false;
        $public = $this->getPublic();
        foreach ($public as $item){
            if($item['resource']===$resource &&$item['method']===$method){
                $ispublic =  true;   
                continue;
            }
        }
        return $ispublic;
    }  
    
    /**
     * TO-DO: crear modulo independiente y poner estas rutas
     * en el module.config.php para permitir
     * rutas publicas
     * 
     * @return array
     */
    private function getPublic()
    {
        $config = $this->getConfig();
        $whitelist = isset($config['router_public'])?$config['router_public']:[];
        return $whitelist;
    }   
    
    /**
     * 
     * @param type $content
     * @return type
     */
    private function getUserNameFromStringJson()
    {
        $arrayContent = null;
        $content = $this->mvcAuthEvent->getMvcEvent()->getRequest()->getContent();
        if(!empty($content)){
            try{
                $arrayContent = Json::decode($content, Json::TYPE_ARRAY);
            } catch (\Exception $ex) {
                $arrayContent = null;
            }            
        }      
        return $arrayContent;        
    } 
    
    /**
     * 
     * @return type
     */
    private function getConfig()
    {
        $config = $this->getServiceManager()->get('config');
        $localAppConfigFilename = $config['path'].
                '/config/autoload/local.php';
        if (is_readable($localAppConfigFilename)) {            
            $config = ArrayUtils::merge($config, require($localAppConfigFilename));
        }
        return $config['adteam_core_authorization'];
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
