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

- [_ResolverService_][] affords resolving a class name to a new instance of that class.

- [_ReflectionParametersResolver_][] affords resolving an array of [_ReflectionParameter_][]s to an array of named arguments.

- [_ReflectionParameterResolver_][] affords resolving a [_ReflectionParameter_][] to an argument value.

- [_ReflectionMethodResolver_][] affords invoking a method on an object.

- [_ReflectionPropertyResolver_][] affords setting a property on an object.

- [_CallResolverService_][] affords invoking a callable with resolved [_ReflectionParameter_][]s and argument overrides.

- [_InvokableResolver_][] affords resolving the implementing object to a value.

- [_ResolverThrowable_][] extends [_Throwable_][] to mark an [_Exception_][] as resolver-related. It adds no class members.

### _ResolverService_

[_ResolverService_][] affords resolving a class name to a new instance of
that class.

#### _ResolverService_ Methods

- ```php
  public function resolve(
      IocInterop\Interface\IocContainer $ioc,
      class-string $class,
      mixed[] $arguments = [],
  ) : T;
  ```
    - Returns a new instance of the `$class` with constructor `$arguments`.

    - Directives:

        - Implementations MUST support constructor injection using logic
          equivalent to that specified by [_ReflectionParametersResolver_][].

        - Implementations MAY support [_ReflectionPropertyResolver_][]
          attributes on the instantiated class properties.

        - Implementations MAY support [_ReflectionMethodResolver_][]
          attributes on the instantiated class methods.

        - Implementations MAY support other forms of injection not specified
          herein.

        - Implementations MUST throw [_ResolverThrowable_][] if the `$class`
          cannot be resolved.

- ```php
  public function isResolvable(string $class) : bool;
  ```
    - Does the `$class` exist, and is it instantiable?

### _ReflectionParametersResolver_

[_ReflectionParametersResolver_][] affords resolving an array of
[_ReflectionParameter_][]s to an array of named arguments.

#### _ReflectionParametersResolver_ Methods

- ```php
  public function resolveParameters(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionParameter[] $parameters,
      mixed[] $arguments = [],
  ) : mixed[];
  ```
    - Resolves an array of [_ReflectionParameter_][]s to an array of named
    arguments, allowing for an array of override arguments.

    - Directives:

        - Implementations MUST NOT attempt to resolve a
          [_ReflectionParameter_][] that already exists by name as an
          argument.

        - When resolving a [_ReflectionParameter_][] to an argument,
          implementations MUST do so using logic equivalent to that specified
          by [_ReflectionParameterResolver_][].

        - Implementations MUST resolve all [_InvokableResolver_][] objects in
          the `$arguments`.

        - Implementations MUST return an array of arguments keyed by the
          [_ReflectionParameter_][] names.

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
              the `$parameter` using that attribute.

            - Otherwise, if the `$parameter` type is a
              [_ReflectionNamedType_][], and the container has a service for
              that type, implementations MUST resolve the `$parameter` to
              that service.

            - Otherwise, implementations MAY attempt to resolve the
              `$parameter` using implementation-specific logic; such logic is
              not defined herein.

            - Otherwise, if the `$parameter` has a default value,
              implementations MUST resolve the `$parameter` to that value.

        - Implementations MUST throw [_ResolverThrowable_][] if the
          `$parameter` cannot be resolved.

### _ReflectionMethodResolver_

[_ReflectionMethodResolver_][] affords invoking a method on an object.

- Notes:

    - **TBD** Marks a method for setter injection or post-instantiation
      invocation.

    - **This interface can be implemented as an attribute.** Doing so allows
      implementors to define custom resolution approaches for consumers to
      apply to specific [_ReflectionMethod_][]s.

#### _ReflectionMethodResolver_ Methods

- ```php
  public function resolveMethod(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionMethod $method,
      object $object,
  ) : object;
  ```
    - Invokes the [_ReflectionMethod_][] on the `$object`, returning either the
    `$object` itself or a replacement object.

    - Directives:

        - Implementations MUST support parameter injection using logic
          equivalent to that specified by [_ReflectionParametersResolver_][].

        - Implementations MUST throw [_ResolverThrowable_][] if the
          `$method` cannot be resolved.

    - Notes:

        - **TBD** Implement on `TARGET_METHOD` attributes for setter or
          immutable injection.

### _ReflectionPropertyResolver_

[_ReflectionPropertyResolver_][] affords setting a property on an object.

- Notes:

    - **TBD** Marks a property for injection.

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

        - Implementations MUST throw [_ResolverThrowable_][] if the
          `$property` cannot be resolved.

### _CallResolverService_

[_CallResolverService_][] affords invoking a callable with resolved
[_ReflectionParameter_][]s and argument overrides.

#### _CallResolverService_ Methods

- ```php
  public function resolveCall(
      IocInterop\Interface\IocContainer $ioc,
      callable $callable,
      mixed[] $arguments = [],
  ) : mixed;
  ```
    - Invokes the `$callable` with `$arguments` overrides, and returns the
    result.

    - Directives:

        - Implementations MUST resolve the `$callable`
          [_ReflectionParameter_][]s and `$arguments` using logic equivalent
          to that specified by [_ReflectionParametersResolver_][].

        - Implementations MUST throw [_ResolverThrowable_][] if the callable
          [_ReflectionParameter_][]s or arguments cannot be resolved.

### _InvokableResolver_

[_InvokableResolver_][] affords resolving the implementing object to a value.

- Notes:

    - **TBD** Use e.g. to defer container calls (i.e. Lazy), then
      can use in $arguments without actually creating anything until the
      moment of resolution.

#### _InvokableResolver_ Methods

- ```php
  public function __invoke(IocInterop\Interface\IocContainer $ioc) : mixed;
  ```
    - Resolves the implementing object to a value.

    - Directives:

        - Implementations MUST throw [_ResolverThrowable_][] if the object
          cannot be resolved.

### _ResolverThrowable_

[_ResolverThrowable_][] extends [_Throwable_][] to mark an [_Exception_][] as
resolver-related. It adds no class members.

## Implementations

- Directives:

    - Implementations MAY define additional class members not defined in these
      interfaces.

- Notes:

    - **Reference implementations** may be found at
      <https://github.com/Resolver-Interop/impl>.

## Q & A

* * *

[_InvokableResolver_]: #invokableresolver
[_CallResolverService_]: #callresolverservice
[_ResolverService_]: #resolverservice
[_Exception_]: https://php.net/Exception
[_IocContainer_]: https://github.com/ioc-interop/interface/#ioccontainer
[_ReflectionParameterResolver_]: #reflectionparameterresolver
[_ReflectionParametersResolver_]: #reflectionparametersresolver
[_ReflectionPropertyResolver_]: #reflectionpropertyresolver
[_ReflectionMethodResolver_]: #reflectionmethodresolver
[_ResolverThrowable_]: #resolverthrowable
[_Throwable_]: https://php.net/Throwable
[BCP 14]: https://www.rfc-editor.org/info/bcp14
[README-RESEARCH.md]: ./README-RESEARCH.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
[_Attribute_]: https://php.net/Attribute
[_ReflectionNamedType_]: https://php.net/ReflectionNamedType
[_ReflectionParameter_]: https://php.net/ReflectionParameter
[_ReflectionProperty_]: https://php.net/ReflectionProperty
[_ReflectionMethod_]: https://php.net/ReflectionMethod
