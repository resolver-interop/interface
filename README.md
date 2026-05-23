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

- [_ClassResolver_][] affords resolving a class to a new instance, and indicating whether a class may be resolved at all.

- [_ReflectionParametersResolver_][] affords resolving an array of [_ReflectionParameter_][] instances into an array of arguments.

- [_ReflectionParameterResolver_][] affords resolving a [_ReflectionParameter_][] to an argument value.

- [_ReflectionTypeResolver_][] affords resolving a [_ReflectionType_][] to a `string` name, or `null` if it cannot be resolved.

- [_ReflectionMethodsResolver_][] affords instantiating and invoking [_ReflectionMethodResolver_][] attributes on object methods.

- [_ReflectionMethodResolver_][] affords method injection when implemented on a method-targeted [_Attribute_][].

- [_ReflectionPropertiesResolver_][] affords instantiating and invoking [_ReflectionPropertyResolver_][] attributes on object properties.

- [_ReflectionPropertyResolver_][] affords property injection when implemented on a property-targeted [_Attribute_][].

- [_CallResolver_][] affords resolving a callable's parameters and invoking it, returning the call's result.

- [_Resolvable_][] affords an implementing object the ability to resolve itself to a value.

- [_ResolverThrowable_][] extends [_Throwable_][] to mark an [_Exception_][] as resolver-related.

### _ClassResolver_

[_ClassResolver_][] affords resolving a class to a new instance, and
indicating whether a class may be resolved at all.

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
          [_ReflectionParametersResolver_][].

        - Implementations MAY support property injection using logic
          identical to that specified by [_ReflectionPropertiesResolver_][].

        - Implementations MAY support method injection using logic
          identical to that specified by [_ReflectionMethodsResolver_][].

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
          `ReflectionClass::isInstantiable()`, or apply stricter rules;
          e.g., requiring a registered service-name alias, rejecting
          abstract bases that lack a concrete binding, or excluding
          PHP-internal classes. Consumers should treat a `true` result
          as "the implementation is willing to try `resolveClass()`",
          not as a guarantee that resolution will succeed.

### _ReflectionParametersResolver_

[_ReflectionParametersResolver_][] affords resolving an array of
[_ReflectionParameter_][] instances into an array of arguments.

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
          by name in the `$arguments`.

        - Implementations MUST `resolve()` every [_Resolvable_][] at the top
          level of the `$arguments` array.

    - Notes:

        - **Mixed name and position keys for the same parameter in the
          `$arguments` pass through unchanged.** Named and positional
          keys referring to the same parameters are preseved in the
          returned array as-is.

        - **Extra and out-of-range keys in `$arguments` pass through
          unchanged.** Keys that do not correspond to any parameter name
          or position, and positional keys whose integer index exceeds the
          parameter count, are preserved in the returned array as-is.

        - **Resolved parameters are retained by name.** Pre-filled
          positional arguments retain their integer keys; newly-resolved
          parameters are keyed on their parameter name, not their position.

        - **Resolve all `$arguments`.** Callers might pass one or more
          [_Resolvable_][] as an argument; the implementation has to resolve
          them before returning.

### _ReflectionParameterResolver_

[_ReflectionParameterResolver_][] affords resolving a
[_ReflectionParameter_][] to an argument value.

This interface is also suitable for implementation on an [_Attribute_][] to
resolve a custom argument on a parameter.

- Directives:

    - An [_Attribute_][] implementing this interface MUST NOT be declared
      with `Attribute::IS_REPEATABLE`.

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

            - If the `$parameter` has one or more [_Attribute_][] that implements
              [_ReflectionParameterResolver_][], implementations MUST resolve
              the `$parameter` using only the first such [_Attribute_][].

            - Otherwise, if the `$parameter` type is resolvable using logic
              identical to [_ReflectionTypeResolver_][] **and**
              `$ioc->hasService()` returns `true` for that resolved type,
              implementations MUST resolve the `$parameter` to that service
              via `$ioc->getService()`.

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

### _ReflectionTypeResolver_

[_ReflectionTypeResolver_][] affords resolving a [_ReflectionType_][] to a
`string` name, or `null` if it cannot be resolved.

#### _ReflectionTypeResolver_ Methods

- ```php
  public function resolveType(
      IocInterop\Interface\IocContainer $ioc,
      ?ReflectionType $type,
  ) : ?string;
  ```
    - Resolves a [_ReflectionType_][] to a `string` name, or `null` if it
    cannot be resolved.

    - Directives:

        - If `$type` is `null`, implementations MUST return `null`.

        - Otherwise, if `$type` is a [_ReflectionNamedType_][],
          implementations MUST return the type name as produced by
          its `getName()` method.

        - Otherwise, the logic for determining the return type name is
          implementation-defined.

    - Notes:

        - **Compound and other type handling is left to the implementation.**
          For example, [_ReflectionUnionType_][] and
          [_ReflectionIntersectionType_][] handling may vary between
          implementations: one might iterate the branches and return the
          first whose name corresponds to an `$ioc` service name; another
          might return the compound stringification (e.g. `"Foo|Bar"`) and
          let the container lookup handle it; yet another might return `null`
          out of hand.

### _ReflectionMethodsResolver_

[_ReflectionMethodsResolver_][] affords instantiating and invoking
[_ReflectionMethodResolver_][] attributes on object methods.

#### _ReflectionMethodsResolver_ Methods

- ```php
  public function resolveMethods(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionMethod[] $methods,
      object $object,
  ) : void;
  ```
    - Invokes the attributed `$methods` on the `$object`.

    - Directives:

        - For each [_ReflectionMethod_][] in `$methods` that carries one
          or more [_Attribute_][] implementing [_ReflectionMethodResolver_][],
          implementations MUST invoke only the first such [_Attribute_][].

        - Implementations MUST NOT invoke un-attributed methods.

        - Implementations MUST throw [_ResolverThrowable_][] if resolution
          fails.

