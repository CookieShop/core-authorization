<?php
$path=  realpath((__DIR__).'/../../../../');

return [
    'adteam_core_authorization'=>[
        'path'=>$path,
        'white_list_router'=>[
            'adbox.rest.configpublic','adbox.rest.configprivate',
            'adbox.rest.usersreset','adbox.rest.usersrecovery',
            'adbox.rest.users','adbox.rest.products',
            'adbox.rest.adminproductssync','adbox.rest.categories',
            'adbox.rest.admincreditsadjustment','adbox.rest.banners',
            'adbox.rest.admincreditsresults',
            'adbox.rest.admincreditsresultslayout',
            'adbox.rest.admincreditsresultsfiles',
            'adbox.rest.admincreditsadjustmentsettings',
            'adbox.rest.admincreditsadjustmentlayout','adbox.rest.userbalance',
            'adbox.rest.admincreditsadjustmentfiles','adbox.rest.usercart',
            'adbox.rest.admincheckoutactivationlog','adbox.rest.checkout',
            'adbox.rest.checkoutsteps','adbox.rest.cedis','adbox.rest.zipcode',
            'adbox.rest.adminorders','adbox.rest.usersinfo',
            'adbox.rest.userspassword','adbox.rest.usersdelivery',
            'adbox.rest.adminusers','adbox.rest.messages','adbox.rest.config',
            'adbox.rest.powerbi','adbox.rest.adminroles',
            'adbox.rest.adminresource','adbox.rest.adminpermissions',
            'adbox.rest.adminpermissionsbuildresource'
        ],
        'router_public'=>[
            ['resource'=>'ZF\OAuth2\Controller\Auth::token','method'=>'POST'],
            ['resource'=>'ZF\OAuth2\Controller\Auth::token','method'=>'GET'],
            ['resource'=>'ZF\OAuth2\Controller\Auth::revoke','method'=>'POST'],            
            ['resource'=>'Adbox\V1\Rest\Configpublic\Controller::collection','method'=>'GET'],
            ['resource'=>'Adbox\V1\Rest\Usersrecovery\Controller::collection','method'=>'POST'],
            ['resource'=>'Adbox\V1\Rest\Usersreset\Controller::collection','method'=>'POST'],
            ['resource'=>'Adbox\V1\Rest\Users\Controller::collection','method'=>'POST'],
            ['resource'=>'Adbox\V1\Rest\Banners\Controller::collection','method'=>'GET'],
            ['resource'=>'ZF\Apigility\Documentation\Swagger\SwaggerUi::list','method'=>'GET'],
            ['resource'=>'ZF\Apigility\Documentation\Swagger\SwaggerUi::show','method'=>'GET'],
            ['resource'=>'ZF\Apigility\Documentation\Controller::show','method'=>'GET'],
            ['resource'=>'Adbox\V1\Rest\Powerbi\Controller::collection','method'=>'GET']            
        ]
    ],
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => \Doctrine\DBAL\Driver\PDOMySql\Driver::class,
                'params' => [
                    'charset' => 'utf8',
                ],
            ],
        ],
        'driver' => [
            'Doctrine_driver_authorization' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    0 => $path.'/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Adteam\\Core\\Authorization' => 'Doctrine_driver_authorization',
                ],
            ],
        ],
    ]
];
