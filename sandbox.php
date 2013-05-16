<?php

require 'vendor/autoload.php';

$method = new CG_Method('_bar');
$method->setVisibility('private');
$method->addParameter(new CG_Parameter('foo'));
$method->addParameter(new CG_Parameter('bar'));
$method->addParameter(new CG_Parameter('zoo'));

$property = new CG_Property('foo');
$property->setDefaultValue('foo');

$class = new CG_Class('Foo');
$class->addMethod($method);
$class->addProperty($property);

$file = new CG_File();
$file->addBlock($class);

echo $file->dump();
