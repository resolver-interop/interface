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

- [_Resolvable_][] affords allowing the implementing object to resolve itself to a value.

- [_ResolverThrowable_][] extends [_Throwable_][] to mark an [_Exception_][] as resolver-related.

### _ResolverService_

[_ResolverService_][] affords resolving classes, calls, parameter arrays, and
types.

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

        - Implementations MUST support parameter injection on the `$class`
          constructor using logic equivalent to that specified by
          `resolveParameters()`.

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

- ```php
  public function resolveCall(
      IocInterop\Interface\IocContainer $ioc,
      callable $callable,
      mixed[] $arguments = [],
  ) : mixed;
  ```
    - Resolves the `$callable` to return its result.

    - Directives:

        - Implementations MUST support parameter injection on the `$callable`
          using logic equivalent to that specified by `resolveParameters()`.

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
          by name in the `$arguments`.

        - Implementations MUST resolve all [_Resolvable_][] objects in
          the `$arguments`.

- ```php
  public function resolveType(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionType $type,
  ) : ?string;
  ```
    - Resolves a [_ReflectionType_][] to a string, or `null` if it cannot be
    be resolved.

    - Notes:

        - **TBD** Typically only for named types, but may help to convert
          union and intersection types to a single named type.

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

            - Otherwise, if the `$parameter` type is resolvable using logic
              equivalent to the [_ReflectionService_][] method
              `resolveType()` and the container has a service for that type,
              implementations MUST resolve the `$parameter` to that service.

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

    - **This interface can be implemented as an attribute.** Doing so allows
      implementors to define custom resolution approaches for consumers to
      apply to specific [_ReflectionMethod_][]s.

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

        - Implementations MUST support parameter injection using logic
          equivalent to that specified by [_ReflectionParametersResolver_][].

        - Implementations MUST throw [_ResolverThrowable_][] if the
          `$method` cannot be resolved.

    - Notes:

        - **TBD** $object is by reference so you can set to a replacement
          object a la immutability.

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

        - Implementations MUST throw [_ResolverThrowable_][] if the
          `$property` cannot be resolved.

### _Resolvable_

[_Resolvable_][] affords allowing the implementing object to resolve
itself to a value.

- Notes:

    - **TBD** Use to defer container calls (i.e. lazy salls), then
      can use in $arguments without actually creating anything until the
      moment of resolution.

#### _Resolvable_ Methods

- ```php
  public function resolve(IocInterop\Interface\IocContainer $ioc) : mixed;
  ```
    - Resolves the implementing object to a value.

    - Directives:

        - Implementations MUST throw [_ResolverThrowable_][] if the object
          cannot be resolved.

        - **TBD** Must recursively resolve all Resolvable in the resolved
          value.

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
[_CallResolverService_]: #callresolverservice
[_Exception_]: https://php.net/Exception
[_IocContainer_]: https://github.com/ioc-interop/interface/#ioccontainer
[_ReflectionMethod_]: https://php.net/ReflectionMethod
[_ReflectionMethodResolver_]: #reflectionmethodresolver
[_ReflectionParameter_]: https://php.net/ReflectionParameter
[_ReflectionParameterResolver_]: #reflectionparameterresolver
[_ReflectionParametersResolver_]: #reflectionparametersresolver
[_ReflectionProperty_]: https://php.net/ReflectionProperty
[_ReflectionPropertyResolver_]: #reflectionpropertyresolver
[_ReflectionType_]: https://php.net/ReflectionType
[_Resolvable_]: #Resolvable
[_ResolverService_]: #resolverservice
[_ResolverThrowable_]: #resolverthrowable
[_Throwable_]: https://php.net/Throwable
[BCP 14]: https://www.rfc-editor.org/info/bcp14
[README-RESEARCH.md]: ./README-RESEARCH.md
[RFC 2119]: https://datatracker.ietf.org/doc/html/rfc2119
[RFC 8174]: https://datatracker.ietf.org/doc/html/rfc8174
