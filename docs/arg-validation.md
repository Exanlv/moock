# Table of contents

 - [String helpers](#string-helpers)
     - [Length](#length)
     - [Contains](#contains)
     - [Starts With](#starts-with)
     - [Ends With](#ends-with)
     - [Regex](#regex)
     - [Min Length](#min-length)
     - [Max Length](#max-length)
     - [Alpha](#alpha)
     - [Alphanumeric](#alphanumeric)
     - [Lowercase](#lowercase)
     - [Uppercase](#uppercase)
     - [Not Empty](#not-empty)
 - [Number helpers](#number-helpers)
     - [Less than](#less-than)
     - [Greater than](#greater-than)
     - [Range](#range)
     - [Positive](#positive)
     - [Negative](#negative)
     - [Even](#even)
     - [Odd](#odd)
     - [Divisible by](#divisible-by)
     - [Approximately](#approximately)
 - [Date helpers](#date-helpers)
     - [Before](#before)
     - [After](#after)
     - [Between](#between)
     - [Equal](#equal)
     - [Same day](#same-day)
     - [In past](#in-past)
     - [In future](#in-future)
 - [Type helpers](#type-helpers)
     - [String](#string)
     - [Integer](#integer)
     - [Float](#float)
     - [Array](#array)
     - [Resource](#resource)
     - [Object](#object)
     - [Boolean](#boolean)
     - [Null](#null)
     - [Callable](#callable)
     - [Iterable](#iterable)
     - [Scalar](#scalar)
     - [Instance of](#instance-of)
 - [Array helpers](#array-helpers)
     - [Count](#count)
     - [Partial match](#partial-match)
     - [All](#all)
     - [Any](#any)
     - [None](#none)
     - [Contains](#contains)
     - [Keys](#keys)
     - [Empty](#empty)
     - [Not empty](#not-empty)
     - [Indexed](#indexed)

## String helpers

```php
use Exan\Moock\Args\Str;
```

### Length
Validate exact string length
```php
$validator = Str::length(5);

$this->assertTrue($validator('hello'));
$this->assertFalse($validator('hell'));
```

### Contains
Validate substring presence (case-insensitive)
```php
$validator = Str::contains('world');

$this->assertTrue($validator('hello world'));
$this->assertTrue($validator('hello WoRLd'));
$this->assertFalse($validator('hello there'));
```

### Starts With
Validate string prefix (case-insensitive)
```php
$validator = Str::startsWith('hel');

$this->assertTrue($validator('hello'));
$this->assertFalse($validator('world'));
```

### Ends With
Validate string suffix (case-insensitive)
```php
$validator = Str::endsWith('lo');

$this->assertTrue($validator('hello'));
$this->assertFalse($validator('world'));
```

### Regex
Validate string against regex pattern
```php
$validator = Str::matchesRegex('/^[a-z]+$/');

$this->assertTrue($validator('hello'));
$this->assertFalse($validator('hello123'));
```

### Min Length
Validate minimum string length
```php
$validator = Str::minLength(3);

$this->assertTrue($validator('abcd'));
$this->assertFalse($validator('ab'));
```

### Max Length
Validate maximum string length
```php
$validator = Str::maxLength(3);

$this->assertTrue($validator('ab'));
$this->assertFalse($validator('abcd'));
```

### Alpha
Validate string contains alphabetical characters only
```php
$validator = Str::alpha();

$this->assertTrue($validator('abc'));
$this->assertFalse($validator('abc123'));
```

### Alphanumeric
Validate string contains alphaniumeric characters only
```php
$validator = Str::alphanumeric();

$this->assertTrue($validator('abc123'));
$this->assertFalse($validator('abc-123'));
```

### Lowercase
Validate string is fully lowercase
```php
$validator = Str::lowercase();

$this->assertTrue($validator('hello'));
$this->assertFalse($validator('Hello'));
```

### Uppercase
Validate string is fully uppercase
```php
$validator = Str::uppercase();

$this->assertTrue($validator('HELLO'));
$this->assertFalse($validator('Hello'));
```

### Not Empty
Validate string is not empty
```php
$validator = Str::notEmpty();

$this->assertTrue($validator('a'));
$this->assertFalse($validator(''));
```

---

## Number helpers

```php
use Exan\Moock\Args\Number;
```

### Less than
Validate a number is less than a given value
```php
$validator = Number::lt(10);

$this->assertTrue($validator(5));
$this->assertFalse($validator(15));
```

### Greater than
Validate a number is greater than a given value
```php
$validator = Number::gt(10);

$this->assertTrue($validator(15));
$this->assertFalse($validator(5));
```

### Range
Validate a number is within a range (inclusive)
```php
$validator = Number::range(10, 20);

$this->assertTrue($validator(10));
$this->assertTrue($validator(15));
$this->assertTrue($validator(20));

$this->assertFalse($validator(9));
$this->assertFalse($validator(21));
```

### Positive
Validate a number is positive
```php
$validator = Number::positive();

$this->assertTrue($validator(1));
$this->assertFalse($validator(0));
$this->assertFalse($validator(-1));
```

### Negative
Validate a number is negative
```php
$validator = Number::negative();

$this->assertTrue($validator(-1));
$this->assertFalse($validator(0));
$this->assertFalse($validator(1));
```

### Even
Validate a number is even integer
```php
$validator = Number::even();

$this->assertTrue($validator(2));
$this->assertFalse($validator(3));
$this->assertFalse($validator(2.5));
```

### Odd
Validate a number is odd integer
```php
$validator = Number::odd();

$this->assertTrue($validator(3));
$this->assertFalse($validator(2));
$this->assertFalse($validator(3.5));
```

### Divisible by
Validate a number is divisible by a given divisor
```php
$validator = Number::divisibleBy(3);

$this->assertTrue($validator(9));
$this->assertFalse($validator(10));
```

### Approximately
Validate a number is approximately equal within epsilon
```php
$validator = Number::approx(10.0, 0.01);

$this->assertTrue($validator(10.005));
$this->assertFalse($validator(10.1));
```

---

## Date helpers

```php
use Exan\Moock\Args\Date;
```

### Before
Validate a date is before a given date
```php
$target = new \DateTimeImmutable('2024-01-10');
$validator = Date::before($target);

$this->assertTrue($validator(new \DateTimeImmutable('2024-01-09')));
$this->assertFalse($validator(new \DateTimeImmutable('2024-01-11')));
```

### After
Validate a date is after a given date
```php
$target = new \DateTimeImmutable('2024-01-10');
$validator = Date::after($target);

$this->assertTrue($validator(new \DateTimeImmutable('2024-01-11')));
$this->assertFalse($validator(new \DateTimeImmutable('2024-01-09')));
```

### Between
Validate a date is within a range (inclusive)
```php
$start = new \DateTimeImmutable('2024-01-10');
$end = new \DateTimeImmutable('2024-01-20');

$validator = Date::between($start, $end);

$this->assertTrue($validator(new \DateTimeImmutable('2024-01-10')));
$this->assertTrue($validator(new \DateTimeImmutable('2024-01-15')));
$this->assertTrue($validator(new \DateTimeImmutable('2024-01-20')));
$this->assertFalse($validator(new \DateTimeImmutable('2024-01-09')));
$this->assertFalse($validator(new \DateTimeImmutable('2024-01-21')));
```

### Equal
Validate a date equals another date
```php
$target = new \DateTimeImmutable('2024-01-10');

$validator = Date::equal($target);

$this->assertTrue($validator(new \DateTimeImmutable('2024-01-10')));
$this->assertFalse($validator(new \DateTimeImmutable('2024-01-11')));
```

### Same day
Validate a date falls on the same calendar day
```php
$target = new \DateTimeImmutable('2024-01-10 10:00:00');

$validator = Date::sameDay($target);

$this->assertTrue($validator(new \DateTimeImmutable('2024-01-10 23:59:59')));
$this->assertFalse($validator(new \DateTimeImmutable('2024-01-11 00:00:00')));
```

### In past
Validate a date is in the past
```php
$validator = Date::inPast();

$this->assertTrue($validator(new \DateTimeImmutable('yesterday')));
$this->assertFalse($validator(new \DateTimeImmutable('tomorrow')));
```

### In future
Validate a date is in the future
```php
$validator = Date::inFuture();

$this->assertTrue($validator(new \DateTimeImmutable('tomorrow')));
$this->assertFalse($validator(new \DateTimeImmutable('yesterday')));
```

---

## Type helpers

```php
use Exan\Moock\Args\Type;
```

### String
Validate that a value is a string
```php
$validator = Type::string();

$this->assertTrue($validator('hello'));
$this->assertFalse($validator(123));
```

### Integer
Validate that a value is an integer
```php
$validator = Type::int();

$this->assertTrue($validator(123));
$this->assertFalse($validator('123'));
```

### Float
Validate that a value is a float
```php
$validator = Type::float();

$this->assertTrue($validator(1.23));
$this->assertFalse($validator(123));
```

### Array
Validate that a value is an array
```php
$validator = Type::array();

$this->assertTrue($validator(['a']));
$this->assertFalse($validator('a'));
```

### Resource
Validate that a value is a resource
```php
$validator = Type::resource();

$resource = fopen('php://memory', 'r');

$this->assertTrue($validator($resource));
$this->assertFalse($validator('not a resource'));

fclose($resource);
```

### Object
Validate that a value is an object
```php
$validator = Type::object();

$this->assertTrue($validator(new \stdClass()));
$this->assertFalse($validator('not an object'));
```

### Boolean
Validate that a value is a boolean
```php
$validator = Type::bool();

$this->assertTrue($validator(true));
$this->assertFalse($validator(1));
```

### Null
Validate that a value is null
```php
$validator = Type::null();

$this->assertTrue($validator(null));
$this->assertFalse($validator(false));
```

### Callable
Validate that a value is callable
```php
$validator = Type::callable();

$this->assertTrue($validator(fn () => null));
$this->assertFalse($validator('not a callable'));
```

### Iterable
Validate that a value is iterable
```php
$validator = Type::iterable();

$this->assertTrue($validator([1, 2, 3]));
$this->assertFalse($validator(123));
```

### Scalar
Validate that a value is scalar
```php
$validator = Type::scalar();

$this->assertTrue($validator('string'));
$this->assertFalse($validator([1, 2, 3]));
```

### Instance of
Validate that a value is instance of a class
```php
$validator = Type::instanceOf(Exception::class);

$this->assertTrue($validator(new Exception()));
$this->assertTrue($validator(new RuntimeException()));

$this->assertFalse($validator(new DateTime()));
```

---

## Array helpers

```php
use Exan\Moock\Args\Arr;
```

### Count
Validate the amount of items in an array
```php
$count = Arr::count(3);

$this->assertTrue($count([1, 2, 3]));
$this->assertFalse($count([1, 2]));
```

### Partial match
Validate an array has all specified values.
```php
$partial = Arr::partial(['key' => 'value']);

$matching = [
    'key' => 'value',
    'extra-field' => 'extra-value',
];

$missingKey = [
    'other-key' => 'other-value',
];

$this->assertTrue($partial($matching));
$this->assertFalse($partial($missingKey));
```

### All
Validate that all items in an array match a condition.
```php
$validator = Arr::all(fn (mixed $value) => $value % 2 === 0);

$this->assertTrue($validator([2, 4, 6]));
$this->assertFalse($validator([2, 4, 6, 7]));
```

This can also be combined with other helpers.
```php
$validator = Arr::all(Type::int());

$this->assertTrue($validator([2, 4, 6]));
$this->assertFalse($validator([2, '4', 6]));
```

### Any
Validate that at least one item matches a condition.
```php
$validator = Arr::any(fn (mixed $value) => $value === 3);

$this->assertTrue($validator([1, 2, 3]));
$this->assertFalse($validator([1, 2, 4]));
```

### None
Validate that no items match a condition.
```php
$validator = Arr::none(fn (mixed $value) => $value === 5);

$this->assertTrue($validator([1, 2, 3]));
$this->assertFalse($validator([1, 5, 3]));
```

### Contains
Validate an array contains a value
```php
$validator = Arr::contains(2);

$this->assertTrue($validator([1, 2, 3]));
$this->assertFalse($validator([1, 3, 4]));
```

### Keys
Validate an array has specific keys
```php
$validator = Arr::keys(['a', 'b']);

$this->assertTrue($validator(['a' => 1, 'b' => 2, 'c' => 3]));
$this->assertFalse($validator(['a' => 1, 'c' => 3]));
```

### Empty
Validate an array is empty
```php
$validator = Arr::empty();

$this->assertTrue($validator([]));
$this->assertFalse($validator([1]));
```

### Not empty
Validate an array is not empty
```php
$validator = Arr::notEmpty();

$this->assertTrue($validator([1]));
$this->assertFalse($validator([]));
```

### Indexed
Validate an array is sequential indexed
```php
$validator = Arr::indexed();

$this->assertTrue($validator([1, 2, 3]));
$this->assertFalse($validator(['key-1' => 'a', 'key-2' => 'b']));
```
