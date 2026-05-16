# Resolver-Interop Standard Interface Package

Resolver-Interop provides an interoperable package of standard interfaces for
autowiring resolver functionality. It reflects, refines, and reconciles the
common practices identified within
[several pre-existing projects][README-RESEARCH.md].

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED",  "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [BCP 14][] ([RFC 2119][], [RFC 8174][]).

## Interfaces

This package defines the following interfaces:

- [_ClassResolver_][] affords resolving a class to a new instance, and indicating whether the resolver may attempt a given class.

- [_ReflectionParametersResolver_][] affords resolving an array of [_ReflectionParameter_][]s into an array of arguments.

- [_ReflectionParameterResolver_][] affords resolving a [_ReflectionParameter_][] to an argument value.

- [_ReflectionTypeResolver_][] affords resolving a [_ReflectionType_][] to a single class-name string (or `null` when the type does not reduce to one).

- [_ReflectionMethodResolver_][] affords invoking a method on an object.

- [_ReflectionPropertyResolver_][] affords setting a property on an object.

- [_CallResolver_][] affords resolving a callable's parameters and invoking it, returning the call's result.

- [_Resolvable_][] affords an implementing object the ability to resolve itself to a value.

- [_ResolverThrowable_][] extends [_Throwable_][] to mark an [_Exception_][] as resolver-related.

### _ClassResolver_

[_ClassResolver_][] affords resolving a class to a new instance, and
indicating whether the resolver may attempt a given class.

#### _ClassResolver_ Methods

- ```php
  public function resolveClass(
      IocInterop\Interface\IocContainer $ioc,
      class-string<T> $class,
      mixed[] $arguments = [],
  ) : T;
  ```
    - Resolves the `$class` to return a new instance.

    - Directives:

        - Implementations MUST resolve the `$class` constructor parameters
          using logic identical to that specified by
          [_ReflectionParametersResolver_][]'s `resolveParameters()`.

        - Implementations MAY support [_ReflectionPropertyResolver_][]
          attributes on the instantiated `$class` properties.

        - Implementations MAY support [_ReflectionMethodResolver_][]
          attributes on the instantiated `$class` methods.

        - Implementations MAY support other forms of injection not specified
          herein.

        - Implementations MUST throw [_ResolverThrowable_][] if the `$class`
          cannot be resolved.

- ```php
  public function mayResolveClass(string $class) : bool;
  ```
    - May the resolver attempt to resolve `$class`?

    - Notes:

        - **The logic for this method is expressly unspecified.**
          Implementations may check `class_exists` and
          `ReflectionClass::isInstantiable()`, or apply stricter rules —
          e.g., requiring a registered service-name alias, rejecting
          abstract bases that lack a concrete binding, or excluding
          PHP-internal classes. Consumers should treat a `true` result
          as "the implementation is willing to try `resolveClass()`",
          not as a guarantee that resolution will succeed.

### _ReflectionParametersResolver_

[_ReflectionParametersResolver_][] affords resolving an array of
[_ReflectionParameter_][]s into an array of arguments.

#### _ReflectionParametersResolver_ Methods

- ```php
  public function resolveParameters(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionParameter[] $parameters,
      mixed[] $arguments = [],
  ) : mixed[];
  ```
    - Resolves the `$parameters` into the `$arguments`.

    - Directives:

        - Implementations MUST NOT attempt to resolve a
          [_ReflectionParameter_][] whose name or position already exists
          in the `$arguments` keys.

        - When resolving a [_ReflectionParameter_][] to an argument,
          implementations MUST do so using logic identical to that specified
          by [_ReflectionParameterResolver_][].

        - Implementations MUST retain each resolved [_ReflectionParameter_][]
          by name in the `$arguments`. Pre-filled positional arguments
          retain their integer keys; only resolver-produced arguments are
          stored by name.

        - After resolving all [_ReflectionParameter_][]s, implementations
          MUST unwrap any [_Resolvable_][] value at the top level of
          `$arguments` by calling its `resolve()` method.

    - Notes:

        - **Mixing name and position keys for the same parameter is the
          caller's responsibility.** If `$arguments` contains both a
          name key and a position key referring to the same parameter,
          behavior is undefined — implementations MAY detect the
          conflict and throw [_ResolverThrowable_][], or pass the
          duplicate keys through to the eventual invocation where PHP
          will raise a runtime error.

        - **Extra and out-of-range keys in `$arguments` pass through
          unchanged.** Keys that don't correspond to any `$parameter`
          name or position, and positional keys whose integer index
          exceeds the parameter count, are preserved in the returned
          array as-is. Implementations do not reorder the array; final
          iteration order at the call site (e.g., `new $class(...$arguments)`)
          is determined by PHP's spread semantics. Callers responsible
          for the shape of `$arguments` remain responsible for any
          downstream errors.

