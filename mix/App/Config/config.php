<?php
return array(

	'dbDSN' => array(//the database config

		'driver' => 'mysql',

		'host' => '127.0.0.1',

		'login' => 'root',

		'password' =>'kfyw888',

		'database' => 'cafev2test',

		'prefix'=>'cofe_'

	),

	'view' => 'FLEA_View_Smarty',//the template config

    	'viewConfig' => array(

        'smartyDir'         => APP_DIR. DS . 'lib' . DS . 'Smarty',

        'template_dir'      => APP_DIR . DS . 'App' . DS . 'Template',

        'compile_dir'       => APP_DIR . DS . 'App' . DS . 'Templates_c',

        'left_delimiter'    => '{',

        'right_delimiter'   => '}',

        'caching'=>false,

    ),
    'internalCacheDir'=>APP_DIR. DS . 'lib' . DS . 'Cache',
    'dbMetaCached'=>true,
	'displayErrors'=>true
);

?>