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

_Note: This also works for anonymous classes, however as these are not proper class structures, they will not extend the given anonymous class. Rather, they will extend the parent and implement the same interfaces if applicable._

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

Note: currently does not work for failing/passing tests in PHPUnit, WIP

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

Mock::method($userService->isValidEmail(...))->calls(); // 3
```

### Force returning a sequence of values

```php
use Exan\Moock\Mock;

$realUserService = (...);
$userService = Mock::class(UserService::class);

/**
 * $realUserService does NOT have to implement any of the mocked interfaces or classes
 * it can be anything you want. The only requirement is that method names match.
 */
Mock::partial($userService, $realUserService);

Mock::method($userService->isValidEmail(...))
    ->forceReturn(true);

$userService->isValidEmail('::my_test_email::'); // true
$userService->anyOtherMethod('...'); // calls $realUserService
```