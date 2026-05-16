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

{{= list }}

{{= docs }}

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
