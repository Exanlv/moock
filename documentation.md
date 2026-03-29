## Mocking a class

### Mocking a class
Creating a test dummy of whatever class you want
```php
$mock = Mock::class(UserService::class);

$this->assertInstanceOf(UserService::class, $mock);
```

### Mocking an interface
Creating a dummy implementation of whatever interface you want
```php
$mock = Mock::interface(UserServiceInterface::class);

$this->assertInstanceOf(UserServiceInterface::class, $mock);
```

### Mocking several interfaces
Creating a dummy implementation of several interfaces. You should only use this if your interfaces are compatible.
```php
$mock = Mock::interfaces(UserServiceInterface::class, TestInterface::class);

$this->assertInstanceOf(UserServiceInterface::class, $mock);
$this->assertInstanceOf(TestInterface::class, $mock);
```

---

## Replacing methods

### Replacing a method
You can replace any public method on your mocks using the following examples
```php
Mock::method($this->mock->userExists(...))->replace(function (string $email) {
    return $email === 'exists@mail.com';
});

$this->assertTrue($this->mock->userExists('exists@mail.com'));
$this->assertFalse($this->mock->userExists('doesnt@mail.com'));
```

Force returning a static value
```php
Mock::method($this->mock->userExists(...))->forceReturn(true);

$this->assertTrue($this->mock->userExists('some-email@domain.com'));
```

Force returning a sequence of values
```php
Mock::method($this->mock->userExists(...))->forceReturnSequence([true, true, false]);

$this->assertTrue($this->mock->userExists('some-email@domain.com'));
$this->assertTrue($this->mock->userExists('some-email@domain.com'));

// 3rd item is false
$this->assertFalse($this->mock->userExists('some-email@domain.com'));
```

Force throwing an exception
```php
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

`string` must contain `@mail.com`
```php
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
$this->mock->deleteUsersByEmail(['a','b','c']);

Mock::method($this->mock->deleteUsersByEmail(...))
    ->expect()
    ->with(Arr::count(3))
    ->toHaveBeenCalled();
```

`array` must be a partial match
```php
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

## Filtering method args

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

## Partial mocks

### Partial mocks
Creating a partial mock can be done in the following way. A partial mock will automatically forward any method call or property get to the partial object.
```php
$this->real = new UserService();

$this->real->users = [
    'first@mail.com',
    'second@mail.com',
    'third@mail.com',
];

$this->partial = Mock::partial($this->real);
```

Any method not explicitly mocked will be forwarded to it's full implementation.
```php
$this->assertTrue($this->partial->userExists('first@mail.com'));
$this->assertFalse($this->partial->userExists('fourth@mail.com'));
```

Methods can still be mocked, in which case the full implementation is bypassed selectively.
```php
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
