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
You can replace any method on your mocks using the following examples
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
    'password123'
);
```

