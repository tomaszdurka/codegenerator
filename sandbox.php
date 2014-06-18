<?php

namespace CodeGenerator;

require 'vendor/autoload.php';

$file = new FileBlock();

$closureFunction = new FunctionBlock(function ($bar = null) {
    return 'foo';
});
$file->addBlock($closureFunction);

$function = new FunctionBlock('return true;');
$file->addBlock($function);

$method = new MethodBlock('_bar');
$method->setVisibility('private');
$method->addParameter(new ParameterBlock('foo'));
$method->addParameter(new ParameterBlock('bar'));
$method->addParameter(new ParameterBlock('zoo'));

$property = new PropertyBlock('foo');
$property->setDefaultValue('foo');

$constant = new ConstantBlock('FOO', 1);

$class = new ClassBlock('Foo');
$class->addMethod($method);
$class->addProperty($property);
$class->addConstant($constant);
$file->addBlock($class);

$childClass = new ClassBlock('Bar', 'Foo');
$file->addBlock($childClass);

$reflectionClass = new \ReflectionClass('\\CodeGenerator\\FunctionBlock');
$reflectedClass = ClassBlock::buildFromReflection($reflectionClass);
$file->addBlock($reflectedClass);

echo $file->dump();