### _ReflectionParameterResolver_

[_ReflectionParameterResolver_][] affords resolving a
[_ReflectionParameter_][] to an argument value.

#### _ReflectionParameterResolver_ Methods

- ```php
  public function resolveParameter(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionParameter $parameter,
  ) : mixed;
  ```
    - Resolves the [_ReflectionParameter_][] to an argument value.

    - Directives:

        - Implementations MUST resolve the `$parameter` in this order:

            - If the `$parameter` has an [_Attribute_][] that implements
              [_ReflectionParameterResolver_][], implementations MUST resolve
              the `$parameter` using that attribute. If more than one such
              attribute is present, implementations MUST use the first one
              returned by `ReflectionParameter::getAttributes()` and MUST
              ignore the rest.

            - Otherwise, if the `$parameter` type is resolvable using logic
              identical to [_ReflectionTypeResolver_][]'s `resolveType()`
              and [_IocContainer_][]'s `hasService()` returns `true` for
              that type, implementations MUST resolve the `$parameter` to
              that service via [_IocContainer_][]'s `getService()`.

            - Otherwise, implementations MAY attempt to resolve the
              `$parameter` using implementation-specific logic; such logic is
              not defined herein. If implementations invoke this step, the
              returned value MUST be used to resolve the `$parameter` and
              the chain stops. If implementations decline to invoke this
              step, control passes to the next step.

            - Otherwise, if the `$parameter` has a default value,
              implementations MUST resolve the `$parameter` to that value.

        - Implementations MUST throw [_ResolverThrowable_][] if the
          `$parameter` cannot be resolved.

    - Notes:

        - **Variadic parameters resolve to a single value.** Implementations
          resolve a variadic `$parameter` once (via attribute, type, or
          default) and return that value as the variadic argument.
          Spreading across multiple variadic slots is not specified by
          this interface.

### _ReflectionTypeResolver_

[_ReflectionTypeResolver_][] affords resolving a [_ReflectionType_][] to a
single class-name string (or `null` when the type does not reduce to one).

#### _ReflectionTypeResolver_ Methods

- ```php
  public function resolveType(
      IocInterop\Interface\IocContainer $ioc,
      ?ReflectionType $type,
  ) : ?string;
  ```
    - Resolves a [_ReflectionType_][] to a string, or `null` if it cannot
    be resolved.

    - Directives:

        - If `$type` is `null`, implementations MUST return `null`.

        - For a [_ReflectionNamedType_][], implementations:

            - MUST return the type name as produced by
              `ReflectionNamedType::getName()` if the name identifies a
              class, interface, trait, or enum.

            - MAY return the name as-is, MAY return `null`, or MAY
              transform it (e.g., resolving `self` to the declaring
              class name) if the name is a PHP built-in scalar (`int`,
              `string`, `bool`, `float`, `array`, `object`, `iterable`,
              etc.) or pseudo-type (`mixed`, `void`, `never`, `null`,
              `self`, `static`, `parent`). The choice is implementation-
              defined and implementations SHOULD document their
              behaviour.

        - For a [_ReflectionUnionType_][] or
          [_ReflectionIntersectionType_][], implementations MAY return
          the name of any branch type — typically one whose name
          corresponds to a container service. Implementations that
          decline to inspect compound types MUST return `null`.

### _ReflectionMethodResolver_

[_ReflectionMethodResolver_][] affords invoking a method on an object.

#### _ReflectionMethodResolver_ Methods

- ```php
  public function resolveMethod(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionMethod $method,
      object $object,
  ) : void;
  ```
    - Invokes the [_ReflectionMethod_][] on the `$object`.

    - Directives:

        - Implementations MUST invoke `$method` on `$object` with the
          resolved arguments.

        - Implementations MUST support parameter injection using logic
          identical to that specified by
          [_ReflectionParametersResolver_][]'s `resolveParameters()`.

        - Implementations MUST throw [_ResolverThrowable_][] if resolution
          of `$method` is attempted and fails. Orchestrating
          implementations (those that iterate over the methods of a class
          looking for [_ReflectionMethodResolver_][] attributes) MAY skip
          methods with no applicable attribute; the MUST-throw rule
          applies when resolution is invoked, not when the orchestrator
          declines to invoke it.

