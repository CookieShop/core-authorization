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
