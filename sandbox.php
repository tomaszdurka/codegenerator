<?php
require 'vendor/autoload.php';

$file = new CG_File();


$function = new CG_Function(function($bar = null) {
	return 'foo';
});
$file->addBlock($function);


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
$file->addBlock($class);

$childClass = new CG_Class('Bar', 'Foo');
$file->addBlock($childClass);

$reflectionClass = new ReflectionClass('CG_File');
$reflectedClass = CG_Class::buildFromReflection($reflectionClass);
$file->addBlock($reflectedClass);

echo $file->dump();
