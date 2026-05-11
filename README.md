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

- [_ResolverService_][] affords resolving classes, calls, parameter arrays, and types.

- [_ReflectionParameterResolver_][] affords resolving a [_ReflectionParameter_][] to an argument value.

- [_ReflectionMethodResolver_][] affords invoking a method on an object.

- [_ReflectionPropertyResolver_][] affords setting a property on an object.

- [_Resolvable_][] affords an implementing object resolving itself to a value.

- [_ResolverThrowable_][] extends [_Throwable_][] to mark an [_Exception_][] as resolver-related.

### _ResolverService_

[_ResolverService_][] affords resolving classes, calls, parameter arrays, and
types.

- Notes:

    - **The [_IocContainer_][] first-parameter pattern is intentional.**
      Unlike most of the surveyed PHP DI/IoC projects, which internalise
      the container as instance state, this interface passes the
      container explicitly on every call. Implementations are therefore
      stateless with respect to a specific container and may be reused
      across multiple containers (a single resolver shared by a test
      container and a production container, for example). Implementations
      that prefer the internalised pattern can wrap a `ResolverService`
      and inject their own container, but the interface itself does not
      require it.

    - **Autowiring mode is implementation-defined.** The surveyed PHP
      DI/IoC projects vary across four modes (Always, Opt-Out, Opt-In,
      Never). The directives on `resolveClass()`, `resolveCall()`, and
      `resolveParameter()` describe what an implementation MUST do
      *when* it attempts autowiring; they do not require any particular
      mode. An Opt-In implementation may decline to invoke
      `resolveParameter()` for unannotated parameters; an Opt-Out
      implementation may do the opposite. The reference implementation
      uses Always mode.

    - **Parameter resolution is structurally required; property and method
      resolution are not.** This interface extends
      [_ReflectionParameterResolver_][] because every `ResolverService`
      method that constructs or invokes something MUST perform parameter
      injection. Property and method resolution, by contrast, are MAY
      directives on `resolveClass()`: implementations advertise support
      by separately implementing [_ReflectionPropertyResolver_][] and
      [_ReflectionMethodResolver_][], which consumers detect with an
      `instanceof` check. The interface hierarchy mirrors that MUST/MAY
      split rather than forcing every implementation to ship no-op
      property and method resolvers.

#### _ResolverService_ Methods

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
          using logic equivalent to that specified by `resolveParameters()`,
          including the `$arguments` pre-fill and [_Resolvable_][]-unwrap
          semantics specified there.

        - Implementations MAY support [_ReflectionPropertyResolver_][]
          attributes on the instantiated `$class` properties.

        - Implementations MAY support [_ReflectionMethodResolver_][]
          attributes on the instantiated `$class` methods.

        - Implementations MAY support other forms of injection not specified
          herein.

        - Implementations MUST throw [_ResolverThrowable_][] if the `$class`
          cannot be resolved.

- ```php
  public function isResolvableClass(string $class) : bool;
  ```
    - Does the `$class` exist, and is it instantiable?

    - Notes:

        - **The logic for this method is expressly unspecified.**
          Implementations may check `class_exists` and
          `ReflectionClass::isInstantiable()`, or apply stricter rules —
          e.g., requiring a registered service-name alias, rejecting
          abstract bases that lack a concrete binding, or excluding
          PHP-internal classes. Consumers should treat a `true` result
          as "the implementation is willing to try `resolveClass()`",
          not as a guarantee that resolution will succeed.

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
          logic equivalent to that specified by `resolveParameters()`,
          including the `$arguments` pre-fill and [_Resolvable_][]-unwrap
          semantics specified there.

        - Implementations MUST throw [_ResolverThrowable_][] if the
          `$callable` cannot be resolved.

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
          implementations MUST do so using logic equivalent to that specified
          by [_ReflectionParameterResolver_][].

        - Implementations MUST retain each resolved [_ReflectionParameter_][]
          by name in the `$arguments`. Pre-filled positional arguments
          retain their integer keys; only resolver-produced arguments are
          stored by name.

        - After resolving all [_ReflectionParameter_][]s, implementations
          MUST unwrap any [_Resolvable_][] value in `$arguments` by
          calling its `resolve()` method, repeating until no
          [_Resolvable_][] remains.

- ```php
  public function resolveType(
      IocInterop\Interface\IocContainer $ioc,
      ?ReflectionType $type,
  ) : ?string;
  ```
    - Resolves a [_ReflectionType_][] to a string, or `null` if it cannot
    be resolved.

    - Directives:

        - For a [_ReflectionNamedType_][], implementations MUST return
          the type name as produced by `ReflectionNamedType::getName()`.

        - For a [_ReflectionUnionType_][] or
          [_ReflectionIntersectionType_][], implementations MAY return
          the name of any branch type — typically one whose name
          corresponds to a container service. Implementations that
          decline to inspect compound types MUST return `null`.

        - If `$type` is `null`, implementations MUST return `null`.

