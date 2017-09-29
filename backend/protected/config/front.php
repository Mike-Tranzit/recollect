<?php

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    array(
'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format

        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'rules'=>array(
                           'pattern1'=>'route1',
                           'pattern2'=>'route2',
                           'pattern3'=>'route3',
                       ),
        ))

    )
);
?>