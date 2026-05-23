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
- [nette/di](https://github.com/nette/di) (nette)
- [php-di/php-di](https://github.com/PHP-DI/PHP-DI) (phpdi)
- [ray/di](https://github.com/ray-di/Ray.Di) (ray)
- [rdlowrey/auryn](https://github.com/rdlowrey/auryn) (rdlowrey)
- [symfony/dependency-injection](https://github.com/symfony/dependency-injection) (symfony)
- [tempest/container](https://github.com/tempestphp/tempest-container) (tempest)
- [yiisoft/injector](https://github.com/yiisoft/injector) (yii-injector)

> **Note:**
>
> Among the yiisoft packages, only `yiisoft/injector` is surveyed here.
> `yiisoft/di` and `yiisoft/factory` are thin wrappers that delegate to
> `yiisoft/definitions` for the actual reflection-based parameter resolution
> (their own source contains no reflection code); they are listed in the
> "container but no autowiring" exclusions below.

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

The following projects were considered but eventually excluded because they
have a container but do not themselves do autowiring (the container source
contains no reflection-based parameter resolution):

- [pimple/pimple](https://github.com/silexphp/Pimple) — closure-based service locator
- [Phalcon 4.x](https://github.com/phalcon/cphalcon/) — service-factory registry (C extension)
- [yiisoft/di](https://github.com/yiisoft/di) — delegates to `yiisoft/definitions` for parameter resolution
- [yiisoft/factory](https://github.com/yiisoft/factory) — also delegates to `yiisoft/definitions`


## Autowiring

The projects allow different autowiring modes:

- "Always": autowiring is always on.
- "Opt-Out": autowiring is on by default, but can be disabled (whether in toto or on a case-by-case basis).
- "Opt-In": autowiring is off by default, but can be enabled (whether in toto or on a case-by-case basis).

Separate columns indicate when the autowiring decisions are made:

- "Runtime": parameter resolution happens by reflection at the time a service is requested.
- "Compiled": parameter resolution happens at compile time; the runtime container is generated code with the bindings baked in.

Libraries that support both modes are marked in both columns.

|              | Always | Opt-Out | Opt-In | Runtime | Compiled |
| ------------ | ------ | ------- | ------ | ------- | -------- |
| aura         |        |         | x      | x       |          |
| flightphp    | x      |         |        | x       |          |
| ghostwriter  | x      |         |        | x       |          |
| illuminate   | x      |         |        | x       |          |
| joomla       | x      |         |        | x       |          |
| laminas      | x      |         |        | x       | x        |
| league       |        | x       |        | x       |          |
| mindplay     | x      |         |        | x       |          |
| nette        |        | x       |        |         | x        |
| phpdi        |        | x       |        | x       | x        |
| ray          | x      |         |        |         | x        |
| rdlowrey     | x      |         |        | x       |          |
| symfony      |        | x       |        |         | x        |
| tempest      |        |         | x      | x       |          |
| yii-injector | x      |         |        | x       |          |

## Autowire by class name

The previous sections describe when autowiring is on and how each library
resolves one parameter. This section names the method that actually
performs the reflection-based instantiation, given a class identifier.
That method need not be public: many libraries split a public dispatcher
(which may also handle service-id lookup, shared-instance caching,
delegate calls, or pre-compiled bindings) from a narrower internal method
that performs the autowiring step itself. The internal one is what gets
catalogued here, regardless of visibility.

Two columns mark how the method takes its class identifier — as a
plain `String` (the typical case), or as a typed `Object` like
`Blueprint` (aura) or `ObjectDefinition` (phpdi). Symfony's
`doProcessValue(mixed $value, ...)` operates on values in the
definition tree and fits neither column.

|              | Visibility | String | Object | Signature |
| ------------ | ---------- | ------ | ------ | --------- |
| aura         | public (on internal class) |   | x | `Resolver::resolve(Blueprint $blueprint, array $contextualBlueprints = []) : object` |
| flightphp    | private | x |   | `Container::resolve(string $id) : object` |
| ghostwriter  | private | x |   | `Container::instantiate(string $service, array $arguments = []) : object` |
| illuminate   | protected | x |   | `Container::resolve($abstract, $parameters = [], $raiseEvents = true) : mixed` |
| joomla       | public | x |   | `Container::buildObject(string $resourceName, bool $shared = false) : object\|bool` |
| laminas      | protected | x |   | `Injector::createInstance(string $name, array $params) : object` |
| league       | protected | x |   | `Container::resolve(string $id, bool $new = false) : mixed` |
| mindplay     | public | x |   | `Container::create(string $class_name, array $map = []) : mixed` |
| nette        | public | x |   | `Container::createInstance(string $class, array $args = []) : object` |
| phpdi        | private |   | x | `ObjectCreator::createInstance(ObjectDefinition $definition, array $parameters) : object` |
| ray          | public | x |   | `Injector::getInstance($interface, $name = Name::ANY) : mixed` |
| rdlowrey     | private | x |   | `Injector::provisionInstance($className, $normalizedClass, array $definition) : object` |
| symfony      | private; **compile-time** only |   |   | `AutowirePass::doProcessValue(mixed $value, bool $isRoot = false) : mixed` |
| tempest      | private | x |   | `GenericContainer::autowire(string $className, mixed ...$params) : object` |
| yii-injector | public | x |   | `Injector::make(string $class, array $arguments = []) : object` |

Method-name distribution across the survey (verb root; method names may
include a suffix such as `Instance`, `Object`, or `Value`):

|              | Autowire | Build | Create | Get | Instantiate | Make | Process | Provision | Resolve |
| ------------ | -------- | ----- | ------ | --- | ----------- | ---- | ------- | --------- | ------- |
| aura         |          |       |        |     |             |      |         |           | x       |
| flightphp    |          |       |        |     |             |      |         |           | x       |
| ghostwriter  |          |       |        |     | x           |      |         |           |         |
| illuminate   |          |       |        |     |             |      |         |           | x       |
| joomla       |          | x     |        |     |             |      |         |           |         |
| laminas      |          |       | x      |     |             |      |         |           |         |
| league       |          |       |        |     |             |      |         |           | x       |
| mindplay     |          |       | x      |     |             |      |         |           |         |
| nette        |          |       | x      |     |             |      |         |           |         |
| phpdi        |          |       | x      |     |             |      |         |           |         |
| ray          |          |       |        | x   |             |      |         |           |         |
| rdlowrey     |          |       |        |     |             |      |         | x         |         |
| symfony      |          |       |        |     |             |      | x       |           |         |
| tempest      | x        |       |        |     |             |      |         |           |         |
| yii-injector |          |       |        |     |             | x    |         |           |         |

Per-project notes (public dispatcher → autowiring method, where they
differ; "autowiring method" is the row in the table above):

- **aura** — Public `Container::newInstance(string $class, array $mergeParams = [], array $mergeSetters = [])` dispatches through `Resolver::resolve(Blueprint)`. The `Resolver` is an internal collaborator of `Container`; its `resolve()` is the autowiring step proper.

- **flightphp** — Public `Container::get(string $id)` falls through to private `Container::resolve()` when the id is unregistered.

- **ghostwriter** — Public `Container::build(string $id, array $arguments = [])` dispatches through private `Container::instantiate($service, $arguments)` (alias resolution and circular-dependency tracking happen in `build()`; the reflection-based instantiation is in `instantiate()`). `Container::get()` retrieves registered services only.

- **illuminate** — Public `Container::make($abstract, $parameters)` calls protected `Container::resolve($abstract, $parameters, $raiseEvents)`. `makeWith()` is an alias for `make()`.

- **joomla** — Public `Container::buildObject(string $resourceName, bool $shared = false)` is itself the autowiring step. Returns `bool(false)` on instantiation failure rather than throwing.

- **laminas** — Public `Injector::create(string $name, array $options)` dispatches through protected `Injector::createInstance($name, $params)`. The separate `laminas-servicemanager` package wraps `create()` for shared-instance retrieval.

- **league** — Public `Container::get($id)` and `getNew($id)` call protected `Container::resolve($id, $new)`. Autowiring is opt-in per container via the `AUTO_WIRING` mode flag; without it, `resolve()` only resolves registered definitions.

- **mindplay** — Public `Container::create($class_name, $map)` is itself the autowiring step; `Container::get()` retrieves registered entries only.

- **nette** — Public `Container::createInstance($class, $args)` is the runtime autowiring step. Compile-time autowiring is a separate layer in `DI\Resolver::autowireArguments()`.

- **phpdi** — Public `Container::make($name, $parameters)` runs the definition-resolver chain; for `ObjectDefinition` (the typical autowiring case), private `ObjectCreator::createInstance(ObjectDefinition, $parameters)` performs the reflection-based instantiation.

- **ray** — Public `Injector::getInstance($interface, $name)` is itself the autowiring step; lazy-binds on miss so an unbound class still resolves on first call.

- **rdlowrey** — Public `Injector::make($name, $args)` dispatches through private `Injector::provisionInstance($className, $normalizedClass, $definition)`. `make()` also accepts an object argument (returned as-is after `prepare()` callbacks); call-time `$args` accepts positional integer keys, named keys, `:name` literals, `+name` delegates, and `@name` nested definitions (see the `## Parameter resolution chain` section).

- **symfony** — Autowiring runs at **compile time** only. `AutowirePass::doProcessValue()` is invoked by `ContainerBuilder` during compilation, not by user code. At runtime, the compiled `Container::get()` serves only pre-resolved services. The mode table marks symfony as **Opt-Out** — that classification refers to compile-time behavior, not to any runtime call.

- **tempest** — Public `GenericContainer::get(string $className, ?string $tag = null, mixed ...$params)` dispatches through private `GenericContainer::autowire(string $className, mixed ...$params)`. The variadic `...$params` is keyed by parameter name.

- **yii-injector** — Public `Injector::make($class, $arguments)` is itself the autowiring step; there is no internal split.

Public, class-string-accepting autowiring methods cluster on two API shapes:

- on the container class itself: joomla (`buildObject`), mindplay (`create`), nette (`createInstance`)
- on a separate injector class: ray (`Injector::getInstance`), yii-injector (`Injector::make`)

The three compile-only libs (symfony, nette, ray; see the `Compiled`
column in the modes table) have rows above that describe their
compile-time autowiring step rather than a runtime method. Among them,
**symfony is the only one without a runtime helper at all** — its
compiled `Container::get()` serves only pre-resolved services. nette
(`Container::createInstance`) and ray (`Injector::getInstance`) expose
runtime helpers that consult the compiled bindings.

## Resolvability check

A separate question, distinct from "autowire this class" itself, is
"can you be reasonably asked to autowire this class at all?" The
answer might be cheap (does the class exist? is it instantiable?) or
more opinionated (does the lib require a registered alias, is the
type abstract without a concrete binding, etc.). A pre-attempt check
lets a caller probe before incurring the cost — or the thrown
exception — of a full autowire-by-class-name attempt.

|              | Autowiring-side precondition method | Adjacent reflection check |
| ------------ | ----------------------------------- | ------------------------- |
| aura         | — | — |
| flightphp    | — | — |
| ghostwriter  | — | — |
| illuminate   | — | — |
| joomla       | — | — |
| laminas      | `InjectorInterface::canCreate(string $name) : bool` | — |
| league       | — | — |
| mindplay     | — | — |
| nette        | — | — |
| phpdi        | — | `ObjectDefinition::isInstantiable() : bool` |
| ray          | — | — |
| rdlowrey     | — | — |
| symfony      | — | — |
| tempest      | — | `ClassReflector::isInstantiable() : bool` |
| yii-injector | — | — |

Per-project notes:

- **laminas** — `InjectorInterface::canCreate(string $name): bool` is a **core interface contract**, not just an implementation convenience. Three implementations live within laminas-di alone: `Injector::canCreate()` (runtime; checks `class_exists()` after alias resolution); `CodeGenerator\AbstractInjector::canCreate()` (compile-time-generated injector — same contract); `Container\AutowireFactory::canCreate(ContainerInterface, $requestedName)` (factory-integration layer). The sibling `laminas-servicemanager` package generalises the same shape: `AbstractFactoryInterface::canCreate(ContainerInterface, string $requestedName): bool` is the extension point every abstract factory implements (`ReflectionBasedAbstractFactory`, `ConfigAbstractFactory`, etc.), giving each factory the freedom to apply arbitrary rules — class existence, registered config, naming conventions, anything.

- **phpdi**, **tempest** — Both expose an `isInstantiable()` method, but on **adjacent reflection-wrapper classes** (`ObjectDefinition` in phpdi, `ClassReflector` in the separate `tempest/reflection` package) rather than on the autowiring API itself. A caller would have to obtain the wrapper first; the lib's autowiring entry point (`Container::make`, `GenericContainer::get`) does not expose a parallel check.

- **All others** — No autowiring-side precondition method. The convention is "attempt the operation and catch the thrown exception." For autowire-by-class-name calls, this means a try/catch around `make()` / `build()` / `create()` / etc. is the only probe.

Patterns:

- **Only one lib treats the precondition as an interface contract.** Laminas is the standout — `canCreate` appears in two interfaces (`InjectorInterface`, `AbstractFactoryInterface`), with multiple implementations across runtime, compile-time, and factory-integration layers.
- **Two more libs expose `isInstantiable()` adjacent to the autowiring API**, on reflection-wrapper classes that the caller has to obtain first.
- **The remaining 12 libs leave the precondition implicit** — the resolver either succeeds or throws; the caller probes by attempting and catching.

Synthesis:

- A boolean precondition check for the autowire-by-class-name operation has **one strong precedent** in the survey (laminas's `InjectorInterface::canCreate`), no widespread adoption, and no contradiction. Including it in an emergent contract is defensible on the strength of laminas's interface-level commitment; omitting it is also defensible given the 12-lib silence. The two adjacent reflection-helper precedents (phpdi, tempest) suggest the question is at least recurring in the design space, even if libs solve it outside the autowiring API.
- If included, the semantics should remain **loose**. Laminas's `Injector::canCreate` implementation is just `class_exists($class)`, but `AbstractFactoryInterface::canCreate` is explicitly designed to let implementations apply arbitrary rules. A "willing to attempt" signal rather than a "guaranteed to succeed" signal is the more flexible reading.

## `$arguments` override semantics

Autowiring libraries typically accept a call-time **arguments array** —
a map of values that override what the resolver would otherwise compute
for a constructor's parameters. This section surveys how each library
interprets that array.

Four families emerge:

- **Both name and position** keys, with a per-library precedence rule.
- **Name only** — positional integer keys are ignored.
- **Variadic via PHP named-arguments** — no keyed array; values flow through
  a variadic parameter at the call site.
- **No call-time override** — values must be configured ahead of time via
  container registration.

|              | Keys accepted | Precedence | Notes |
| ------------ | ------------- | ---------- | ----- |
| aura         | name + position | position wins | extra positional ignored |
| flightphp    | — | — | no override surface |
| ghostwriter  | name + position | name wins | extra entries ignored |
| illuminate   | name only | n/a | extra named ignored |
| joomla       | — | — | no override surface |
| laminas      | name only | n/a | values wrapped in `ValueInjection` (always literal) |
| league       | — | — | no override surface |
| mindplay     | name + position + class | name > position > class-match | extra entries ignored |
| nette        | name + position | name wins | extra named raises `ServiceCreationException` |
| phpdi        | name only | n/a | extra named ignored |
| ray          | both via `getInstanceWithArgs` | (impl-defined) | not on `Injector::getInstance` itself |
| rdlowrey     | name + position + `:` + `+` + `@` | position > name > `:` > `+` > `@` | bare name keys are **service ids** (not literals); `:name` is the raw-literal form |
| tempest      | PHP named-args via variadic | n/a | no keyed array; uses PHP 8 named-argument call form |
| yii-injector | name + numeric-as-typed-pool | named first; numeric pool consulted in type pass | numeric keys must be objects; non-object integer-keyed entry throws |

Per-project notes:

- **aura** — `Container::newInstance(string $class, array $mergeParams = [], array $mergeSetters = [])`. Both positional and named keys are accepted on `$mergeParams`; positional wins. Values are taken as literals (lazies are unwrapped by the underlying `Resolver`). Extra positional keys past the constructor arity are silently ignored.

- **flightphp** — No call-time override surface. `Container::get(string $id)` takes only an identifier; constructor values come from container lookup or the parameter default.

- **ghostwriter** — `Container::build(string $id, array $arguments = [])`. Named keys checked first, then positional. Values are literals. Extra keys that don't match a parameter are silently skipped.

- **illuminate** — `Container::make($abstract, array $parameters = [])`. **Named keys only** — the `$with` stack pushes the array verbatim and the resolution loop matches by name; positional integer keys are not consulted at the parameter loop.

- **joomla** — No call-time override surface. `Container::buildObject($resourceName, bool $shared = false)` has no override array.

- **laminas** — `Injector::create(string $name, array $options = [])`. Only **named** keys. Values are wrapped in `ValueInjection` and injected as literals — call-time values are **not** interpreted as service ids to resolve. Configured `parameters` set via `$config->setParameters()` are merged into the same resolution pass; call-time entries win over configured.

- **league** — No call-time override surface. `Container::get($id)` and `getNew($id)` both accept only an id.

- **mindplay** — `Container::create(string $class_name, array $map = [])`. The richest matching surface aside from rdlowrey: name keys, integer positional keys, and class-name keys for type-hint matching. Precedence is **name > position > class-match**. Values are literals; `BoxedValueInterface` values are unwrapped after resolution. Extra keys are silently ignored.

- **nette** — `Container::createInstance(string $class, array $args = [])` for the runtime container. Both named and positional keys are accepted; named wins when both apply to the same parameter. Extra positional keys are appended (useful for variadics); extra named keys that match no parameter raise `ServiceCreationException`.

- **phpdi** — `Container::make(string $name, array $parameters = [])`. Named keys only at call time. Values are literals; service references (`\DI\get(...)`) are recognized only in pre-configured definitions, not in the call-time array.

- **ray** — `Injector::getInstance($interface, $name)` does **not** accept an override array. A separate container-level method `Container::getInstanceWithArgs(string $interface, array $params)` accepts one and delegates to `Dependency::injectWithArgs($container, $params)`; the per-key interpretation is left to the Dependency implementation.

- **rdlowrey** — `Injector::make($name, array $args = [])`. The most elaborate override syntax in the survey. Five buckets, consulted in this order per parameter: positional integer keys, plain named keys (**the value is treated as a service id to recursively `make()`**, not as a literal), `:name` for a raw literal, `+name` for a delegate callable invoked at resolution time, and `@name` for a nested definition array. Pre-configured `define($class, $args)` is merged with the call-time `$args` via `array_replace` — call-time wins.

- **tempest** — `GenericContainer::get(string $className, ?string $tag = null, mixed ...$params)`. There is **no keyed array** parameter. Instead, the variadic `...$params` is fed by PHP 8 named-argument call syntax at the call site (e.g., `$container->get(Foo::class, paramName: $value)`). Positional-variadic call form is also accepted.

- **yii-injector** — `Injector::make(string $class, array $arguments = [])`. Two distinct buckets: **named** (any non-integer key) and **numeric** (any integer key — the value **must be an object**, otherwise `InvalidArgumentException`). Named arguments are matched against parameter names first; the numeric bucket is then consulted by the type-resolution pass to match objects against parameters by `instanceof`. Variadic parameters can consume the entire numeric pool. Extra numeric objects are appended to a trailing variadic; extra named keys past the constructor are silently ignored.

Common patterns:

- **Name-keyed is universal** — every lib that has an override surface accepts name keys.
- **Position keys** are accepted by six libs (aura, ghostwriter, mindplay, nette, rdlowrey, yii-injector) with **opposite precedence rules**: rdlowrey and aura prefer positional; ghostwriter, mindplay, nette prefer named; yii-injector splits positional into a typed pool consulted only in the type-resolution pass.
- **Literal-by-default** is the prevalent value semantics. The lone exception is **rdlowrey**, where a bare name key is treated as a service id to recursively `make()`; literal values require the `:name` prefix.
- **Strict out-of-range handling** is rare. Only **nette** raises an exception on an extra named key that matches no parameter; every other lib silently ignores.
- **Three libs have no call-time override** (flightphp, joomla, league); overrides for these come exclusively from pre-registration / configuration.

## Parameter resolution chain

Once autowiring is on (per the table above), each library follows an ordered
chain to produce a value for a single constructor parameter. The chain
typically consults some subset of these sources:

- **Attribute** — a PHP 8 attribute on the parameter
  (e.g. `#[Inject]`, `#[Instance]`, `#[Autowire]`).
- **Call arg** — an entry in the `$params` / `$arguments` / `$with` / etc.
  array passed to the instantiation method, keyed by parameter name and/or
  position.
- **Pre-config** — a per-class or per-parameter definition fixed ahead of
  resolution (`define()`, configured parameters, array definitions, type
  preferences, contextual bindings, etc.).
- **Type lookup** — container retrieval by the parameter's type hint,
  possibly via aliases, type preferences, or contextual bindings.
- **Default** — the PHP default value declared on the parameter.
- **Final** — what happens when nothing else matched; almost always a thrown
  exception, sometimes `null` for nullable parameters, sometimes `[]` for
  variadics.

Numbered cells indicate the order in which the library consults each source;
blank cells mean the library does not consult that source.

|              | Attribute | Call arg | Pre-config | Type lookup | Default | Final |
| ------------ | --------- | -------- | ---------- | ----------- | ------- | ----- |
| aura         | 3         | 1        | 4          | 5           | 6       | error |
| flightphp    |           |          |            | 1           | 2       | error |
| ghostwriter  |           | 1        |            | 2           | 3       | error |
| illuminate   | 2         | 1        | 3          | 4           | 5       | null \| error |
| joomla       |           |          |            | 1           | 2       | null \| error |
| laminas      |           | 1        | 2          | 3           | 4       | error |
| league       | 2         | 1        |            | 3           | 4       | error |
| mindplay     |           | 1        |            | 2           | 3       | null \| error |
| nette        |           |          | 1          | 2           | 3       | error |
| phpdi        |           | 1        | 2          | 4           | 3       | error |
| ray          | 1         |          | 2          | 3           | 4       | error |
| rdlowrey     |           | 1        | 2          | 3           | 4       | null \| error |
| symfony      | 1         | 3        | 2          | 5           | 6       | error |
| tempest      | 2         | 1        |            | 3           | 4       | \[\] \| null \| error |
| yii-injector |           | 1        |            | 2           | 3       | null \| error |

Per-project notes:

- **aura** — `Resolver::resolve()`, `AutoResolver::reflectParam()`. Both
  positional and named call args are accepted (positional wins). The
  `#[Instance]` and `#[Service]` parameter attributes are consulted after
  call args but before parameter defaults. Type-based auto-resolution
  requires the `AutoResolver` extension, which sits above the base
  `Resolver`.

- **flightphp** — `Container::resolveDependencies()`. The minimal chain in the
  survey: required parameters get a container lookup by type, optional
  parameters get their default, everything else errors. No call args, no
  attributes, no pre-config. Union types are rejected.

- **ghostwriter** — `Container::buildParameter()`. Named call args before
  positional. Optional parameters jump straight to the parameter's default
  (PHP fills it at invocation time); the container is only consulted for
  required class-typed parameters. Builtin types on required parameters
  without a default are an error.

- **illuminate** — `Container::resolveDependencies()`, `resolveClass()`,
  `resolvePrimitive()`. Named overrides come from the `$with` stack pushed
  during `make()`. The chain splits at "class-hinted vs primitive":
  primitives fall back to default-then-null-then-error after contextual
  bindings; class-hinted parameters fall back to recursive `make()` then
  default then error. Contextual attributes are consulted as their own
  attribute layer ahead of contextual bindings.

- **joomla** — `Container::getMethodArgs()`. Reflection-based but minimal:
  container lookup by type, recursive build if the type is unregistered,
  otherwise the parameter default (if optional) or an error. Union types are
  rejected unless the parameter is nullable. No call args, no attributes,
  no pre-config.

- **laminas** — `Resolver\DependencyResolver::resolveParameters()`. Two
  flavors of pre-config: per-class "parameters" and per-context "type
  preferences". The container is consulted only after preferences are
  exhausted. Scalar / builtin types are not autowired by type — they must
  come from call args, configured parameters, or the parameter default. The
  `'*'` sentinel in configuration forces a parameter to fall through
  pre-config and re-resolve. `ValueInjection` vs `TypeInjection`
  distinguishes literal-value injection from container-id injection.

- **league** — `Argument\ArgumentResolverTrait::resolveArguments()`. Call-arg
  and attribute resolution each depend on container mode flags
  (`AUTO_WIRING`, `ATTRIBUTE_RESOLUTION`). `#[Inject]` and `#[Resolve]`
  attributes carry a service-id string. Union types are rejected.

- **mindplay** — `Container::resolve()`. The `$map` argument is consulted in
  three modes: by parameter name, by position, and by class match (for
  type-hinted parameters). The container is also consulted by parameter
  *name* (not just type), but only in "safe" mode (constructor injection,
  not free function invocation). `BoxedValueInterface` values are unwrapped
  after resolution.

- **nette** — `DI\Resolver::autowireArguments()`. Resolution is
  **compile-time** (the container is generated as PHP source). Pre-config
  covers both named and positional argument entries. Attributes are not
  consulted; PHPDoc annotations describe array element types only. There is
  no separate call-time stage — there is only compile-time configuration.

- **phpdi** — `Definition\Resolver\ParameterResolver::getParameters()`.
  Attribute-based hints (`#[Inject]`) are merged into the `Definition` at
  compile time, so attributes do not appear as a runtime step in the chain —
  they are realized as pre-config entries. Definition values pointing to
  other services (`\DI\get(...)` / `\DI\factory(...)`) are resolved
  recursively when encountered. Default consultation precedes type lookup
  because PHP-DI prefers the declared default to implicit autowiring when
  both are viable.

- **ray** — `Ray\Di\Argument` plus generated container code. Attribute-driven
  (`@Inject`, `@Named`) and Guice-style; the chain is realized in generated
  code at container compile time. No call args, no per-call overrides.

- **rdlowrey** — `Injector::provisionFuncArgs()`. The richest call-arg surface
  in the survey: positional integer keys, named keys treated as class names
  to recursively `make()`, `:name` raw literals, `+name` delegate callables,
  and `@name` nested definitions. Pre-config comes from `define()`
  (per-class) and `defineParam()` (per-name, global). When both a type hint
  and a default exist, the default wins unless the type has been explicitly
  bound (via `alias`, `delegate`, or `share`).

- **symfony** — `Compiler\AutowirePass::doProcessValue()`. Resolution is
  **compile-time**. `#[Autowire]` accepts a literal, env var
  (`%env(...)%`), expression, container parameter name, or service id —
  all evaluated by `processValue()`. `#[Target]` provides "named autowiring"
  via alias. `#[AutowireDecorated]` references the inner service in a
  decorator chain. `#[Lazy]` wraps the injection in a proxy. Scalar /
  builtin types are filtered out of autowiring unless explicitly directed
  via attribute.

- **tempest** — `GenericContainer::autowireDependencies()` plus
  `autowireBuiltinDependency()`. Call args are passed as variadic
  `mixed ...$params`, keyed by parameter name. `#[Tag]` directs both object
  and builtin resolution; builtins are resolved through registered
  initializers. The final fallback splits: variadic / iterable parameters
  fall back to `[]`, optional non-nullable to `null`, otherwise error.

- **yii-injector** — `Injector::resolveParameter()`. Named call args first;
  then a type pass that first pulls positional args of the matching class
  out of the call-args array before falling back to `container.get(type)`.
  Variadic and nullable parameters have explicit final-fallback branches.

Common patterns across the survey:

- The most common ordering is **call arg → pre-config → type lookup →
  default → error**, with optional attribute consultation layered in at the
  front (libraries that support attributes generally check them ahead of
  type lookup, but the relative position vs. call args varies).
- Libraries that support both name-keyed and position-keyed call args
  prefer one over the other: `rdlowrey` reads position first, most others
  read name first. `mindplay` adds a third mode (by class match in the
  call-arg array).
- Compile-time containers (`nette`, `symfony`, `ray`, `phpdi`'s attribute
  layer) collapse "call arg" and "pre-config" into a single compile-time
  configuration step; they have no notion of resolution-time overrides.
- Builtin / scalar types are never autowired by type alone — every surveyed
  library either rejects them (no default, no override) or routes them
  through pre-config, attributes, or initializers.
- Variadic and nullable parameters consistently get explicit final-fallback
  branches (`[]` for variadic, `null` for nullable) rather than going
  through the default-value step.

## Builtin / scalar parameter handling

PHP builtin types (`string`, `int`, `bool`, `float`, `array`, `callable`,
`iterable`, `mixed`, etc.) cannot be autowired from a class-keyed service
container by type alone — the container is keyed by class names, not by
"any string". Every surveyed library has to answer the same question:
when the resolver encounters a builtin-typed parameter, where does the
value come from?

The universal answer is **not from the container by type alone**. Builtins
flow through the call-arg array, per-class pre-configuration, the
parameter's default value, or (rarely) a dedicated builtin-injection
mechanism. A required builtin parameter with no source is an error in
every surveyed library.

|              | Container-by-type? | Where builtin values come from |
| ------------ | ------------------ | ------------------------------ |
| aura         | no | `$mergeParams` call args, `$di->params['Class']['name']` pre-config, `#[Value(name)]` attribute, default |
| flightphp    | no | parameter default only |
| ghostwriter  | no | call args, default |
| illuminate   | no | call args (`$with`), contextual `->when()->needs('$name')->give()`, default |
| joomla       | no | parameter default only |
| laminas      | no | call args (wrapped in `ValueInjection`), `$config->setParameters()`, default |
| league       | no | `#[Inject(string $id)]` resolving to a `LiteralArgument` definition, default |
| mindplay     | no | `$map` (name/position key), default |
| nette        | no | per-service `arguments` config (`*.neon` / compiled), runtime `createInstance` `$args`, default |
| phpdi        | no | scalar definitions (`\DI\value()`, `ValueDefinition`, parameter-name keys), `#[Inject('id')]`, call args, default |
| ray          | no | `#[Named('qualifier')]` qualifier routing to bound value, module bindings, default |
| rdlowrey     | no | call args with `:name` prefix (raw literal — bare name keys are service ids), `defineParam($name, $value)` global, default |
| symfony      | no by type; **yes via `#[Autowire]`** | `#[Autowire(value:)]`, `#[Autowire(env:)]`, `#[Autowire(param:)]`, `#[Autowire(expression:)]`, default |
| tempest      | **only via `#[Tag]`+`Initializer`** | tagged `Initializer<T>` resolved by `#[Tag]` name, call args, default |
| yii-injector | no | **named call args only** (numeric integer keys must be objects; scalars excluded from the positional pool) |

Per-project notes:

- **aura** — Three mechanisms: (1) `$di->params['ClassName']['paramName'] = $value` (positional or named) — the primary pre-config form; (2) `#[Value(string $name)]` parameter attribute, which wraps `LazyValue($name)` to look up a configured literal at resolution time; (3) the `$mergeParams` arg on `Container::newInstance()`. Note: the similarly-named `#[Instance(string $name)]` is **not** a literal-value attribute — it instantiates the named class.

- **flightphp** — Strictly the parameter default. Required builtins without a default raise a `ContainerException`.

- **ghostwriter** — Named or positional call args carry scalars; otherwise the parameter default; otherwise `UnresolvableParameterException` for required params.

- **illuminate** — Contextual primitives: `$container->when(Consumer::class)->needs('$paramName')->give($value)`. The `$with` stack pushed by `make()` accepts name-keyed scalars too. The `#[Give]` parameter attribute exists but is oriented toward "give me an instance of this class with these params", not bare scalar injection.

- **joomla** — Scalars are never autowired. The source-level error message is explicit: "Scalar parameters cannot be autowired and the parameter does not have a default value." Optional builtins fall through to the PHP default.

- **laminas** — Two wrapper types: `Laminas\Di\Resolver\ValueInjection` for literal values, `TypeInjection` for service ids. `DependencyResolver::prepareInjection()` decides which wrapper to use based on the value's type and the parameter's required type. Configured `$config->setParameters('ClassName', ['paramName' => $value])` is the primary pre-config form; call-time `$options` on `Injector::create()` are merged in and always treated as `ValueInjection`.

- **league** — `#[Inject(string $id)]` looks `$id` up in the container, which may carry a `LiteralArgument`-wrapped scalar definition.

- **mindplay** — `$map` argument on `Container::create()` accepts scalars under any of its matching modes (name or position; class-key doesn't apply to builtins). `BoxedValueInterface` lets a scalar be wrapped for deferred resolution.

- **nette** — Scalars live in the service config (`*.neon` `arguments:` section, or in the compiled definition); at runtime, `Container::createInstance($class, $args)` accepts scalars via the `$args` array (named or positional keys). No runtime attribute surface for scalars.

- **phpdi** — Multiple paths: `\DI\value($v)` creates a `ValueDefinition` registered in the container by id; a parameter-name keyed definition (`['paramName' => 'literal value']`) is treated similarly; `#[Inject('id')]` on a parameter pulls that definition; call-time `$parameters` array on `Container::make()` accepts scalars by name.

- **ray** — `#[Named('qualifier')]` is a routing/qualifier annotation only — it does not itself inject a value. A binding module must supply the value under that qualifier (Guice-style `bind()->annotatedWith()`) for the qualified parameter to resolve. Untreated builtins without a default raise.

- **rdlowrey** — Two surfaces: (1) `Injector::defineParam(string $paramName, $value)` registers a global default for any parameter named `$paramName`, regardless of class; (2) at the per-class `define()` or call-time `$args`, **the `:name` prefix is required for a raw literal** — a bare named entry is interpreted as a service id to `make()`. See `## $arguments override semantics` above for the full prefix vocabulary (`:` raw, `+` delegate, `@` nested definition).

- **symfony** — Builtins are filtered out of type-based autowiring at compile time. The `#[Autowire]` attribute is the canonical mechanism, accepting **exactly one** of five named arguments: `value:` (literal), `service:` (service id reference), `expression:` (ExpressionLanguage expression), `env:` (environment-variable expansion), or `param:` (parameter-bag entry). The `lazy:` flag is orthogonal and may combine with any of the five.

- **tempest** — The closest the survey has to a container-driven builtin path, but it is **gated by `#[Tag]`**, not by the type. `GenericContainer::autowireBuiltinDependency()` consults `Initializer<T>` only when the parameter carries a `#[Tag]` attribute; the lookup key is `resolveTaggedName($typeName, $tag->name)`, never the bare type name. Without `#[Tag]`, builtins fall through to the provided value → default → error, like every other surveyed lib. The `Initializer<T>` interface itself (one method, `initialize(Container): mixed`) is general — what makes builtin support possible is the tag-keyed registry, not type-symmetry with class autowiring. Tempest's class-typed path is more permissive (untagged `DynamicInitializer`s exist for class types); the builtin path is strictly tag-gated.

- **yii-injector** — Named call args carry scalars. The integer-keyed pool **must contain objects** (the runtime throws `InvalidArgumentException` on a non-object integer-keyed entry), so builtins cannot be injected by position. No container fallback for builtin types.

Common patterns:

- **Container-by-type for builtins is absent across the survey.** Tempest is the closest, but its `Initializer<T>` lookup for builtins is keyed by `#[Tag]` on the parameter (`resolveTaggedName($typeName, $tag->name)`), not by the bare type — so even tempest does not reduce a builtin from the type alone.
- **The call-arg array is the most consistent runtime source.** Every lib that has a call-arg surface accepts scalars there, with rdlowrey's "bare name keys are service ids" wrinkle as the lone exception.
- **Compile-time / config-time scalar definitions** appear in libs with a configuration phase: laminas (`setParameters`), nette (`*.neon arguments`), php-di (`\DI\value()`), symfony (`#[Autowire]` at compile pass).
- **Required builtin without a source is an error in every lib.** None silently injects `null` for a required, non-nullable builtin.

Synthesis:

- A single-class type-reducing operation can safely return `null` for builtin / pseudo types — no surveyed lib resolves a builtin from the type alone. Tempest's tagged-`Initializer<T>` pattern requires `#[Tag]` on the parameter and operates one layer up at the parameter-resolver layer, where the attribute is visible. An implementation that wants tempest-style behavior layers it above the type reducer.
- The **arguments array** is the principal runtime source for builtin values across the survey — consistent with the prevalent practice.
- **Attribute-driven scalar injection** (`#[Value]`, `#[Inject(id)]`, `#[Autowire(value:)]`) is widespread enough to warrant an attribute step in the per-parameter resolution chain.

## Complex type handling

PHP supports several type forms beyond plain class types: **union**
(`Foo|Bar`), **intersection** (`Foo&Bar`), **nullable** (`?Foo`),
**pseudo-types** (`self`, `static`, `parent`), and special builtins
(`object`, `iterable`, `callable`, `mixed`). Every surveyed library has
to decide what its resolver does when it encounters one.

A natural reducing operation answers "given a parameter's reflected
type, what single class name should I look up in the container?" For
plain class types the answer is obvious; for union, intersection,
nullable, pseudo, and builtin types the answer is more nuanced. The
survey shows most libraries either ignore complex types and fall
through to non-type sources (call args / default / error), or
special-case nullable to fall back to `null`. Three libraries (symfony,
tempest, yiisoft) implement try-each-in-order semantics for unions;
yiisoft/injector alone resolves intersections.

|              | Union | Intersection | Nullable fallback |
| ------------ | ----- | ------------ | ----------------- |
| aura         | ignored | — | default-then-error |
| flightphp    | rejected (`ContainerException`) | — | error |
| ghostwriter  | ignored | — | default-then-error |
| illuminate   | ignored | — | `null` |
| joomla       | rejected (nullable → `null`) | — | `null` |
| laminas      | ignored | — | default-then-error |
| league       | rejected (`Union types are not supported`) | — | default-then-error |
| mindplay     | ignored | — | `null` |
| nette        | ignored | — | `null` |
| phpdi        | ignored | — | default-then-error |
| ray          | — | — | (binding-driven) |
| rdlowrey     | string-coerced → fails container lookup | — | default-then-error |
| symfony      | strip builtins, try each class | strip builtins, try each class | `null` |
| tempest      | try each in order | — | default-then-error |
| yii-injector | try each in order | `instanceof`-match against numeric call-arg pool | implicit `null` (when `allowsNull()`) |

Per-project notes:

- **aura** — `AutoResolver::reflectParam()` only branches on `ReflectionNamedType`; unions and intersections are silently skipped and fall through to the default or `UnresolvedParam`.

- **flightphp** — Explicitly rejects unions with `ContainerException`. No nullable fallback — required params without a resolvable single-class type raise.

- **ghostwriter** — Same as aura: non-`ReflectionNamedType` is skipped, falling through to default-then-`UnresolvableParameterException`.

- **illuminate** — `Util::getParameterClassName()` recognizes `self` and `parent` (resolving against the declaring class). Nullable unresolvable parameters return `null` via `allowsNull()`.

- **joomla** — The only lib that **explicitly rejects non-nullable union types** with a typed exception (`Union typehints are not supported`). Nullable unions fall back to `null`.

- **laminas** — Recognizes `iterable` and `callable` as pseudo-builtins: `is_iterable()` / classes implementing `Traversable` satisfy `iterable`; `is_callable()` / classes with `__invoke()` satisfy `callable`. `ContainerInterface` is special-cased to return the resolver's bound container.

- **league** — Strict end of the spectrum: explicitly rejects both union types and `mixed`.

- **mindplay** — Unions and intersections silently ignored; nullable parameters return `null` after the fallback chain.

- **nette** — Uniquely supports collection-of-services via `@param ServiceClass[] $items` docblock annotations, resolved by `isArrayOf()`. `mixed` and `object` are explicitly named non-autowirable.

- **phpdi** — `Invoker\TypeHintResolver` recognizes `self` (resolves to the declaring class). No union or intersection handling.

- **ray** — Binding-driven (Guice-style); reflection does not pick types apart. `object`, `iterable`, `callable` are listed in `UNBOUND_TYPE` and never resolve without an explicit binding.

- **rdlowrey** — `StandardReflector::getParamTypeHint()` returns `(string) $type`, so unions and intersections yield strings like `"Foo|Bar"` that fail container lookup. Effectively "ignored" but with a confusing error path.

- **symfony** — `AutowirePass::getAutowiredReference()` strips builtin types from union/intersection types (`Foo|Bar|int` → `Foo|Bar`), then tries each remaining class type in order. `object` and `mixed` are explicitly excluded from autowiring.

- **tempest** — `autowireDependency` splits the parameter type and iterates over each named type, returning the first successful resolution. `iterable` is special-cased for collections of tagged services.

- **yii-injector** — The only lib in the survey that **actively resolves intersection types**: `ResolvingState::resolveParameterByClasses()` walks the numeric (object) call-arg pool and selects an entry that is `instanceof` every named type in the intersection. Union types iterate left-to-right; the first successful `resolveNamedType()` wins. The string `'object'` is uniquely treated as a class name and looked up in the container.

Common patterns:

- **Union types are the most-tested complex form.** Four libs handle them with try-each semantics (symfony, tempest, yii-injector, plus joomla's nullable-only special case). Two libs (flightphp, league) explicitly reject. The rest silently ignore.
- **Intersection support is rare.** Only yii-injector implements it as a meaningful operation (against a typed call-arg pool); every other lib treats intersection types as unrecognized.
- **Nullable-as-fallback to `null`** is the most common safety net — adopted by illuminate, joomla, mindplay, nette, symfony, and yii-injector implicitly. The rest fall through to default-then-error.
- **Pseudo-types are barely recognized.** Only illuminate (`self`, `parent`) and phpdi (`self`) handle them; `static` is unhandled everywhere.
- **`iterable` and `callable` as pseudo-builtins** appear in three libs: laminas (`Traversable` / `__invoke` check), nette (`@param Type[]` docblock collections), and tempest (tagged-collection iterables).

Synthesis:

- A single-class type-reducing operation (one that returns a class name or "nothing") is the natural design point — libraries that go further (try-each unions, intersection resolution, pseudo-types) layer that logic above the single-class lookup, not inside it. The reducer returns "nothing" for union, intersection, builtin, and pseudo types; the surrounding per-parameter chain handles the rest.
- Nullable-fallback to `null` is a widely adopted runtime convention but not universal — leave room for it without mandating it.
- yiisoft/injector's intersection-resolution pattern (match an `instanceof`-satisfying object from the call-arg pool) is a coherent outlier; an emergent contract MAY support it via the arguments array but need not mandate it.
- `'object'` as a container key (yii-injector's quirk) is not prevalent enough to be a survey-driven convention.

## Variadic handling

A variadic parameter (`Foo ...$foos`) can receive zero or more values
when the constructor is called. How should an autowiring resolver
behave when it encounters one — return a single value, spread an array
into multiple slots, collect every type-match from the call-arg pool?
The survey shows three groups.

- **Variadic-unaware (the majority).** Ten libs do not check
  `ReflectionParameter::isVariadic()` and treat a variadic param like
  any other: one value from the chain (call arg, container, default)
  goes into the variadic position, no spreading.
- **Spread-aware.** Three libs (illuminate, nette, yiisoft/injector)
  recognize variadics and spread an array call-arg value into multiple
  variadic positions.
- **Empty-fallback.** Four libs (illuminate, nette, tempest,
  yiisoft/injector) fall back to an empty list `[]` for an unresolved
  variadic rather than erroring.
- **Compile-time outlier.** Symfony pops the variadic parameter from
  the autowire list before the compile pass; variadics never
  participate in autowiring at all.

|              | Variadic recognized | Empty fallback | Call-arg array spread | Container type lookup |
| ------------ | ------------------- | -------------- | --------------------- | --------------------- |
| aura         | no | (default-then-error) | no (single value into first slot) | one |
| flightphp    | no | error | n/a | one |
| ghostwriter  | no | (default-then-error) | n/a | one |
| illuminate   | yes | `[]` | yes (`array_merge`) | one (merged) |
| joomla       | partial (untyped variadic skipped) | (skipped if untyped, else error) | n/a | one if typed |
| laminas      | no | error | n/a | one |
| league       | no | (default-then-error) | n/a | one |
| mindplay     | no | (default-then-error) | n/a | one |
| nette        | yes | `[]` (merged) | yes (`array_merge`) | one (merged) |
| phpdi        | no | (default-then-error) | n/a | one |
| ray          | n/a (binding-driven) | — | — | — |
| rdlowrey     | partial (skipped if unresolved) | skipped on unresolved | no | one |
| symfony      | popped before autowiring (compile-time) | n/a | n/a | n/a |
| tempest      | yes | `[]` | no | none (skipped) |
| yii-injector | yes | empty (no result added) | yes (`array_walk`) | type-matched from positional pool |

Per-project notes:

- **aura** — `ReflectionParameter::isVariadic()` is examined to flag the parameter in the Blueprint, but the autowiring resolver does not branch on it. A variadic resolves like any other parameter — one value into the first variadic slot if a call arg, type, or default supplies one; otherwise `UnresolvedParam`.

- **flightphp**, **ghostwriter**, **laminas**, **league**, **mindplay**, **phpdi** — None check `isVariadic()`. Variadics are treated as one-shot parameters: one resolved value into the variadic position, with the usual default-then-error fallback. Laminas's documentation explicitly notes "No variadic support."

- **joomla** — Untyped variadic parameters are silently skipped from the required-arg list. Typed variadics are processed like any other typed param (one container fetch).

- **illuminate** — `Container::resolveClass()` / `resolveDependencies()` check `isVariadic()`. When the call-arg array has an entry under the variadic's name with an array value, the array is merged into the result via `array_merge`, spreading values into successive slots. With no call arg, a single container fetch by type is merged into the result; unresolvable variadics fall back to `[]`.

- **nette** — At compile time, `Resolver::autowireArguments()` recognizes variadics via `isVariadic()`. A named call-arg value must be an array, which is merged via `array_merge`. Excess positional call args are appended directly into the variadic pool. With no override, the autowired type lookup contributes one value; unresolvable variadics yield an empty list.

- **rdlowrey** — `provisionFuncArgs` continues over the variadic without adding to the args array when `buildArgFromReflParam` returns null. Effectively, an unresolved variadic gets nothing.

- **symfony** — `AutowirePass::doProcessValue()` pops the trailing variadic parameter from the params list before iterating. Variadic constructor parameters do not participate in autowiring at all and must be supplied via user configuration or remain empty.

- **tempest** — `autowireBuiltinDependency()` returns `[]` unconditionally for variadic or iterable parameters when no override matches. No container type lookup is attempted for the variadic.

- **yii-injector** — The richest variadic surface. `Injector::resolveParameter` disables trailing-arg pushdown for typed variadics, then matches against named call args first (an array value is spread via `array_walk` into successive slots). The type pass via `ResolvingState::resolveParameterByClass()` then consumes the entire numeric (object) call-arg pool that matches the variadic's class type. With no overrides and no type matches, the variadic resolves to nothing.

Common patterns:

- **"Treat as single value" is the majority position** — ten of fifteen libs do not branch on `isVariadic()` at all.
- **Spreading is implemented by three libs**, all via similar means: a named call-arg with an array value merged into the result. `array_merge` (illuminate, nette) and `array_walk` (yii-injector) are the two patterns.
- **`[]` as the empty fallback** is the soft default in spread-aware libs (illuminate, nette, tempest, yii-injector). Variadic-unaware libs apply their normal default-then-error fallback to the single variadic slot.
- **Type-matched positional pool consumption** is unique to yii-injector: all positional call args of the matching class type are pulled into the variadic at once.
- **Symfony is the outlier** — variadics are excluded from autowiring entirely at compile time.

Synthesis:

- The natural per-parameter resolver returns **a single value** for a variadic, matching the majority of the survey. Implementations that want to spread arrays or consume positional pools (illuminate-style, nette-style, yii-injector-style) can do so at a higher layer that has the full arguments array in view, without altering the per-parameter contract.
- The `[]` empty-fallback convention is a useful safety net that an implementation MAY adopt.
- Symfony's "exclude variadics from autowiring entirely" fits compile-time containers but is not the prevalent runtime convention.

## Lazy / deferred resolution patterns

"Lazy" is overloaded across libraries — three distinct patterns appear
across the survey, each answering a different deferral question:

- **Deferred-resolver wrapper** — a value-carrier object the resolver
  knows to unwrap at resolution time. The wrapper carries enough
  information to recreate the value when needed; an autowiring
  resolver that encounters one calls the wrapper's unwrap method
  (often passing the container) to obtain the actual value.
- **Lazy proxy** — a generated proxy class that looks like the real
  service but defers actual instantiation until the first method call
  on the proxy.
- **Factory / provider** — a callable or `Provider`-typed object
  registered as the service definition; when the service is resolved,
  the factory is invoked.

The three patterns answer different questions: wrappers defer a single
configured value, proxies defer a whole service instance until method
call, factories convert "how to compute the value" into a callable.

|              | Deferred-resolver wrapper | Lazy proxy | Factory / provider |
| ------------ | ------------------------- | ---------- | ------------------ |
| aura         | yes — `LazyInterface::__invoke(Resolver)` (`Lazy`, `LazyNew`, `LazyGet`, `LazyValue`, `LazyCallable`, `LazyArray`, `LazyInclude`, `LazyRequire`) | no | via `LazyCallable` |
| flightphp    | no | no | no |
| ghostwriter  | no | no | closure-as-definition |
| illuminate   | no formal wrapper | no native proxy | closure-as-definition |
| joomla       | no | yes — PHP 8.4 `ReflectionClass::newLazyProxy()` via `lazy($class, $factory)` | yes — `lazy()` factory |
| laminas      | yes — `InjectionInterface::toValue($container)` (`ValueInjection`, `TypeInjection`) | no | no formal factory |
| league       | yes — `ResolvableArgument`, `LiteralArgument` | no | closure-as-definition |
| mindplay     | yes — `BoxedValueInterface::unbox($container)` (`BoxedReference`) | no | `ProviderInterface` (module bundles, not runtime factories) |
| nette        | no (compile-time) | no | compile-time code generation |
| phpdi        | yes — `Reference::resolve($container)` via `\DI\get()`; `FactoryDefinition` | yes — `->lazy()` via `ocramius/proxy-manager` | yes — `\DI\factory(...)` |
| ray          | partial — `LazyProvider` wraps a provider class name | no | yes — `ProviderInterface::get()` |
| rdlowrey     | no formal wrapper | no | yes — `delegate($name, $callable)`, `+name` in args |
| symfony      | internal `LazyClosure` only | yes — `#[Lazy]` via `symfony/var-exporter` | closures, `ServiceLocator`, `ServiceSubscriberInterface` |
| tempest      | no | yes — `#[Proxy]` attribute (impl-dependent) | no formal factory |
| yii-injector | no | no | no |

Per-project notes:

- **aura** — The most elaborate wrapper family in the survey: `LazyInterface::__invoke(Resolver $resolver)` is implemented by eight distinct classes — `Lazy` (general callable), `LazyNew` (instantiate a class), `LazyGet` (fetch a container service), `LazyValue` (fetch a configured literal), `LazyCallable` (defer a callable call), `LazyArray` (lazy array), `LazyInclude` / `LazyRequire` (defer file inclusion). Lazies compose recursively.

- **flightphp** — None. No lazy concept.

- **ghostwriter**, **illuminate** — No formal wrapper. Lazy-like behavior comes from registering a closure as a service definition; the closure is invoked at `get()`/`make()`/`build()` time.

- **joomla** — Uses **PHP 8.4's native lazy proxy** (`ReflectionClass::newLazyProxy()`) via `Container::lazy(string $class, callable $factory)`. The only surveyed lib that uses PHP's native lazy proxies (other proxy-supporting libs use external proxy generators).

- **laminas** — `InjectionInterface::toValue(ContainerInterface $container): mixed` is the wrapper contract; `ValueInjection` (literal-value) and `TypeInjection` (container-id lookup) are its two impls. Standard wrapper shape, with `toValue` as the unwrap method.

- **league** — `ResolvableArgument` wraps a service-id string; `LiteralArgument` wraps a raw value. Both implement `ArgumentInterface` with `getValue()`. The argument-resolution machinery unwraps them at resolution time. Slight outlier — `getValue()` takes no container argument.

- **mindplay** — `BoxedValueInterface::unbox(Container $container): mixed` is the wrapper contract; `BoxedReference` (lazy container fetch) is the primary impl. Standard wrapper shape, with `unbox` as the unwrap method.

- **nette** — Compile-time DI; "lazy" manifests as generated code patterns, not runtime wrappers. No direct deferred-resolver wrapper.

- **phpdi** — Three mechanisms: (1) `\DI\get($id)` returns a `Reference` value with `resolve(ContainerInterface)`, used inside definition arrays for deferred container lookups; (2) `\DI\factory($callable)` returns a `FactoryDefinition` invoked at resolution time; (3) `Container::create($class)->lazy()` produces a `CreateDefinitionHelper` configured for lazy-proxy generation via the `ocramius/proxy-manager` library.

- **ray** — `ProviderInterface::get(): mixed` is the factory contract. `LazyProvider` wraps a `ProviderInterface` class name so the provider class itself is not instantiated until needed. Both work together: deferred provider class, then deferred service from the provider.

- **rdlowrey** — `Injector::delegate(string $name, callable|string $callableOrMethodStr)` registers a factory invoked at resolution time. In call-time args, the `+name` prefix invokes a delegate callable (see `## $arguments override semantics`).

- **symfony** — `#[Lazy]` on a class or parameter triggers lazy-proxy generation via `symfony/var-exporter`'s `ProxyHelper`. The compiled container inlines the proxy code. `LazyClosure` is an internal wrapper for initializer closures, not a user-facing interface.

- **tempest** — `#[Proxy]` on a parameter or property is a hint to the container that the dependency may be instantiated lazily; actual lazy behavior depends on the container's implementation choice.

- **yii-injector** — Pure parameter injector; defers all factory and lazy concerns to whatever container is paired with it.

Patterns:

- **Deferred-resolver wrappers** are widely supported — five libs (aura, laminas, league, mindplay, phpdi) implement the same shape: a method on the wrapper takes the container (usually) and returns the resolved value. Method names vary: `__invoke($resolver)` (aura), `toValue($container)` (laminas), `unbox($container)` (mindplay), `resolve($container)` (phpdi), `getValue()` (league).
- **Lazy proxies** are the rarer pattern, requiring either external proxy-generation libraries (phpdi via `ocramius/proxy-manager`, symfony via `symfony/var-exporter`) or PHP 8.4's native lazy classes (joomla). Tempest's `#[Proxy]` is a hint rather than a generation mechanism.
- **Factory / provider** patterns are universal in some form. Closures-as-definitions (illuminate, league, ghostwriter) are the loosest; formal `Provider` types (ray) and dedicated factory wrappers (phpdi `\DI\factory`, auryn `delegate`) are tighter.

Synthesis:

- A marker interface for **deferred-resolver wrappers** is the most natural runtime extension point — five libs already implement that shape, with the verb `resolve` (phpdi's choice) being the most direct match. The unwrap method takes the container in four of the five wrapper-using libs (league is the outlier with a parameter-less form).
- A wrapper marker is a **value-carrier**, not a service-proxy marker — it should not be conflated with the lazy-proxy pattern (which is generated code, not a marker interface) or with factories (which are callables, not interfaces).
- Lazy proxies should not be required of an emergent contract — only four libs support them, each via different external machinery (or PHP 8.4 native support).
- An implementation can supply its own wrapper types for lazy lookups — e.g., a wrapper that calls the container only when its unwrap method is invoked — matching phpdi's `\DI\get()`, aura's `LazyGet`, mindplay's `BoxedReference`.

Recursive composition:

When a wrapper's resolution returns a value that itself contains other
wrappers (a `LazyArray` whose entries are `LazyGet`s, a `Reference`
inside an array of `Reference`s, etc.), who unwraps the nested
wrappers — the wrapper's own resolution code, or the consuming
resolver? The five libs with deferred-resolver wrappers split three
ways:

|              | Composition | Unwrap responsibility | Depth |
| ------------ | ----------- | --------------------- | ----- |
| aura         | yes | wrapper-side | full (each `Lazy*` iterates its own contents and calls `__invoke()` on nested wrappers) |
| laminas      | no | n/a | wrappers are metadata, not composable |
| league       | shallow | resolver-side | one level only |
| mindplay     | implicit | resolver-side | one explicit level; deeper recursion happens implicitly via container re-entry |
| phpdi        | yes | resolver-side | full (`ArrayResolver` uses `array_walk_recursive` plus the definition-resolver dispatcher) |

- **aura** — Composition is first-class. `LazyArray::__invoke()` (`Injection/LazyArray.php:32-43`) walks its contents and calls `__invoke()` on each nested `LazyInterface`; `Lazy::__invoke()` (`Injection/Lazy.php:63-85`) does the same for callable params. A `LazyArray` can contain other `LazyArray`s; nesting unwraps eagerly inside the outermost wrapper. The library even ships a `LazyLazy` class for the rare case where a caller wants to defer unwrapping the outer wrapper itself.

- **laminas** — `ValueInjection::toValue()` returns the wrapped value unchanged; `TypeInjection::toValue()` fetches from the container without inspecting the result. Neither type recognizes another `InjectionInterface` in its return value. Composition is not part of the design.

- **league** — `ArgumentResolverTrait` (`Argument/ArgumentResolverTrait.php:50-52`) checks whether a value resolved from the container is itself an `ArgumentInterface` and unwraps it once. Beyond that single level, no recursion: if unwrapping yields another argument, the value is passed through as-is.

- **mindplay** — `Container::resolve()` (`Container.php:252-254`) unwraps `BoxedValueInterface` exactly once at the point of use. Further recursion isn't explicit; it happens implicitly when the result enters parameter resolution again and triggers another `unbox()` call.

- **phpdi** — `ArrayResolver::resolve()` (`Definition/Resolver/ArrayResolver.php:39-51`) uses `array_walk_recursive()` to traverse a definition's contents; any element that's a `Definition` is dispatched through the resolver chain. Naturally handles nested compositions of arbitrary depth — a `Reference` inside an array inside another array all resolve in one pass.

Two valid full-recursion designs coexist in the survey: **wrapper-side** (aura) and **resolver-side** (phpdi). They produce the same end behavior for the consumer — a fully-resolved value — but differ in where the recursion lives.

- **Wrapper-side**: simpler resolver code, more work for wrapper implementors. The consuming resolver can trust the unwrap method's return value and stop. Matches aura's lazy family directly.
- **Resolver-side**: simpler wrapper code, more work for the resolver. The resolver must loop or recurse until a non-wrapper value is reached. Risks infinite loops if a wrapper resolves to itself; phpdi sidesteps that via definition-typed dispatch rather than identity checks.

An emergent contract that mandates **wrapper-side recursive resolution** matches aura but goes beyond league / mindplay's shallow practice. An emergent contract that mandates **resolver-side recursion** matches phpdi but pushes the burden of cycle-detection / depth-limiting onto the resolver. A hybrid contract — wrappers SHOULD return fully resolved, resolvers MUST defensively recurse anyway — adds complexity at both layers for no functional gain over a single clean choice.

## Method / property injection beyond constructor

Many libraries inject more than just the constructor: methods called on
the newly-built object (setter injection) and properties assigned on it
(field injection). Two operations naturally arise: applying an
autowired method-call to an existing object, and assigning an autowired
value to an existing object's property.

Distinguish two patterns:

- **Post-construction injection (pattern A).** During or after
  instantiation, the library discovers methods or properties marked for
  injection (via attribute, config, or naming convention) and applies
  them to the new object. This section covers pattern A.
- **Arbitrary callable invocation (pattern B).** A separate method
  (`call()`, `invoke()`, `execute()`) accepts any callable and resolves
  its parameters via autowiring, returning the call's result —
  orthogonal to per-object setter/property injection.

This section is about pattern A. Pattern B has its own section below
(`## Autowire a callable`).

|              | Setter / method injection | Property injection | Trigger mechanism |
| ------------ | ------------------------- | ------------------ | ----------------- |
| aura         | yes — `$di->setters['Class']['setX']` and `$mergeSetters` arg | no | explicit config |
| flightphp    | no | no | — |
| ghostwriter  | no | no | — |
| illuminate   | no | no | — |
| joomla       | no | no | — |
| laminas      | no | no | docs: "constructor injection only" |
| league       | yes via `Inflector::invokeMethod()` | yes via `Inflector::setProperty()` | explicit `Inflector` registration |
| mindplay     | no | no | — |
| nette        | yes — `inject*` method naming convention | yes — public property with `#[Inject]` attribute or `@inject` docblock | naming convention + `#[Inject]` |
| phpdi        | yes — `ObjectDefinition` method-injections | yes — properties in `ObjectDefinition` (private via `setAccessible()`) | `#[Inject]` attribute or config |
| ray          | yes — `#[Inject]` on methods | no direct property attribute (use method) | `#[Inject]` attribute |
| rdlowrey     | yes via `Injector::prepare($class, $callable)` | no direct (use prepare hook to call setters) | `prepare()` post-construction hook |
| symfony      | yes — `#[Required]` on methods (`AutowireRequiredMethodsPass`) | yes — `#[Required]` on properties (`AutowireRequiredPropertiesPass`) | `#[Required]` attribute (compile-time) |
| tempest      | no | yes — `#[Inject]` attribute on properties | `#[Inject]` attribute |
| yii-injector | no | no | — |

Per-project notes:

- **aura** — Setter calls are registered via `$di->setters['ClassName']['setMethod'] = $value`. `Container::newInstance()` accepts a `$mergeSetters` array for call-time overrides. Setters are unified across the class hierarchy: a parent's setter config applies to child classes.

- **flightphp**, **joomla**, **illuminate**, **ghostwriter**, **laminas**, **mindplay**, **yii-injector** — Constructor injection only for pattern A. Most of these (illuminate, ghostwriter, mindplay, yii-injector) support pattern B but do not auto-inject setters or properties on a freshly built object. See `## Autowire a callable` for the full pattern B survey.

- **league** — The `Inflector` is league's setter-injection mechanism: `$container->inflector(Trait::class)->invokeMethod('setX', [...])->setProperty('y', $value)`. The inflector matches instances by type (often an interface or trait) and applies the configured method calls and property assignments post-construction.

- **nette** — Two mechanisms: (1) Methods named `inject*` are auto-discovered on the new instance and invoked with autowired args via `InjectExtension::callInjects()` post-construction. (2) Public, non-static, non-readonly properties with `#[Inject]` (or `@inject` docblock) are set via direct assignment. Property injection requires the property to be public.

- **phpdi** — `ObjectCreator::injectMethodsAndProperties()` walks the `ObjectDefinition`'s method- and property-injections post-construction. Methods are invoked via `ReflectionMethod::invokeArgs()`; properties are set via `ReflectionProperty::setValue()` with `setAccessible(true)` to reach private / protected. The `#[Inject]` attribute (targeting `TARGET_PROPERTY | TARGET_METHOD | TARGET_PARAMETER`) is the typical trigger.

- **ray** — `#[Inject]` (targeting `TARGET_METHOD`) marks methods to be invoked with autowired arguments post-construction. Property injection is achieved indirectly through methods rather than direct property-level attributes.

- **rdlowrey** — `Injector::prepare($class, $callableOrMethodStr)` registers a post-construction hook that runs after `provisionInstance()`. The callable receives `($instance, $injector)` and can call setters or modify properties manually. The hook runs for all instances of `$class` and its interfaces, so a single `prepare()` can target multiple types.

- **symfony** — Two dedicated compile passes: `AutowireRequiredMethodsPass` finds methods annotated `#[Required]` (or `@required` in docblock) and registers them as `addMethodCall()` on the service definition. `AutowireRequiredPropertiesPass` does the same for properties (registered via `setProperty()`). Both run at compile time; the compiled container performs the calls and property assignments at instantiation.

- **tempest** — Property injection via `#[Inject]` on properties; properties are set via `PropertyReflector::set()` post-construction. The `#[Proxy]` attribute marks a property for lazy initialization. No method-injection mechanism is exposed.


Patterns:

- **Method + property injection (full pattern A)**: phpdi, nette, symfony, league — all four support both methods and properties.
- **Method injection only**: aura, ray, rdlowrey (three libs).
- **Property injection only**: tempest (one lib).
- **No pattern A (constructor-only)**: flightphp, ghostwriter, illuminate, joomla, laminas, mindplay, yii-injector (seven libs).

Trigger mechanisms split as: **attribute-driven** (nette `#[Inject]`, phpdi `#[Inject]`, ray `#[Inject]`, symfony `#[Required]`, tempest `#[Inject]`), **explicit config** (aura `$setters`, league `Inflector`), **naming convention** (nette `inject*`), and **callback hook** (rdlowrey `prepare()`).

Synthesis:

- **A per-method post-construction injection operation** matches the prevalent setter-injection step. The trigger (which methods to call) varies — config in aura, attributes in symfony/phpdi/nette/ray, naming convention in nette, callback in rdlowrey — but the per-method invocation step is mechanically similar across libs.
- **A per-property post-construction injection operation** matches the prevalent property-injection step (phpdi, nette, symfony, tempest, league). Whether the property must be public (nette) or any visibility via reflection (phpdi) is an implementation choice that need not be mandated.
- **Every surveyed setter mutates in place.** No lib implements with-clone setter injection (a `with*()` method returning a modified clone), and to the best of our knowledge no major DI/IoC container in any other language formalizes it either. Pass-by-value for the target object matches universal practice across DI ecosystems.
- **Method injection and property injection are mechanically parallel but distinct.** Most libs that do both treat them separately rather than as one combined operation — favoring two operations over one.
- **Arbitrary callable invocation (pattern B)** is well-supported across the survey (nine libs — see `## Autowire a callable`). It is consistent to separate "invoke this callable with autowiring" from "apply this method-call to this object" as two operations.

## Editorial stance on post-construction injection

The previous section documented *which* libraries support method / property injection. This section captures *what each library's own documentation says* about when to use post-construction injection relative to constructor injection — explicit preferences, warnings, refusals, or recommended scenarios.

Per-project notes:

- **aura** — Silent. The setter docs state only that "the Container supports setter injection in addition to constructor injection. (These can be combined as needed.)" No editorial preference is published.

- **flightphp**, **ghostwriter**, **illuminate**, **joomla**, **mindplay**, **yii-injector** — Silent. These libs implement constructor-only by design but do not publish doc-level statements explaining or defending the choice.

- **laminas** — Explicit constructor-first. ServiceManager docs: "we encourage you to inject all necessary dependencies via the constructor, using factories. If some dependencies use setter or interface injection, use delegator factories." Setter / interface injection is positioned as a workaround pathway requiring a delegator factory, not a peer mechanism.

- **league** — Silent. The `Inflector` documentation describes the mechanism without recommendation or warning.

- **nette** — Explicit hierarchy. Constructor injection "is suitable for mandatory dependencies that the class absolutely requires"; setter injection "is suitable for optional dependencies … as it's not guaranteed that the object will actually receive the dependency"; property injection (public properties with `#[Inject]`) is "considered inappropriate because the member property must be declared as `public`." Summary: "Public properties are generally not recommended."

- **phpdi** — Nuanced split by class role. For services, "we recommend using constructor injection and autowiring." For controllers, property injection via attributes is "the solution we recommend," on the grounds that controllers contain no business logic, are not unit-tested in isolation, and may need rewriting if the framework changes. The doc openly states the costs: "injecting in a private property breaks encapsulation," "it is not an explicit dependency," and using attributes makes the class "dependent on the container."

- **ray** — Silent on comparison; refuses property injection outright. The injections doc explicitly states "Ray.Di does not support property injection." Method injection via `#[Inject]` is documented mechanically without a stated preference relative to the constructor.

- **rdlowrey** — Explicit constructor-first. README: "Constructor injection is almost always preferable to setter injection." `Injector::prepare()` is framed as an exception for "some APIs [that] require additional post-instantiation mutations" — not a standard practice.

- **symfony** — Explicit hierarchy and the most detailed published guidance. Constructor injection is the default ("the constructor is only called once when the object is created, so you can be sure that the dependency will not change during the object's lifetime"). Setter / immutable-setter injection is recommended specifically for **optional** dependencies, for trait-based composition, and for collections built via repeated calls. Property injection draws the strongest warning: "There are mainly only disadvantages to using property injection… You cannot control when the dependency is set at all, it can be changed at any point in the object's lifetime." The `#[Required]` doc itself acknowledges "property injection having some drawbacks."

- **tempest** — Silent. Constructor injection appears in examples; no editorial stance on property injection (its sole post-construction mechanism) is published.

Patterns:

- **Explicit "prefer constructor" doc voice**: laminas, nette, phpdi (for services), rdlowrey, symfony — five libs.
- **Silent / agnostic in published docs**: aura, league, ray (comparative), tempest, plus the constructor-only libs that publish no rationale (flightphp, ghostwriter, illuminate, joomla, mindplay, yii-injector).
- **Endorses post-construction injection conditionally**: phpdi (controllers), nette (optional deps), symfony (optional deps, traits, collections).
- **Endorses property injection specifically**: phpdi, for controllers only.
- **Refuses property injection by design**: ray-di.

Recurring conditional-use scenarios — wherever a lib publishes a positive case for post-construction injection, the cited scenario is one of:

1. **Optional dependencies** that the class can function without (symfony, nette).
2. **Framework-bound, non-tested code** such as controllers (phpdi).
3. **Cyclic / mutually-referential services** that cannot be assembled purely through constructors (symfony immutable-setter).
4. **Collection building** via repeated calls that add many dependencies to the same target (symfony).
5. **Third-party code** with already-public properties (symfony).
6. **Trait-based composition** where the trait carries a setter (symfony immutable-setter).

Synthesis:

- **Constructor injection is the documented default across the ecosystem.** No surveyed lib recommends post-construction injection as the primary mechanism for ordinary services.
- **Method / setter injection is treated as a subordinate mechanism** reserved for documented exception cases. Where libs publish a stance, that stance is consistent: optional deps, collections, cyclic deps.
- **Property injection draws stronger warnings than method injection.** Three libs warn explicitly (nette "inappropriate," phpdi "breaks encapsulation … not an explicit dependency," symfony "mainly only disadvantages"), one refuses to implement it (ray-di), and only one endorses it conditionally (phpdi, for controllers).
- **Silence is consistent with the "prefer constructor" stance, not against it.** Libs that publish no comparative guidance still document constructor injection as the primary mechanism in examples and surface area; none promote post-construction injection as the default.
- **The asymmetry between method injection and property injection is consistent across the libs that comment on it.** Method injection is "for optional dependencies"; property injection is "mainly only disadvantages" / "inappropriate" / "breaks encapsulation". Method injection is treated as a legitimate-but-narrow mechanism; property injection as a last resort.

## Resolver-attribute mechanics

The previous sections covered *whether* libs use attributes and *what their docs say*. This section captures the mechanical details of attribute-driven resolution in the surveyed libs — discovery patterns and declaration flags — because those mechanics inform two directives in the spec: the `MUST NOT be IS_REPEATABLE` rule on each resolver-attribute interface, and the interface-based discovery pattern the spec adopts.

Discovery patterns (where verified by reading source):

- **symfony** — `AutowireRequiredMethodsPass` discovers via `$r->getAttributes(Required::class)`. Single-class lookup, `break` after first detection.
- **phpdi** — `AttributeBasedAutowiring` uses `$method->getAttributes(Inject::class)[0] ?? null`. Single-class lookup with explicit `[0]` for first-wins.
- **league** — `ArgumentReflectorTrait::reflectArguments()` iterates `$param->getAttributes()` (no class filter), then uses `continue 2` after the first matching attribute resolves. Operationally first-wins.

Declaration repeatability across all surveyed resolver attributes:

| lib       | attribute    | targets                                                      | repeatable? |
|-----------|--------------|--------------------------------------------------------------|-------------|
| symfony   | `#[Required]`  | `TARGET_METHOD \| TARGET_PROPERTY`                             | no          |
| symfony   | `#[Autowire]`  | `TARGET_PARAMETER`                                             | no          |
| phpdi     | `#[Inject]`    | `TARGET_PROPERTY \| TARGET_METHOD \| TARGET_PARAMETER`         | no          |
| nette     | `#[Inject]`    | `TARGET_PROPERTY`                                              | no          |
| ray       | `#[Inject]`    | `TARGET_METHOD \| TARGET_PARAMETER`                            | no          |
| tempest   | `#[Inject]`    | `TARGET_PROPERTY`                                              | no          |
| league    | `#[Inject]`    | `TARGET_PARAMETER`                                             | **yes**     |

Synthesis:

- **Discovery is single-class lookup wherever verified.** No surveyed lib uses interface-based attribute discovery (`ReflectionAttribute::IS_INSTANCEOF`) for resolver attributes. The attribute serves as a *marker* that the framework looks up by exact class; the framework owns the resolution logic. The attribute itself carries no resolution behavior.

- **Declared repeatability is rare and operationally inert.** Six of seven surveyed resolver attributes are non-repeatable. League's `#[Inject]` is the lone exception, but its resolution code stops at the first matching attribute regardless, so the `IS_REPEATABLE` flag has no observable effect.

- **Operational behavior is first-wins, universally.** No surveyed lib invokes resolution multiple times from multiple attributes on a single target — either the declaration disallows it (most cases) or the resolution code ignores subsequent attributes (league).

- **The spec's interface-based discovery pattern is novel.** Using `getAttributes(ReflectionMethodResolver::class, ReflectionAttribute::IS_INSTANCEOF)` to find any attribute implementing a resolver interface is a deliberate departure from surveyed practice, where every lib hard-codes a specific marker-attribute class. The departure gives consumers more flexibility (any attribute class can be a resolver) but creates a situation no surveyed lib faces: multiple distinct attribute classes implementing the same resolver interface on a single target. The spec's "only the first such Attribute" rule handles that situation conservatively.

- **The spec's `MUST NOT be IS_REPEATABLE` directive aligns with operational rather than declared practice.** Surveyed resolution code is first-wins regardless of declared repeatability, so the directive codifies what the ecosystem actually does, even where one lib's declaration would permit otherwise.

## Contextual binding / type preferences

A **contextual binding** is a rule of the form "when injecting into
`ConsumerA`, resolve `Foo` to `ImplX`; when injecting into `ConsumerB`,
resolve the same `Foo` to `ImplY`." The selected implementation
depends on the *consumer class*, not on the type alone.

This is distinct from:

- **Per-class constructor-arg config** (auryn `define()`, aura
  `$di->params`) — name-keyed parameter overrides for instantiation of
  a specific class, not redirection of a type for that class's
  dependencies.
- **Parameter-name routing** — symfony `#[Target]`, ray `#[Named]`,
  tempest `#[Tag]`. These select an implementation by a name applied
  to the parameter, not by the consumer class. They approximate
  contextual binding by tagging parameters rather than consumers.
- **Aliases** — `Container::alias(FooInterface::class, FooImpl::class)`.
  Global, not per-consumer.

|              | True contextual binding | Per-consumer type preferences | Parameter-name routing |
| ------------ | ----------------------- | ----------------------------- | ---------------------- |
| aura         | no | no | no |
| flightphp    | no | no | no |
| ghostwriter  | yes | yes | no |
| illuminate   | yes | yes | no |
| joomla       | no | no | no |
| laminas      | partial | yes (with optional `$context`) | no |
| league       | no | no | no |
| mindplay     | no | no | no |
| nette        | no | no | no |
| phpdi        | no | no | no |
| ray          | no | no | `#[Named]` / `annotatedWith()` |
| rdlowrey     | no | no | no |
| symfony      | no | no | `#[Target]`, `#[Autowire(service:)]` |
| tempest      | no | no | `#[Tag]` |
| yii-injector | no | no | no |

Per-project notes:

- **illuminate** — Full contextual binding via the fluent
  `Container::when($consumer)->needs($abstract)->give($implementation)`
  builder (`ContextualBindingBuilder`). The `$consumer` may be a single
  class name or an array of class names; the `$implementation` may be a
  class string, a closure, or a literal value (the latter via
  `->needs('$paramName')` for primitives). Helpers: `giveTagged($tag)`,
  `giveConfig($key, $default)`. Storage:
  `$this->contextual[$concrete][$abstract] = $implementation`.

- **ghostwriter** — `Container::bind(string $concrete, string $abstract, string $implementation)` registers a contextual binding. At resolution time, the resolver looks up the current consumer via the dependency stack (`array_key_last($this->dependencies)`) and looks up `$bindings[$concrete][$abstract]`. Smaller surface than illuminate's (no closure or tagged-collection support), but the same consumer-driven dispatch.

- **laminas** — `Config::setTypePreference(string $type, string $preference, ?string $context = null)`. With `$context` set to a class name, the preference applies only when resolving for that consumer; without `$context`, it's global. The resolver consults context-specific preferences first, then global. Partial — there is no contextual primitive form.

- **symfony** — No true contextual binding. Per-service `bind:` directives in `services.yaml` are static configuration on the consumer service definition, not consumer-driven dispatch. `#[Target('alias-name')]` routes a parameter to a service named `alias-name $paramName`, which is parameter-name routing keyed by the parameter, not the consumer. `#[Autowire(service:)]` is an explicit service-id reference at the parameter level.

- **ray** — Qualifier-based routing via `bind(FooInterface::class)->annotatedWith('qualifier')->to(FooImpl::class)`. Parameters select with `#[Named('qualifier')]`. The qualifier is a global label, not a consumer class — parameter-name routing rather than true contextual binding.

- **tempest** — `#[Tag('name')]` on parameters routes to a tagged service or initializer of the matching type. Like ray and symfony, this is parameter-name routing.

- **aura**, **rdlowrey** — Per-class constructor-arg config (`$di->params`, `define()`) can pin specific values for specific consumers, but cannot redirect a *type*-based dependency on a per-consumer basis. A consumer constructed by aura cannot say "when resolving `FooInterface` for this consumer, use `FooImplA`" — only "when constructing this consumer, set the `$foo` parameter to this specific value".

- **nette** — Per-service `bind:` and `arguments:` configuration in NEON is per-service, not per-consumer dispatch. Static at compile time.

- **flightphp**, **joomla**, **league**, **mindplay**, **phpdi**, **yii-injector** — No contextual binding mechanism beyond global aliases or per-class param config.

Patterns:

- **True contextual binding is rare.** Only illuminate and ghostwriter implement it fully — both via storage keyed by `[consumer][abstract] = impl`. Laminas implements a partial form (type preferences with optional context).
- **Parameter-name routing is more common but mechanically different.** Symfony, ray, tempest all use parameter-level attributes (`#[Target]`, `#[Named]`, `#[Tag]`) that select an implementation by an explicit qualifier on the parameter rather than by the consumer class. This is the useful pattern when the same consumer needs multiple instances of the same type (e.g., "primary database vs. secondary database").
- **Per-class constructor-arg config is widely conflated with contextual binding but is not equivalent.** It pins specific values for specific consumer constructors but does not redirect type resolution for the consumer's other dependencies.

Synthesis:

- A single-class type-reducing operation needs **no consumer context** to satisfy the prevalent practice. Only three of fifteen libs implement true contextual binding, and the prevalent design (a separate map keyed by `[consumer][abstract]`) is straightforward to layer above any single-class reducer without changing it.
- The **container** is the natural place to thread consumer context — an implementation that needs contextual binding can wrap or extend its own container for the lookup, without surfacing context as a parameter on the type-reducing operation.
- **Parameter-name routing** (`#[Target]`, `#[Named]`, `#[Tag]`) belongs at the **attribute** step of the per-parameter resolution chain, ahead of the type lookup. A chain that consults attributes first naturally accommodates these patterns.
- Neither contextual binding nor parameter-name routing is universal enough to be required of an emergent contract; both are well-supported as implementation-specific features layered above a small core.

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
| phpdi       |             | x          |         |
| ray         | x           |            |         |
| rdlowrey    |             |            | x       |
| symfony     |             | x          |         |
| tempest     |             | x          |         |
| yii-injector|             |            | x       |

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
## Autowire a callable

Beyond instantiating a class with autowired constructor parameters,
several libraries also offer **callable invocation with autowiring**:
pass any callable (closure, function name, `[object, 'method']`,
`'Class::method'`, or an invokable), have its parameters resolved by the
same autowiring machinery used for constructors, then return the
callable's result.

This is parallel to `## Autowire by class name` above and distinct from
the post-construction setter/property injection catalogued in
`## Method / property injection beyond constructor`: that section is
about applying configured injections to a newly-built object; this
section is about a free-standing invocation of any callable with
autowired arguments.

|              | Method | Args keying | Autowiring chain |
| ------------ | ------ | ----------- | ---------------- |
| aura         | — | — | — |
| flightphp    | — | — | — |
| ghostwriter  | `Container::call(callable\|string $callable, array $arguments = []) : mixed` | name + position | same as constructor |
| illuminate   | `Container::call($callback, array $parameters = [], $defaultMethod = null) : mixed` | name + position | same as constructor |
| joomla       | — | — | — |
| laminas      | — | — | — |
| league       | `ReflectionContainer::call(callable $callable, array $args = []) : mixed` | position only | same as constructor |
| mindplay     | `Container::call(callable $callback, array $map = []) : mixed` | name + position | same as constructor |
| nette        | `Container::callMethod(callable $function, array $args = []) : mixed` | name + position | same as constructor |
| phpdi        | `Container::call($callable, array $parameters = []) : mixed` | name + position | different — via `php-di/invoker`, minimal without explicit container resolvers |
| ray          | — | — | — |
| rdlowrey     | `Injector::execute($callableOrMethodStr, array $args = []) : mixed` | position only | same as constructor |
| symfony      | — | — | — |
| tempest      | `GenericContainer::invoke($method, mixed ...$params) : mixed` | variadic positional | same as constructor |
| yii-injector | `Injector::invoke(callable $callable, array $arguments = []) : mixed` | name + position | same as constructor |

Callable types accepted are universally broad — closure, function-name
string, `[object, 'method']` / `['Class', 'method']` arrays, static-method
string `'Class::method'`, and invokable objects (classes with
`__invoke()`). Many libraries also accept a **class-name string** for an
invokable class: passing the name as the callable triggers
instantiation-via-container followed by invocation (ghostwriter,
illuminate, tempest at minimum).

Per-project notes:

- **ghostwriter** — `Container::call()` shares the `buildParameter()` autowiring chain with constructor injection. Class-name strings are auto-instantiated via `get()` before invocation.

- **illuminate** — Delegates to `BoundMethod::call()`. Tracks the callable's declaring class in `buildStack` for circular-dependency detection. The third arg `$defaultMethod` allows passing `'SomeClass'` and getting `SomeClass::$defaultMethod` invoked.

- **league** — Lives on a sibling class, `ReflectionContainer`, not the main `Container`. Positional only — array keys in `$args` are ignored; the reflector matches by parameter position. Static methods are handled by nullifying the bound object after the `$reflection->isStatic()` check.

- **mindplay** — `$map` accepts name keys, position keys, or class-name keys (the same three modes as `Container::create()`).

- **nette** — Routes through `Nette\Utils\Callback::toReflection()` plus `Resolver::autowireArguments()`. Name- and position-keyed args both work; the type-based autowiring lookup is identical to constructor autowiring.

- **phpdi** — Delegates to the **separate `php-di/invoker` package**. The invoker's parameter chain (`NumericArrayResolver`, `AssociativeArrayResolver`, `DefaultValueResolver`) does **not** by default consult the container for type-based bindings. To get container-aware autowiring on calls, the invoker must be configured with container resolvers explicitly. This is the only surveyed lib where the call chain differs meaningfully from constructor autowiring.

- **rdlowrey** — `Injector::execute($callableOrMethodStr, array $args = [])`. Positional only — named parameters are ignored. Wraps the callable in an `Executable` object before invocation. Class-name strings in `$callableOrMethodStr` are auto-instantiated.

- **tempest** — Variadic signature `mixed ...$params` means args are passed positionally at the PHP call site (or by name via PHP 8 named-argument syntax, since they reach the variadic with their names preserved). Accepts `ClassReflector` / `MethodReflector` / `FunctionReflector` directly in addition to PHP callables.

- **yii-injector** — `invoke()` is the sibling of `make()`; both share the same `resolveDependencies()` chain (the per-injector wrinkles in `## $arguments override semantics` and `## Variadic handling` apply — same numeric-pool-must-be-objects rule).

Six libraries have **no callable-invocation method**: aura, flightphp,
joomla, laminas, ray, symfony. For these, arbitrary callables must be
invoked manually with whatever arguments the caller produces.

Patterns:

- **Nine of fifteen libs support callable invocation** with autowired args — a significant majority of the libs that autowire anything.
- **Eight of those nine** share the same parameter-resolution chain with constructor autowiring; **php-di is the lone exception**, routing through `php-di/invoker` whose chain is minimal unless explicitly configured with container-aware resolvers.
- **Args keying** splits 6/3: six libs (ghostwriter, illuminate, mindplay, nette, phpdi, yii-injector) accept both name and position; three (league, rdlowrey, tempest) are positional-only (tempest's PHP 8 named-args at the call site is the workaround).
- **The verb is dominated by `call` / `invoke`.** `call` in five libs (ghostwriter, illuminate, league, mindplay, phpdi); `invoke` in two (tempest, yii-injector); `callMethod` (nette) and `execute` (rdlowrey) are outliers.
- **Compile-time DI libraries (laminas, symfony) don't have this.** It doesn't fit their model — they generate factory code at compile time and have no runtime parameter-resolution machinery to point at an arbitrary callable.

Synthesis:

- The natural shape for an autowire-a-callable operation is: take a callable, take an arguments array, return the callable's result. Nine surveyed libs implement that shape directly; the prevalent verbs are `call` and `invoke`.
- An arguments array that is both name- and position-keyed (per `## $arguments override semantics`) matches the six-of-nine majority. Implementations that want to support only positional (like league / rdlowrey) can do so without departing from the contract.
- The callable's parameters sharing the autowiring chain with constructor parameters is supported by eight of nine libs. The php-di outlier — its routing through `php-di/invoker` — is an architectural choice that decouples the invoker; not a counter-argument to chain unification.
- Compile-time DI ecosystems (laminas, symfony) have no runtime analogue. An emergent contract for callable invocation is realizable in such ecosystems via a small runtime helper that constructs its own reflection chain, even though the host lib doesn't offer a parallel.

