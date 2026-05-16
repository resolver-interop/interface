<?php return [
    'template' => dirname(__DIR__) . '/resources/README.tpl.md',
    'directory' => dirname(__DIR__) . '/src',
    'namespace' => 'ResolverInterop\\Interface\\',
    'interfaces' => [
        'ClassResolver',
        'ReflectionParametersResolver',
        'ReflectionParameterResolver',
        'ReflectionTypeResolver',
        'ReflectionMethodResolver',
        'ReflectionPropertyResolver',
        'CallResolver',
        'Resolvable',
        'ResolverThrowable',
    ],
];
