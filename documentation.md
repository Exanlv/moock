# Table of contents

 - [Creating a mock](#creating-a-mock)
     - [Mocking a class](#mocking-a-class)
     - [Mocking an interface](#mocking-an-interface)
     - [Mocking several interfaces](#mocking-several-interfaces)
 - [Replacing methods](#replacing-methods)
 - [Asserting expectations](#asserting-expectations)
     - [Asserting amount of calls](#asserting-amount-of-calls)
     - [Asserting method was called with specific input](#asserting-method-was-called-with-specific-input)
     - [Built-in helpers](#built-in-helpers)
 - [Order expectations](#order-expectations)
     - [Verifying order of calls](#verifying-order-of-calls)
     - [Verifying order and arguments](#verifying-order-and-arguments)
     - [Verifying order between different mocks](#verifying-order-between-different-mocks)
 - [Filtering method arguments](#filtering-method-arguments)
     - [Filtering an argument](#filtering-an-argument)
     - [Using closures](#using-closures)
 - [Creating partial mocks](#creating-partial-mocks)
 - [Compatibility](#compatibility)

## Creating a mock

_Note: all configuration of a mock is stored in `$mock`, the static `Mock::...` methods are there to provide the user-facing API._

### Mocking a class
```php
use Exan\Moock\Mock;

$mock = Mock::class(UserService::class);

$this->assertInstanceOf(UserService::class, $mock);
```

### Mocking an interface
```php
use Exan\Moock\Mock;

$mock = Mock::interface(UserServiceInterface::class);

$this->assertInstanceOf(UserServiceInterface::class, $mock);
```

### Mocking several interfaces
```php
use Exan\Moock\Mock;

$mock = Mock::interfaces(UserServiceInterface::class, TestInterface::class);

$this->assertInstanceOf(UserServiceInterface::class, $mock);
$this->assertInstanceOf(TestInterface::class, $mock);
```

---

## Replacing methods

### Replacing a method
You can replace any public method on your mocks using the following examples
```php
use Exan\Moock\Mock;

Mock::method($this->mock->userExists(...))->replace(fn (string $email) => $email === 'exists@mail.com');

$this->assertTrue($this->mock->userExists('exists@mail.com'));
$this->assertFalse($this->mock->userExists('doesnt@mail.com'));
```

Returning a static value
```php
use Exan\Moock\Mock;

Mock::method($this->mock->userExists(...))->forceReturn(true);

$this->assertTrue($this->mock->userExists('some-email@domain.com'));
```

Returning a sequence of values
```php
use Exan\Moock\Mock;

Mock::method($this->mock->userExists(...))->forceReturnSequence([true, true, false]);

$this->assertTrue($this->mock->userExists('some-email@domain.com'));
$this->assertTrue($this->mock->userExists('some-email@domain.com'));

// 3rd item is false
$this->assertFalse($this->mock->userExists('some-email@domain.com'));
```

Throwing an exception
```php
use Exan\Moock\Mock;
use RuntimeException;

Mock::method($this->mock->createUser(...))->throwsException(RuntimeException::class);

$this->expectException(RuntimeException::class);

$this->mock->createUser(
    'mail@domain.com',
    'username',
    'password123',
);
```

---

## Asserting expectations

These assertions work out of the box with both [PHPUnit](https://packagist.org/packages/phpunit/phpunit) and [Nette Tester](https://packagist.org/packages/nette/tester). If neither are installed, a regular PHP `assert` is used.

### Asserting amount of calls
Asserting the method not called at all.
```php
Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

Mock::method($this->mock->userExists(...))
    ->expect()
    ->not()
    ->toHaveBeenCalled();
```

Asserting the method was called at all.
```php
Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('my-email@domain.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->toHaveBeenCalled();
```

Asserting the method was called exactly once.
```php
Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('my-email@domain.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->toHaveBeenCalledOnce();
```

Asserting the method was not called exactly once.
```php
Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('my-email@domain.com');
$this->mock->userExists('my-email@domain.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->not()
    ->toHaveBeenCalledOnce();
```

Asserting the method was called _n_ times.
```php
Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('my-email@domain.com');
$this->mock->userExists('my-email@domain.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->toHaveBeenCalledTimes(2);
```

Asserting the method was not called _n_ times.
```php
Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('my-email@domain.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->not()
    ->toHaveBeenCalledTimes(2);
```

### Asserting method was called with specific input
You can assert a method was called with specific input by passing the expected arguments into `with()`.
```php
Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('my-email@domain.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->with('my-email@domain.com')
    ->toHaveBeenCalled();
```

This can of course also be reversed.
```php
Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('my-email@domain.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->not()
    ->with('other-email@domain.com')
    ->toHaveBeenCalled();
```

Rather than being tied to static values, you can pass a closure as well.
```php
Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('my-email@domain.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->with(fn (string $email) => str_ends_with($email, '@domain.com'))
    ->toHaveBeenCalled();
```

If you only care about a specific argument, you can use named arguments.
```php
$this->mock->createUser('my-email@domain.com', 'my-username', 'password');

Mock::method($this->mock->createUser(...))
    ->expect()
    ->with(email: 'my-email@domain.com', password: 'password')
    ->toHaveBeenCalled();
```

Of course, closures can be used here too.
```php
$this->mock->createUser('my-email@domain.com', 'my-username', 'password');

Mock::method($this->mock->createUser(...))
    ->expect()
    ->with(
        email: fn (string $email) => str_ends_with($email, '@domain.com'),
        password: 'password',
    )->toHaveBeenCalled();
```

### Built-in helpers
`string` must contain `@mail.com`
```php
use Exan\Moock\Args\Str;

Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('test@mail.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->with(Str::contains('@mail.com'))
    ->toHaveBeenCalled();
```

`string` must have specific length
```php
use Exan\Moock\Args\Str;

Mock::method($this->mock->userExists(...))
    ->forceReturn(true);

$this->mock->userExists('test@mail.com');

Mock::method($this->mock->userExists(...))
    ->expect()
    ->with(Str::length(13))
    ->toHaveBeenCalled();
```

`DateTimeInterface` must be before given time
```php
use Exan\Moock\Args\Date;
use DateTime;

Mock::method($this->mock->getUsersCreatedBefore(...))
    ->forceReturn([]);

$this->mock->getUsersCreatedBefore(new DateTime('2024-12-31'));

Mock::method($this->mock->getUsersCreatedBefore(...))
    ->expect()
    ->with(Date::before(new DateTime('2025-01-01 12:00:00')))
    ->toHaveBeenCalled();
```

`DateTimeInterface` must be after given time
```php
use Exan\Moock\Args\Date;
use DateTime;

Mock::method($this->mock->getUsersCreatedBefore(...))
    ->forceReturn([]);

$this->mock->getUsersCreatedBefore(new DateTime('2025-01-02'));

Mock::method($this->mock->getUsersCreatedBefore(...))
    ->expect()
    ->with(Date::after(new DateTime('2025-01-01 12:00:00')))
    ->toHaveBeenCalled();
```

`int|float` must be less than given number
```php
use Exan\Moock\Args\Number;

Mock::method($this->mock->getUsersByAge(...))
    ->forceReturn([]);

$this->mock->getUsersByAge(50);

Mock::method($this->mock->getUsersByAge(...))
    ->expect()
    ->with(Number::lt(100))
    ->toHaveBeenCalled();
```

`int|float` must be greater than given number
```php
use Exan\Moock\Args\Number;

Mock::method($this->mock->getUsersByAge(...))
    ->forceReturn([]);

$this->mock->getUsersByAge(75);

Mock::method($this->mock->getUsersByAge(...))
    ->expect()
    ->with(Number::gt(50))
    ->toHaveBeenCalled();
```

`int|float` must be within range
```php
use Exan\Moock\Args\Number;

Mock::method($this->mock->getUsersByAge(...))
    ->forceReturn([]);

$this->mock->getUsersByAge(15);

Mock::method($this->mock->getUsersByAge(...))
    ->expect()
    ->with(Number::range(10, 20))
    ->toHaveBeenCalled();
```

`array` must have given number of items
```php
use Exan\Moock\Args\Arr;

$this->mock->deleteUsersByEmail(['a','b','c']);

Mock::method($this->mock->deleteUsersByEmail(...))
    ->expect()
    ->with(Arr::count(3))
    ->toHaveBeenCalled();
```

`array` must be a partial match
```php
use Exan\Moock\Args\Arr;

$this->mock->deleteUsersByEmail([
    'some-email@example.com',
    'ignore-this@mail.com',
    'another@example.com',
]);

Mock::method($this->mock->deleteUsersByEmail(...))
    ->expect()
    ->with(Arr::partial([
        0 => 'some-email@example.com',
        2 => fn ($email) => str_ends_with($email, '@example.com'),
    ]))
    ->toHaveBeenCalled();
```

---

## Order expectations

If you're expecting many calls to be made after each other in specific order, you may use `Mock::expect`

### Verifying order of calls
To verify the order in which methods were called on a mock, call `$expect` in the desired order with the given method.
```php
$this->mock->isValidEmail('mail@domain.com');
$this->mock->userExists('mail@domain.com');
$this->mock->createUser('mail@domain.com', 'username', 'password');

// Asserting the methods are called in the right order only
Mock::expect(function ($expect) {
    $expect($this->mock->isValidEmail(...));
    $expect($this->mock->userExists(...));
    $expect($this->mock->createUser(...));
});
```

### Verifying order and arguments
Optionally attach expectations of the given arguments.
```php
Mock::expect(function (Closure $expect) {
    $expect($this->mock->isValidEmail(...))->with('mail@domain.com');
    $expect($this->mock->userExists(...)); // Argument isn't verified, just order

    // Only mail and password are validated
    $expect($this->mock->createUser(...))->with('mail@domain.com', password: 'password');
});
```

### Verifying order between different mocks
You may also validate in what order methods were called between several mocks
```php
$userService = Mock::interface(UserServiceInterface::class);
$productsService = Mock::interface(ProductServiceInterface::class);

$productsService->productExists(123);
$userService->isValidEmail('mail@domain.com');
$productsService->purchase(123, 'mail@domain.com');

Mock::expect(function (Closure $expect) use ($userService, $productsService) {
    $expect($productsService->productExists(...))->with(123);
    $expect($userService->isValidEmail(...))->with('mail@domain.com');
    $expect($productsService->productExists(...))->with(123, 'mail@domain.com');
});
```

---

## Filtering method arguments

Filters restrict which arguments are allowed at runtime. Calls with disallowed input will immediately throw a `RuntimeException`.

### Filtering an argument
To filter arguments that are allowed into a method, you can use the `filter()` method.
```php
Mock::method($this->mock->userExists(...))
    ->filter('my-email@domain.com')
    ->forceReturn(true);

$this->assertTrue($this->mock->userExists('my-email@domain.com'));

$this->expectException(RuntimeException::class);
// Since other-email@domain.com is not allowed per the filter, the method throws a RuntimeException
$this->mock->userExists('other-email@domain.com');
```

To filter specific args of a method, use named properties.
```php
Mock::method($this->mock->createUser(...))
    ->filter(username: 'my-username');

$this->mock->createUser('my-email@domain.com', 'my-username', 'password');

$this->expectException(RuntimeException::class);
// Since username: other-username is not allowed per the filter, the method throws a RuntimeException
$this->mock->createUser('my-email@domain.com', 'other-username', 'password');
```

### Using closures
You can also pass a closure instead of a straight value, or use some of the helper functions documented in the expectations section instead.
```php
Mock::method($this->mock->userExists(...))
    ->filter(fn (string $email) => in_array($email, ['first@mail.com', 'second@mail.com']))
    ->forceReturn(true);

$this->mock->userExists('first@mail.com');
$this->mock->userExists('second@mail.com');

$this->expectException(RuntimeException::class);
$this->mock->userExists('third@domain.com');
```

---

## Creating partial mocks

### Partial mocks
A partial mock wraps an existing object, forwarding method calls and property access to the underlying instance. This allows you to override specific behavior while leaving the rest untouched.

They can be useful when only a small part of an object needs to be controlled, though they tend to work best when used sparingly.
```php
$this->real = new UserService();

$this->real->users = [
    'first@mail.com',
    'second@mail.com',
    'third@mail.com',
];

$this->partial = Mock::partial($this->real);
```

Any method not explicitly mocked will be forwarded to its original implementation.
```php
$this->assertTrue($this->partial->userExists('first@mail.com'));
$this->assertFalse($this->partial->userExists('fourth@mail.com'));
```

Methods can still be mocked, in which case the original implementation is bypassed selectively.
```php
use Exan\Moock\Mock;

Mock::method($this->partial->userExists(...))
    ->replace(fn (string $email) => $email === 'fourth@mail.com');

$this->assertFalse($this->partial->userExists('first@mail.com'));
$this->assertTrue($this->partial->userExists('fourth@mail.com'));
```

Properties are also synced between real & fake. _Note: this does not work for properties with `private(set)`, `readonly`, or `final`._
```php
$this->assertEquals([
    'first@mail.com',
    'second@mail.com',
    'third@mail.com',
], $this->partial->users);

$this->partial->users = ['fourth@mail.com'];

$this->assertEquals(['fourth@mail.com'], $this->real->users);
```

---

## Compatibility

Out of the box, Moock will use PHPUnit or Nette Tester assertion methods if they're available. If neither are available, a regular PHP assert is used instead.

### Registering a custom assertion
```php
use Exan\Moock\MoockAssert;

$usedAssert = false;

MoockAssert::useAssert(function (bool $actual, bool $expected, string $message) use (&$usedAssert): void {
    $usedAssert = true;
});

$mock = Mock::interface(UserServiceInterface::class);

Mock::method($mock->createUser(...))
    ->expect()
    ->toHaveBeenCalled(); // This now uses our custom assert, which doesn't throw exceptions on failure

$this->assertTrue($usedAssert);
```