### _ReflectionMethodResolver_

[_ReflectionMethodResolver_][] affords method injection when implemented
on a method-targeted [_Attribute_][].

- Directives:

    - An [_Attribute_][] implementing this interface MUST NOT be declared
      with `Attribute::IS_REPEATABLE`.

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
          identical to that specified by [_ReflectionParametersResolver_][].

        - Implementations MUST invoke `$method` on `$object` with the
          resolved arguments.

        - Implementations MUST throw [_ResolverThrowable_][] if resolution
          fails.

### _ReflectionPropertiesResolver_

[_ReflectionPropertiesResolver_][] affords instantiating and
invoking [_ReflectionPropertyResolver_][] attributes on object properties.

#### _ReflectionPropertiesResolver_ Methods

- ```php
  public function resolveProperties(
      IocInterop\Interface\IocContainer $ioc,
      ReflectionProperty[] $properties,
      object $object,
  ) : void;
  ```
    - Resolves the attributed `$properties` on the `$object`.

    - Directives:

        - For each [_ReflectionProperty_][] in `$properties` that carries
          one or more [_Attribute_][] implementing
          [_ReflectionPropertyResolver_][], implementations MUST resolve
          the property using only the first such [_Attribute_][].

        - Implementations MUST leave un-attributed properties unchanged.

        - Implementations MUST throw [_ResolverThrowable_][] if any
          attempted resolution fails.

### _ReflectionPropertyResolver_

[_ReflectionPropertyResolver_][] affords property injection when implemented
on a property-targeted [_Attribute_][].

- Directives:

    - An [_Attribute_][] implementing this interface MUST NOT be declared
      with `Attribute::IS_REPEATABLE`.

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

        - Implementations MUST resolve the `$property` in this order:

            - Implementations MAY attempt to resolve the `$property` using
              implementation-specific logic; such logic is not defined
              herein.

            - Otherwise, implementations MUST resolve the `$property` to the
              [_IocContainer_][] service whose name matches the property
              type as produced by [_ReflectionTypeResolver_][], via
              `$ioc->getService()`.

        - Implementations MUST throw [_ResolverThrowable_][] if resolution
          of `$property` is attempted and fails.

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

        - Implementations MUST resolve the `$callable`'s parameters using
          logic identical to that specified by
          [_ReflectionParametersResolver_][]'s `resolveParameters()`,
          including the `$arguments` pre-fill and [_Resolvable_][]-unwrap
          semantics specified there.

        - Implementations MUST invoke the `$callable` with the resolved
          `$arguments` and MUST return the result of that invocation.

        - Implementations MUST throw [_ResolverThrowable_][] if the
          `$callable` cannot be resolved.

### _Resolvable_

[_Resolvable_][] affords an implementing object the ability to resolve itself
to a value.

- Notes:

    - **`Resolvable` defers container calls until the moment of resolution.**
      Wrapping a container call in a `Resolvable` lets the implmenting object
      be pass around without forcing the container interation at construction
      time.

#### _Resolvable_ Methods

- ```php
  public function resolve(IocInterop\Interface\IocContainer $ioc) : mixed;
  ```
    - Resolves the implementing object to a value.

    - Directives:

        - Implementations MUST return a value that is neither itself a
          [_Resolvable_][] nor contains any [_Resolvable_] instances.

        - Implementations MUST throw [_ResolverThrowable_][] if the object
          cannot be resolved.

    - Notes:

        - **Resolve recursively as needed.** Some implementations may return
          arrays or objects; the implementation might need to check their
          contents for other [_Resolvable_] instances so that the return value is
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

### Why are there so many interfaces?

The researched projects typically afford autowiring through a
single container class with several methods (`get()`, `make()`,
`call()`, and so on), each performing a distinct resolution task.

Resolver-Interop opts to split those operations into separate
interfaces. Doing so allows consumers to depend only on the
specific methods they need, and implementors to combine them
as they see fit.

### Why are setter-injection and property-injection supported?

Constructor injection is the documented default across all the researched
projects. Setter-injection is generally supported, whereas property-injection
is far less common.

Resolver-Interop summarizes this guidance evident from the projects:

- constructor injection is preferred above all;

- but sometimes setter-injection is unavoidable, especially in legacy or
  refactoring scenarios;

- and while property-injection is to be shunned prejudicially, having it
  available as an absolute last resort can be useful.

Thus, while Resolver-Interop does not forbid post-construction injection, it
attempts to make explicit the appropriate scope for these alternative forms of
injection.

* * *

[_Attribute_]: https://php.net/Attribute
[_CallResolver_]: #callresolver
[_ClassResolver_]: #classresolver
[_Exception_]: https://php.net/Exception
[_IocContainer_]: https://github.com/ioc-interop/interface/#ioccontainer
[_ReflectionIntersectionType_]: https://php.net/ReflectionIntersectionType
[_ReflectionMethod_]: https://php.net/ReflectionMethod
[_ReflectionMethodResolver_]: #reflectionmethodresolver
[_ReflectionMethodsResolver_]: #reflectionmethodsresolver
[_ReflectionNamedType_]: https://php.net/ReflectionNamedType
[_ReflectionParameter_]: https://php.net/ReflectionParameter
[_ReflectionParameterResolver_]: #reflectionparameterresolver
[_ReflectionParametersResolver_]: #reflectionparametersresolver
[_ReflectionProperty_]: https://php.net/ReflectionProperty
[_ReflectionPropertiesResolver_]: #reflectionpropertiesresolver
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
