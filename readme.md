# Moock
<p align="center">
    <img src="moock.png" alt="Moock logo"><br>
    A simple way of mocking objects in PHP
</p>

## Usage


### Asserting number of calls

```php
use Exan\Moock\Mock;

$userService = Mock::class(UserService::class);

Mock::method($userService->isValidEmail(...))
    ->forceReturn(true);

Mock::method($userService->isValidEmail(...))
    ->expect()
    ->not()->toHaveBeenCalled();

$userService->isValidEmail('::my_test_email::');

Mock::method($userService->isValidEmail(...))
    ->expect()->toHaveBeenCalled();

Mock::method($userService->isValidEmail(...))
    ->expect()->toHaveBeenCalledOnce();

$userService->isValidEmail('::my_other_test_email::');

Mock::method($userService->isValidEmail(...))
    ->expect()->toHaveBeenCalledTimes(2);

# Invert any assertion using ->expect()->not()
Mock::method($userService->isValidEmail(...))
    ->expect()->not()->haveBeenCalledTimes(3);
```

### Force returning a sequence of values

```php
use Exan\Moock\Mock;

$realUserService = (...);

Mock::partial($realUserService);

Mock::method($userService->isValidEmail(...))
    ->forceReturn(true);

$userService->isValidEmail('::my_test_email::'); // true
$userService->anyOtherMethod('...'); // calls $realUserService

// If you now want a specific method to not be forwarded to $realUserService, you can void it
Mock::method($userService->someOtherMethod(...))
    ->void();

$userService->someOtherMethod('my-arg'); // Does NOT call $realUserService
```

### Expecting specific input

```php
$userService = Mock::class(UserService::class);

Mock::method($userService->isValidEmail(...))
    ->forceReturn(true);

$userService->isValidEmail('::my_test_email::');
$userService->isValidEmail('::my_other_test_email::');
$userService->isValidEmail('::my_other_test_email::');

Mock::method($userService->isValidEmail(...))
    ->expect()
    ->with('::my_test_email::')
    ->toHaveBeenCalledOnce();

Mock::method($userService->isValidEmail(...))
    ->expect()
    ->with('::my_other_test_email::')
    ->toHaveBeenCalledTimes(2);

# Using closures
Mock::method($userService->isValidEmail(...))
    ->expect()
    ->with(fn ($email) => true)
    ->toHaveBeenCalledTimes(3);

$userService->isValidEmail('::my_test_email::', 'test-password');
$userService->isValidEmail('::my_other_test_email::', 'test-password');
$userService->isValidEmail('::my_other_test_email::', 'other-password');

# Using named args
Mock::method($userService->isValidEmail(...))
    ->expect()
    ->with(password: 'test-password')
    ->toHaveBeenCalledTimes(2);
```

### Mocking non-public API of a class

You don't.
