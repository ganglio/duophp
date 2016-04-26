<?php

require_once(__DIR__ . "/vendor/autoload.php");

use Symfony\Component\Yaml\Parser;

$loader = new Twig_Loader_Filesystem(__DIR__ . "/views");
$twig = new Twig_Environment($loader, [
	"cache" => false,
]);

$schemes = glob(__DIR__ . "/schemes/*");

$colors = [];
$cnt = 0;
foreach ($schemes as $scheme_name) {
	$cb = function() use ($scheme_name) {return require($scheme_name);};
	$colors[$cnt++] = $cb();
}

echo $twig->render("duotone.twig", ["schemes"=>$colors]);