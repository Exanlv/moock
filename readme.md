# Moock
<p align="center">
    <img src="moock.png" alt="Moock logo"><br>
    A simple way of mocking objects in PHP
</p>

## Installation

```sh
composer require exan/moock
```

## About

Moock is a package to abstract creating test dummies for objects, intended to be used in unit tests.
Using test dummies allows you to write more specific tests, where you don't have to worry about a class's dependencies.
This works best when using the Dependency Injection pattern.

Check out the docs [here!](./documentation.md)

### Sales pitch

If you're looking into this library, there's a good chance you already know of some other mocking library. (If not, see the [new to mocking](./new-to-mocking.md) introduction)
For Moock, the goal is to rely on PHP language tricks as much as possible for the syntax.

Take for example the mocking of methods:

```php
/** @var MyClass */
$myMock;

Mock::method($myMock->someMethod(...));
```

This makes it so IDE's don't (or shouldn't) need specific extensions to get nice auto-complete, or to support refactoring method names.

If you go ahead and rename `someMethod` on `MyClass`, your IDE will properly recognize it in your creation of mocks, and thus also rename it there.

Compare this to a fictional example:

```php
/** @var MyClass */
$myMock;

$myMock->mockMethod('someMethod');
```

To get an IDE to automatically refactor `someMethod`, your IDE needs to be aware of PHP's syntax rules _and_ the syntax of the package.
Rather than only needing to know PHP syntax.

This can be achieved by having extensions specific to your IDE & mocking library of choice, of course.
Relying on these specific types of extensions however, is not my personal preference.

### Conscious omissions

There are some features you may take for granted in other libraries, including but not limited to:

- Overloading
- Mocking protected/private methods
- Mocking static methods

These are (opinionated) conscious omissions.
These features can lead you down a path of hard to maintain tests, or tests which don't meaningfully test your application.

If you are missing a feature, please consider the above. If you don't think it applies, please create an issue with your request.
