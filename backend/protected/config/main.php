<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('local',dirname(__FILE__).DIRECTORY_SEPARATOR);


// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Портал недвижимости юга России',

    // preloading 'log' component
    'preload'=>array('log'),

    'charset'=>'windows-1251',
    'sourceLanguage'=>'ru',
    'language'=>'ru',

    'defaultController' => 'glonass',

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.helpers.*',
        'ext.giix-components.*', // giix components
    ),

    /*'controllerMap'=>array(
        'Cabinet'=>array(
            'class'=>'application.controllers.cpanel.Cabinet'
        )
    ),*/
    'modules'=>array(
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'1111',
             'ipFilters'=>array(),
                'generatorPaths' => array(
                    'ext.giix-core', // giix generators
                ),
            // 'newFileMode'=>0666,
            // 'newDirMode'=>0777,
        ),
    ),

    'behaviors'=>array(
        'runEnd'=>array(
            'class'=>'application.components.WebApplicationEndBehavior',
        ),
    ),
    // application components
    'components'=>array(

        'image'=>array(
            'class'=>'application.extensions.image.CImageComponent',
            'driver'=>'GD',
        ),
        // uncomment the following to enable URLs in path-format



        /*'db'=>array(
            'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
        ),
        // uncomment the following to use a MySQL database
        */


        'errorHandler'=>array(
            // use 'front/error' action to display errors
            'errorAction'=>'/site/error',
        ),

       /* 'db'=>array(
            'connectionString' => 'mysql:host=192.168.2.104;dbname=nztmodule3',
            'emulatePrepare' => true,
            'username' => 'all',
            'password' => '1111',
            'charset' => 'cp1251',
        ),*/

        'BNComplex'=>array(
            // 'class'=>'application.extensions.PHPPDO.CPdoDbConnection',
            // 'pdoClass'=>'PHPPDO',
            'class' => 'system.db.CDBConnection',
            'connectionString' => 'sqlsrv:Server=192.168.2.200;database=BNComplex',
            //'emulatePrepare' => false,
            'username' => 'sa',
            'password' => 'BNComplex1',
            'charset' => 'utf8',
        ),
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=nztmodule3',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'cp1251',
        ),

    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        // this is used in contact page
        'adminEmail'=>'webmaster@example.com',
        'smtp' => array(
            "host" => "smtp.spaceweb.ru", //smtp сервер
            "debug" => 1, //отображение информации дебаггера (0 - нет вообще)
            "auth" => true, //сервер требует авторизации
            "port" => 25, //порт (по-умолчанию - 25)
            "username" => "portal-uga.ru+no-reply", //имя пользователя на сервере
            "password" => "111fd11fsd11", //пароль
            "addreply" => "no-reply@portal-uga.ru", //ваш е-mail
            "replyto" => "", //e-mail ответа
            "fromname" => "Портал Юга", //имя
            "from" => "no-reply@portal-uga.ru", //от кого
            "charset" => "UTF-8", //от кого
        )
    )
);