### _ReflectionPropertyResolver_

[_ReflectionPropertyResolver_][] affords setting a property on an object.

#### _ReflectionPropertyResolver_ Methods

- ```php
  public function resolveProperty(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionProperty $property,
      object $object,
  ) : void;
  ```
    - Sets the [_ReflectionProperty_][] on an object.

    - Directives:

        - Implementations MUST set the value of `$property` on `$object`.

        - If `$property` has an [_Attribute_][] that implements
          [_ReflectionPropertyResolver_][], implementations MUST resolve
          the `$property` using that attribute. If more than one such
          attribute is present, implementations MUST use the first one
          returned by `ReflectionProperty::getAttributes()` and MUST
          ignore the rest.

        - Implementations MAY support other forms of property resolution
          not specified herein.

        - Implementations MUST throw [_ResolverThrowable_][] if resolution
          of `$property` is attempted and fails. Orchestrating
          implementations (those that iterate over the properties of a
          class looking for [_ReflectionPropertyResolver_][] attributes)
          MAY skip properties with no applicable attribute; the MUST-throw
          rule applies when resolution is invoked, not when the
          orchestrator declines to invoke it.

### _CallResolver_

[_CallResolver_][] affords resolving a callable's parameters and invoking
it, returning the call's result.

#### _CallResolver_ Methods

- ```php
  public function resolveCall(
      IocInterop\Interface\IocContainer $ioc,
      callable $callable,
      mixed[] $arguments = [],
  ) : mixed;
  ```
    - Resolves the `$callable` to return its result.

    - Directives:

        - Implementations MUST invoke the `$callable` with the resolved
          arguments and MUST return the result of that invocation.

        - Implementations MUST resolve the `$callable`'s parameters using
          logic identical to that specified by
          [_ReflectionParametersResolver_][]'s `resolveParameters()`,
          including the `$arguments` pre-fill and [_Resolvable_][]-unwrap
          semantics specified there.

        - Implementations MUST throw [_ResolverThrowable_][] if the
          `$callable` cannot be resolved.

### _Resolvable_

[_Resolvable_][] affords an implementing object the ability to resolve itself
to a value.

- Notes:

    - **`Resolvable` defers container calls until parameter
      resolution.** Wrapping a container lookup in a `Resolvable`
      lets callers pass it in `$arguments` without forcing the
      lookup at construction time; the resolver invokes `resolve()`
      only at the moment of parameter resolution.

#### _Resolvable_ Methods

- ```php
  public function resolve(IocInterop\Interface\IocContainer $ioc) : mixed;
  ```
    - Resolves the implementing object to a value.

    - Directives:

        - Implementations MUST return a value that is neither itself a
          [_Resolvable_][] nor contains any [_Resolvable_]s.

        - Implementations MUST throw [_ResolverThrowable_][] if the object
          cannot be resolved.

    - Notes:

        - **Resolve recursively as needed.** Some implementations may return
          arrays or objects; the implementation might need to check their
          contents for other [_Resolvable_]s so that the return value is
          fully and deeply resolved.

### _ResolverThrowable_

[_ResolverThrowable_][] extends [_Throwable_][] to mark an [_Exception_][] as
resolver-related.

It adds no class members.

## Implementations

- Directives:

    - Implementations MAY define additional class members not defined in these
      interfaces.

- Notes:

    - **Reference implementations** may be found at
      <https://github.com/resolver-interop/impl>.

## Q & A

### Why do the resolver interfaces take an [_IocContainer_][] on every call instead of holding one as state?

Unlike most of the surveyed PHP DI/IoC projects, which internalise the
container as instance state, the resolver interfaces pass the container
explicitly on every call. Implementations are therefore stateless with
respect to any specific container and may be reused across multiple
containers — a single resolver shared by a test container and a
production container, for example.

Implementations that prefer the internalised pattern can wrap a
resolver and inject their own container, but the interfaces themselves
do not require it.

### Why don't the resolver interfaces specify an autowiring mode?

The surveyed PHP DI/IoC projects vary across four modes — Always
(every parameter is autowired by default), Opt-Out (autowiring on,
with an attribute or flag to skip), Opt-In (autowiring off, with an
attribute or flag to enable), and Never (no autowiring). Rather than
pick one mode and exclude the others, the interfaces specify only
what an implementation MUST do *when* it attempts to autowire; they
do not require any particular mode.

