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
