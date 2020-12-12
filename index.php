<?php

require_once './vendor/autoload.php';
require_once './commando/libs/ArrayTraverser.php';


$data = [
	'base_1' => null,
	'base_2' => 2,
	'base_3' => 0,
	'base_4' => 'string',
	'base_5' => [],
	'base_6' => [null],
	'base_7' => [
		'layer_1_1' => null,
		'layer_1_2' => [
			[],
			'layer_2_1' => null,
			'layer_2_2' => 'layer_2_2_content',
		],
	],
];

$traverser = new \Commando\Libs\ArrayTraverser();
$traverser->data = $data;

$result = $traverser->mergeValuesForMultipleKeyChains([['base_1'], ['base_5']]);

echo PHP_EOL.PHP_EOL;
var_dump($result);
echo PHP_EOL.PHP_EOL;