### _ReflectionParameterResolver_

[_ReflectionParameterResolver_][] affords resolving a
[_ReflectionParameter_][] to an argument value.

- Notes:

    - **This interface can be implemented as an attribute.** Doing so allows
      implementors to define custom resolution approaches for consumers to
      apply to specific [_ReflectionParameter_][]s. For example, implementors
      may declare a `#[GetEnv($name)]` attribute to resolve the
      [_ReflectionParameter_][] to an environment value.

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
              equivalent to the [_ResolverService_][] method
              `resolveType()` and the container has a service for that type,
              implementations MUST resolve the `$parameter` to that service.

            - Otherwise, implementations MAY attempt to resolve the
              `$parameter` using implementation-specific logic; such logic is
              not defined herein.

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

### _ReflectionMethodResolver_

[_ReflectionMethodResolver_][] affords invoking a method on an object.

- Notes:

    - **This interface can be implemented as an attribute.** Doing so allows
      implementors to define custom resolution approaches for consumers to
      apply to specific [_ReflectionMethod_][]s.

#### _ReflectionMethodResolver_ Methods

- ```php
  public function resolveMethod(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionMethod $method,
      object &$object,
  ) : void;
  ```
    - Invokes the [_ReflectionMethod_][] on the `$object`.

    - Directives:

        - Implementations MUST support parameter injection using logic
          equivalent to that specified by the [_ResolverService_][] method
          `resolveParameters()`.

        - Implementations MUST throw [_ResolverThrowable_][] if resolution
          of `$method` is attempted and fails. Orchestrating
          implementations (those that iterate over the methods of a class
          looking for [_ReflectionMethodResolver_][] attributes) MAY skip
          methods with no applicable attribute; the MUST-throw rule
          applies when resolution is invoked, not when the orchestrator
          declines to invoke it.

        - Implementations MAY assign a replacement object to `$object` to
          support immutable update patterns (e.g., invoking a `with*()`
          method that returns a modified clone).

### _ReflectionPropertyResolver_

[_ReflectionPropertyResolver_][] affords setting a property on an object.

- Notes:

    - **This interface can be implemented as an attribute.** Doing so allows
      implementors to define custom resolution approaches for consumers to
      apply to specific [_ReflectionProperty_][]s.

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

        - Implementations MUST throw [_ResolverThrowable_][] if resolution
          of `$property` is attempted and fails. Orchestrating
          implementations (those that iterate over the properties of a
          class looking for [_ReflectionPropertyResolver_][] attributes)
          MAY skip properties with no applicable attribute; the MUST-throw
          rule applies when resolution is invoked, not when the
          orchestrator declines to invoke it.

### _Resolvable_

[_Resolvable_][] affords an implementing object resolving itself to a
value.

- Notes:

    - **Use `Resolvable` to defer container calls.** Wrapping a
      container lookup in a `Resolvable` lets callers pass it in
      `$arguments` without forcing the lookup at construction time;
      the resolver invokes `resolve()` only at the moment of
      parameter resolution.

#### _Resolvable_ Methods

- ```php
  public function resolve(IocInterop\Interface\IocContainer $ioc) : mixed;
  ```
    - Resolves the implementing object to a value.

    - Directives:

        - Implementations MUST throw [_ResolverThrowable_][] if the object
          cannot be resolved.

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

* * *

[_Attribute_]: https://php.net/Attribute
[_Exception_]: https://php.net/Exception
[_IocContainer_]: https://github.com/ioc-interop/interface/#ioccontainer
[_ReflectionIntersectionType_]: https://php.net/ReflectionIntersectionType
[_ReflectionMethod_]: https://php.net/ReflectionMethod
[_ReflectionMethodResolver_]: #reflectionmethodresolver
[_ReflectionNamedType_]: https://php.net/ReflectionNamedType
[_ReflectionParameter_]: https://php.net/ReflectionParameter
[_ReflectionParameterResolver_]: #reflectionparameterresolver
[_ReflectionProperty_]: https://php.net/ReflectionProperty
[_ReflectionPropertyResolver_]: #reflectionpropertyresolver
[_ReflectionType_]: https://php.net/ReflectionType
[_ReflectionUnionType_]: https://php.net/ReflectionUnionType
[_Resolvable_]: #resolvable
[_ResolverService_]: #resolverservice
[_ResolverThrowable_]: #resolverthrowable
[_Throwable_]: https://php.net/Throwable
[BCP 14]: https://www.rfc-editor.org/info/bcp14
[README-RESEARCH.md]: ./README-RESEARCH.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
