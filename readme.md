# Moock
<p align="center">
    <img src="moock.png" alt="Moock logo"><br>
    A simple way of mocking objects in PHP
</p>

## Usage

### Mocking a class

```php
use Exan\Moock\Mock;

$userService = Mock::class(UserService::class);
```

### Mocking an interface

```php
use Exan\Moock\Mock;

$userService = Mock::interface(UserServiceInterface::class);
```

### Mocking several interfaces

```php
use Exan\Moock\Mock;

/** @var CreatesUsersInterface&DeletesUsersInterface */
$userService = Mock::interfaces(
    CreatesUsersInterface::class,
    DeletesUsersInterface::class,
);
```

### Replacing a method

```php
use Exan\Moock\Mock;

$userService = Mock::class(UserService::class);

Mock::method($userService->isValidEmail(...))
    ->replace(fn ($email) => $email === '::my_test_email::');

$userService->isValidEmail('::my_test_email::'); // true
$userService->isValidEmail('::other_value::'); // false
```

### Force returning a value

```php
use Exan\Moock\Mock;

$userService = Mock::class(UserService::class);

Mock::method($userService->isValidEmail(...))
    ->forceReturn(true);

$userService->isValidEmail('::my_test_email::'); // true
$userService->isValidEmail('::other_value::'); // true
```

### Force returning a sequence of values

```php
use Exan\Moock\Mock;

$userService = Mock::class(UserService::class);

Mock::method($userService->isValidEmail(...))
    ->forceReturnSequence([true, false]);

$userService->isValidEmail('::my_test_email::'); // true
$userService->isValidEmail('::other_value::'); // false
```

### Force throwing of an exception

```php
use Exan\Moock\Mock;

$userService = Mock::class(UserService::class);

Mock::method($userService->isValidEmail(...))
    ->throwsException(RuntimeException::class);

$userService->isValidEmail('::my_test_email::'); // Fatal error: Uncaught RuntimeException
$userService->isValidEmail('::other_value::'); // ...
```

### Asserting number of calls

```php
use Exan\Moock\Mock;

$userService = Mock::class(UserService::class);

Mock::method($userService->isValidEmail(...))
    ->forceReturn(true);

Mock::method($userService->isValidEmail(...))
    ->shouldNotHaveBeenCalled();

$userService->isValidEmail('::my_test_email::');

Mock::method($userService->isValidEmail(...))
    ->shouldHaveBeenCalledOnce();

$userService->isValidEmail('::my_other_test_email::');

Mock::method($userService->isValidEmail(...))
    ->shouldHaveBeenCalledTimes(2);

Mock::method($userService->isValidEmail(...))
    ->shouldNotHaveBeenCalledTimes(3);
```
