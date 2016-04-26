<?php

require_once(__DIR__ . "/vendor/autoload.php");

use Symfony\Component\Yaml\Parser;

$options = getopt("t:s:");

if (isset($options['t'])) {
	$templates = [__DIR__ . "/templates/" . $options['t']];
} else {
	$templates = glob(__DIR__ . "/templates/*");
}

if (isset($options['s'])) {
	$schemes = [__DIR__ . "/schemes/" . $options['s']];
} else {
	$schemes = glob(__DIR__ . "/schemes/*");
}

$loader = new Twig_Loader_Filesystem(__DIR__ . "/templates");
$twig = new Twig_Environment($loader, [
	"cache" => false,
]);

$yaml = new Parser();

foreach ($schemes as $scheme_name) {
	$scheme_info = pathinfo($scheme_name);
	if ($scheme_info['extension'] == 'yml') {
		$scheme = $yaml->parse(file_get_contents($scheme_name));
	} else if ($scheme_info['extension'] == 'php') {
		$scheme = require($scheme_name);
	}

	array_walk($scheme, function (&$v,$k) {
		if (substr($k,0,4) == "base") {
			$hex = $v;
			$v = array_map(function($c) {
				return hexdec($c)/255;
			}, str_split($v,2));
			$v['hex'] = $hex;
		}
	});

	$scheme['uuid'] = uniqid();
	$scheme['slug'] = preg_replace("/[^\w\d]/", ".", $scheme['scheme']);

	foreach ($templates as $template) {
		$type = explode(".", basename($template))[0];

		error_log("Generating " . basename($scheme_name) . " - " . $type);

		if (!file_exists(__DIR__ . "/output/$type" )) {
			mkdir(__DIR__ . "/output/$type");
		}

		file_put_contents(
			__DIR__ . "/output/$type/" . $scheme['scheme'] . "." . $type,
			$twig->render(basename($template), $scheme)
		);
	}
}