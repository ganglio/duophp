<?php

namespace duotone_cyan_salmon;

use MischiefCollective\ColorJizz\Formats\RGB;
use MischiefCollective\ColorJizz\Formats\HEX;

$duotone_mapping = [
	0,
	1,
	2,
	3,
	4,
	8,
	15,
	14,
	5,
	6,
	7,
	9,
	10,
	13,
	12,
	11,
];

$uno = new Hex(0x0077BE);
$duo = new Hex(0xFA8072);
$black = $uno->brightness(-70);

$range     = [];
$range[]   = $black->range($uno->saturation(-20), 5, true);
$range[]   = $uno->range($uno->brightness(30),6, true);
$range[]   = $duo->range($duo->brightness(20), 4, true);
$range[]   = $duo->brightness(-15)->saturation(-40);

$colors = [];
array_walk_recursive($range, function($a) use (&$colors) {
	$colors[] = (string)$a->toHex();
});

$scheme = [
	"scheme" => "duotone-cyan-salmon",
	"author" => "Roberto Torella <roberto.torella@gmail.com>",
];

foreach ($duotone_mapping as $index=>$value) {
	$scheme[sprintf("base%02s", strtoupper(dechex($value)))] = $colors[$index];
}

return $scheme;