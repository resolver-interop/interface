# Research

Resolver-Interop is based on research into the following projects that provide
some form of autowiring resolver:

- [aura/di](https://github.com/auraphp/Aura.Di) (aura)
- [flightphp/container](https://github.com/flightphp/container) (flightphp)
- [ghostwriter/container](https://github.com/ghostwriter/container) (ghostwriter)
- [illuminate/container](https://github.com/illuminate/container) (illuminate)
- [joomla/di](https://github.com/joomla-framework/di) (joomla)
- [laminas/laminas-di](https://github.com/laminas/laminas-di) (laminas)
- [league/container](https://github.com/thephpleague/container) (league)
- [mindplay/unbox](https://github.com/mindplay-dk/unbox) (mindplay)
- [nette/di](https://github.com/nette/di) (netter)
- [pimple/pimple](https://github.com/silexphp/Pimple) (pimple)
- [Phalcon 4.x](https://github.com/phalcon/cphalcon/) (phalcon)
- [php-di/php-di](https://github.com/PHP-DI/PHP-DI) (phpdi)
- [ray/di](https://github.com/ray-di/Ray.Di) (ray)
- [rdlowrey/auryn](https://github.com/rdlowrey/auryn) (rdlowrey)
- [symfony/dependency-injection](https://github.com/symfony/dependency-injection) (symfony)
- [tempest/container](https://github.com/tempestphp/tempest-container) (tempest)
- [yiisoft/di](https://github.com/yiisoft/di) (yii-di)
- [yiisoft/factory](https://github.com/yiisoft/di) (yii-factory)

> **Note:**
>
> The `yii` projects are unusual, in that they keep shared service functionality
> in a `di` package, but keep new-instance functionality in a separate `factory`
> package.

The following projects were considered but eventually excluded because they use
external container systems:

- Cake v5 -- uses League
- [Mezzio](https://github.com/mezzio/) -- uses other PSR-11 containers
- Slim -- v3 used Pimple, Slim v4 et al. use any PSR-11 container

The following projects were considered but eventually excluded because they had
no obvious or discernible container system:

- [Code Igniter](https://github.com/bcit-ci/)
- [Horde](https://github.com/horde/)
- [Klein](https://github.com/klein/)
- [Lithium](https://github.com/UnionOfRAD/)
- [YAF](https://www.php.net/yaf/)
- [MediaWiki](https://github.com/wikimedia/mediawiki)


## Autowiring

The projects allow different autowiring modes:

- "Always": autowiring is always on.
- "Opt-Out": autowiring is on by default, but can be disabled (whether in toto or on a case-by-case basis).
- "Opt-In": autowiring is off by default, but can be enabled (whether in toto or on a case-by-case basis).
- "Never": not available.

|             | Always | Opt-Out | Opt-In | Never |
| ----------- | ------ | ------- | ------ | ----- |
| aura        |        |         | x      |       |
| flightphp   | x      |         |        |       |
| ghostwriter | x      |         |        |       |
| illuminate  | x      |         |        |       |
| joomla      | x      |         |        |       |
| laminas     | x      |         |        |       |
| league      |        | x       |        |       |
| mindplay    | x      |         |        |       |
| nette       |        | x       |        |       |
| phalcon     |        |         |        | x     |
| phpdi       |        | x       |        |       |
| pimple      |        |         |        | x     |
| ray         | x      |         |        |       |
| rdlowrey    | x      |         |        |       |
| symfony     |        | x       |        |       |
| tempest     |        |         | x      |       |
| yii-di      | x      |         |        |       |
| yii-factory |        |         |        | x     |

## Instantiate a service

Contains the instantiation logic, including autowiring.

Usually internal to container itself (though maybe not public); sometimes on a
separate object that receives the container; sometimes the service name is
needed, other times not.

|             | Internal? | Public? | Named? | Signature |
| ----------- | --------- | ------- | ------ | --------- |
| aura        |           | x       |        | `resolve(Blueprint $blueprint, array $contextualBlueprints = []) : object` |
| flightphp   | x         |         | x      | `resolve(string $id) : object` |
| ghostwriter | x         |         | x      | `instantiate(string $service, array $arguments = []) : object` |
| illuminate  | x         |         | x      | `resolve($abstract, $parameters = [], $raiseEvents = true) : ($abstract is class-string<TClass> ? TClass : mixed)` |
| joomla      | x         | x       | x      | `buildObject($resourceName, $shared = false) : object\|false` |
| laminas     |           | x       | x      | `create(string $name, array $params = []) : object` |
| league      | x         |         | x      | `resolve(string $id, bool $new = false) : mixed` |
| mindplay    | x         | x       | x      | `create(string $class_name, array $map = []) : mixed` |
| nette       | x         | x       | x      | `createInstance(string $class, array $args = []) : object` |
| phalcon     |           | x       |        | `resolve(?array $parameters = null, ?DiInterface $container = null) : object` |
| phpdi       |           | x       |        | `resolve(Definition $definition, array $parameters = []) : mixed` |
| pimple      | x         | x       | x      | `offsetGet($id) : mixed` (1) |
| ray         |           | x       |        | `inject(Container $container) : object` |
| rdlowrey    | x         | x       | x      | `make($name, array $args = array()) : object` |
| symfony     | x         |         | x      | `make(self $container, string $id, int $invalidBehavior) : ?object` |
| tempest     | x         |         | x      | `resolve(string $className, null\|string\|UnitEnum $tag = null, mixed ...$params) : object` |
| yii-di      | x         |         | x      | `build(string $id) : mixed` |

1. `pimple` may return a shared instance.

Public and named:

- internal: joomla, nette (args), pimple, rdlowrey (args).
- external: laminas (args)

Terminology:

|             | Build | Create | Get | Inject | Instantiate | Make | Resolve |
| ----------- | ----- | ------ | --- | ------ | ----------- | ---- | ------- |
| aura        |       |        |     |        |             |      | x       |
| flightphp   |       |        |     |        |             |      | x       |
| ghostwriter |       |        |     |        | x           |      |         |
| illuminate  |       |        |     |        |             |      | x       |
| joomla      | x     |        |     |        |             |      |         |
| laminas     |       | x      |     |        |             |      |         |
| league      |       |        |     |        |             |      | x       |
| mindplay    |       | x      |     |        |             |      |         |
| nette       |       | x      |     |        |             |      |         |
| phalcon     |       |        |     |        |             |      | x       |
| phpdi       |       |        |     |        |             |      | x       |
| pimple      |       |        | x   |        |             |      |         |
| ray         |       |        |     | x      |             |      |         |
| rdlowrey    |       |        |     |        |             | x    |         |
| symfony     |       |        |     |        |             | x    |         |
| tempest     |       |        |     |        |             |      | x       |
| yii-di      | x     |        |     |        |             |      |         |

## Annotations/Attributes

Some projects offer annotations or attributes to inform the resolver on how to
build services, whether on-demand by reflection or through a collect-and-compile
process.


|             | Annotations | Attributes | Neither |
| ----------- | ----------- | ---------- | ------- |
| aura        |             | x          |         |
| flightphp   |             |            | x       |
| ghostwriter |             |            | x       |
| illuminate  |             | x          |         |
| joomla      |             |            | x       |
| laminas     |             |            | x       |
| league      |             | x          |         |
| mindplay    |             |            | x       |
| nette       |             | x          |         |
| phalcon     |             |            | x       |
| phpdi       |             | x          |         |
| pimple      |             |            | x       |
| ray         | x           |            |         |
| rdlowrey    |             |            | x       |
| symfony     |             | x          |         |
| tempest     |             | x          |         |
| yii-di      |             |            | x       |
| yii-factory |             |            | x       |

aura:
- new: Instance (string $name) (TARGET_PARAMETER|PROPERTY)
- get: Service (string $name, ?string $methodName = null) (TARGET_PARAMETER|PROPERTY)
- plus others

illuminate:
- bind
- tag
- singleton
- scoped
- lots of framework-specific attrs

league:
- new/get: Inject(string $id) (depends on if it's shared or not?) (TARGET_PARAM | REPEATABLE)

nette:
- ???: Inject() (TARGET_PROPERTY)

phpdi
- ??? Inject(string|array|null $name = null) Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER

symfony:
- no new, no get

tempest
- Autowire
- Decorator
- Singleton
- Inject() is on properties
