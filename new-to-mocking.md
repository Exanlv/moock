# New to mocking

Mocking is when you use replace certain classes or functions with test doubles to check whether your code behaves as expected.
These dummies allow you to pre-determine what unpredictable return values will be, or replace expensive or external operations, such as API calls or database queries.

Take for example the following classes:

```php
class Foo
{
    public function createId(): string
    {
        return substr(md5(mt_rand()), 0, 8);
    }
}

class Bar
{
    public function __construct(public readonly Foo $foo)
    {
    }

    public function createRecord(string $name): array
    {
        return [
            'id' => $this->foo->createId(),
            'name' => $name,
        ];
    }
}
```

Here you can see, `Foo@createId` is based on `mt_rand`, which makes it difficult to write deterministic tests.
To test the `Bar@createRecord` method, you can only really verify the working of the `$name` parameter.

```php
$bar = new Bar(new Foo());
$record = $bar->createRecord('Someone');

assert($record['name'] === 'Someone');
```

You can of course assert that `$record['id']` is a string, and you can make assertions based on the structure of the value.
These checks however, are more annoying to write, and less strict than what you can achieve with mocking.

With mocks:

```php
$foo = Mock::class(Foo::class);
Mock::method($foo->createId(...))
    ->returns('A hardcoded ID');

$bar = new Bar($foo);
$record = $bar->createRecord('Someone');

assert($record['id'] === 'A hardcoded ID');
assert($record['name'] === 'Someone');
```

Now by controlling what `Foo@createId` returns, we can verify that `Bar@createRecord` uses this value when creating the record.

Apart from pre-determining return values, you can also assert that a method was called with specific input.
This can help speed up your tests by only partially excecuting your application code.

```php
class HttpService
{
    public function post(string $url, array $data)
    {
        // ...
    }
}

class MyIntegration
{
    public function __construct(public readonly HttpService $httpService)
    {
    }

    public function updateUser(string $userId, array $data)
    {
        $this->httpService->post(
            'https://my-integration.com/users/' . $userId,
            $data
        );
    }
}
```

Asserting the behaviour of `MyIntegration@updateUser` without mocks would require you to connect to the external service in your test to make sure that the update went through correctly.
This slows down your tests, is hard to maintain and leaves you with a bunch of garbage data in the external service.

With mocks, this is easy to assert without these issues:

```php
$httpService = Mock::class(HttpService::class);

$myIntegration = new MyIntegration($httpService);
$myIntegration->updateUser('user-id', [
    'name' => 'Someone else',
]);

Mock::method($httpService->post(...))
    ->assert()
    ->with('https://my-integration.com/users/user-id', ['name' => 'Someone else'])
    ->calledOnce()
```

Now, `MyIntegration@updateUser` is fully covered in your test, without making a real HTTP request.