For example, an Opt-In implementation may decline to invoke
`resolveParameter()` for unannotated parameters; an Opt-Out
implementation may do the opposite. The reference implementation
uses Always mode.

### Why are class, callable, parameters-array, and type resolution defined as separate interfaces?

Each resolution operation has a distinct purpose:

- [_ClassResolver_][] — instantiate a class with autowired
  constructor parameters.
- [_CallResolver_][] — invoke a callable with autowired
  parameters and return the result.
- [_ReflectionParametersResolver_][] — resolve a list of
  [_ReflectionParameter_][]s into an arguments array.
- [_ReflectionTypeResolver_][] — reduce a [_ReflectionType_][]
  to a single class-name string.

Splitting them lets a consumer declare exactly the surface it
depends on. A router that only invokes controllers can type-hint
[_CallResolver_][] without claiming a dependency on class
instantiation; a container that only autowires classes can
implement [_ClassResolver_][] alone. Mocks for unit tests stub only
the methods of the narrow interface in use. Implementations may
implement all four (the reference implementation does) or only the
subset they support.

### Can the Reflection*Resolver interfaces also be implemented as PHP attributes?

Yes. Each of [_ReflectionParameterResolver_][],
[_ReflectionMethodResolver_][], and [_ReflectionPropertyResolver_][]
is designed so that an implementor can also declare it as a PHP
[_Attribute_][], letting consumers customise resolution per-target
by annotating the specific [_ReflectionParameter_][],
[_ReflectionMethod_][], or [_ReflectionProperty_][] they want to
customise.

Examples an implementor might ship:

- `#[GetEnv($name)]` — a parameter-resolver attribute that resolves
  a [_ReflectionParameter_][] to an environment value;
- `#[CallAfterConstruct]` — a method-resolver attribute that invokes
  a setup method on the instantiated object;
- `#[InjectService($name)]` — a property-resolver attribute that
  sets a property to a named container service.

Each interface specifies the lookup-and-dispatch behavior in its own
`resolve*()` directives.

### Why doesn't [_ReflectionMethodResolver_][] support with-clone setter injection?

A setter that returns a modified clone (e.g., a `with*()` method
returning `static`) is a real PHP pattern — PSR-7, `DateTimeImmutable`,
and fluent APIs generally. An earlier draft of this interface passed
`$object` to `resolveMethod()` by reference so an implementation could
assign a returned clone back to the caller and thread it through
subsequent calls.

That signature was simplified to pass-by-value because no surveyed PHP
DI library implements with-clone setter injection — every surveyed
setter mutates in place — and to the best of our knowledge no major
DI/IoC container in any other language formalises it either. Setter-
style injection across DI ecosystems converges on void-returning,
in-place mutation.

Both [_ReflectionMethodResolver_][] and [_ReflectionPropertyResolver_][]
therefore pass `$object` by value: the resolver invokes a method (or
sets a property) for its side effects on the existing instance and
discards any return value. Callers that need with-clone fluency should
orchestrate the chain outside the resolver.

* * *

[_Attribute_]: https://php.net/Attribute
[_CallResolver_]: #callresolver
[_ClassResolver_]: #classresolver
[_Exception_]: https://php.net/Exception
[_IocContainer_]: https://github.com/ioc-interop/interface/#ioccontainer
[_ReflectionIntersectionType_]: https://php.net/ReflectionIntersectionType
[_ReflectionMethod_]: https://php.net/ReflectionMethod
[_ReflectionMethodResolver_]: #reflectionmethodresolver
[_ReflectionNamedType_]: https://php.net/ReflectionNamedType
[_ReflectionParameter_]: https://php.net/ReflectionParameter
[_ReflectionParameterResolver_]: #reflectionparameterresolver
[_ReflectionParametersResolver_]: #reflectionparametersresolver
[_ReflectionProperty_]: https://php.net/ReflectionProperty
[_ReflectionPropertyResolver_]: #reflectionpropertyresolver
[_ReflectionType_]: https://php.net/ReflectionType
[_ReflectionTypeResolver_]: #reflectiontyperesolver
[_ReflectionUnionType_]: https://php.net/ReflectionUnionType
[_Resolvable_]: #resolvable
[_ResolverThrowable_]: #resolverthrowable
[_Throwable_]: https://php.net/Throwable
[BCP 14]: https://www.rfc-editor.org/info/bcp14
[README-RESEARCH.md]: ./README-RESEARCH.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
