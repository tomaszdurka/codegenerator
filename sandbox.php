<?php

require 'vendor/autoload.php';

$method = new CG_Method('bar');

$property = new CG_Property('foo');
$property->setDefaultValue('foo');

$class = new CG_Class('Foo');
$class->addMethod($method);
$class->addProperty($property);

$file = new CG_File();
$file->addBlock($class);

echo $file->dump();
