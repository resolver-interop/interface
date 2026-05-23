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
