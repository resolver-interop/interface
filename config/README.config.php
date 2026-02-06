<?php return [
    'template' => dirname(__DIR__) . '/resources/README.tpl.md',
    'directory' => dirname(__DIR__) . '/src',
    'namespace' => 'ResolverInterop\\Interface\\',
    'interfaces' => [
        'ResolverService',
        'ReflectionParameterResolver',
        'ReflectionMethodResolver',
        'ReflectionPropertyResolver',
        'Resolvable',
        'ResolverThrowable',
    ],
];
