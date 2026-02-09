* [Back](../README.md)
* [Configuration](configuration.md)
* [ResponseFactory](response-factory.md)

# Controller Dispatcher with HandledByResponseFactory Attribute

The package provides a custom `ControllerDispatcher` that automatically processes controller responses using the ResponseFactory when marked with the `#[HandledByResponseFactory]` attribute.

## Automatic Registration

The `ControllerDispatcher` is automatically registered when you install the package. Laravel's Package Auto-Discovery ensures that the service provider is loaded automatically.

## How it works

The custom `ControllerDispatcher` intercepts the response from your controller methods and automatically applies the ResponseFactory serialization when the method is annotated with the `#[HandledByResponseFactory]` attribute.

This allows you to:
1. Return plain arrays or objects from your controller methods
2. Let the ResponseFactory handle the serialization automatically
3. Keep your controller methods clean and focused on business logic

## Using the HandledByResponseFactory Attribute

### Basic Usage with Arrays

Simply add the `#[HandledByResponseFactory]` attribute to your controller method and return an array:

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Dropelikeit\ResponseFactory\Http\Attributes\HandledByResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserController extends Controller
{
    #[HandledByResponseFactory]
    public function index(): array
    {
        return [
            'users' => [
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'Jane Smith'],
            ],
        ];
    }
}
```

The dispatcher will automatically call `ResponseFactory::createFromArray()` to create the response.

### Using with Objects

You can also return objects (DTOs, entities, etc.) with JMS Serializer annotations:

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use Dropelikeit\ResponseFactory\Http\Attributes\HandledByResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    #[HandledByResponseFactory]
    public function show(int $id): UserDto
    {
        return $this->userService->findUser($id);
    }
}
```

The dispatcher will automatically call `ResponseFactory::create()` to serialize the object.

### When the Attribute is NOT Used

If you don't add the attribute, the dispatcher behaves like Laravel's default dispatcher:

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;

final class UserController extends Controller
{
    // No attribute - normal Laravel behavior
    public function index(): array
    {
        return [
            'users' => [
                ['id' => 1, 'name' => 'John Doe'],
            ],
        ];
    }
}
```

This will return a regular Laravel array response without ResponseFactory serialization.

### Returning Response Objects

If your method returns a `Response` object, the ResponseFactory will be bypassed automatically, even if the attribute is present:

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Dropelikeit\ResponseFactory\Http\Attributes\HandledByResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserController extends Controller
{
    #[HandledByResponseFactory]
    public function index(): JsonResponse
    {
        // This Response will be returned directly
        return new JsonResponse(['message' => 'Custom response']);
    }
}
```

## Benefits

### 1. Cleaner Controllers

Your controllers don't need to inject the ResponseFactory:

**Before:**
```php
public function __construct(
    private readonly ResponseFactory $responseFactory,
    private readonly UserService $userService
) {}

public function show(int $id): JsonResponse
{
    $user = $this->userService->findUser($id);
    return $this->responseFactory->create($user);
}
```

**After:**
```php
public function __construct(
    private readonly UserService $userService
) {}

#[HandledByResponseFactory]
public function show(int $id): UserDto
{
    return $this->userService->findUser($id);
}
```

### 2. Consistency

All controller methods marked with the attribute will consistently use the ResponseFactory configuration (serialize_null, custom handlers, etc.).

### 3. Flexibility

You can mix and match:
- Use the attribute for automatic serialization
- Manually inject ResponseFactory for complex scenarios
- Return regular responses when needed

## Advanced Examples

### Combining with Custom Context

You can still inject the ResponseFactory when you need custom serialization context:

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Dropelikeit\ResponseFactory\Http\Attributes\HandledByResponseFactory;
use Dropelikeit\ResponseFactory\Http\ResponseFactory;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ResponseFactory $responseFactory
    ) {}

    // For simple cases, use the attribute
    #[HandledByResponseFactory]
    public function index(): array
    {
        return ['users' => $this->userService->getAllUsers()];
    }

    // For complex cases, use the ResponseFactory manually
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findUser($id);

        $context = SerializationContext::create()
            ->setGroups(['detailed'])
            ->setSerializeNull(false);

        $this->responseFactory->withContext($context);

        return $this->responseFactory->create($user);
    }
}
```

### Combining with Status Codes

If your method is marked with the attribute, you can still inject ResponseFactory to set custom status codes:

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Dropelikeit\ResponseFactory\Http\Attributes\HandledByResponseFactory;
use Dropelikeit\ResponseFactory\Http\ResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ResponseFactory $responseFactory
    ) {}

    #[HandledByResponseFactory]
    public function create(CreateUserRequest $request): UserDto
    {
        // Set status code for created resource
        $this->responseFactory->withStatusCode(201);

        return $this->userService->createUser($request->validated());
    }
}
```

*Please be careful*, currently no files and silent responses are supported by the attribute path.

## Technical Details

The `ControllerDispatcher`:
1. Extends Laravel's `Illuminate\Routing\ControllerDispatcher`
2. Calls the parent dispatcher first to execute the controller method
3. Checks if the result is already a `Response` object (if yes, returns it immediately)
4. Uses reflection to check for the `#[HandledByResponseFactory]` attribute
5. If found and result is an array: calls `ResponseFactory::createFromArray()`
6. If found and result is an object: calls `ResponseFactory::create()`
7. Otherwise: returns the result as-is (normal Laravel behavior)

## Migration Guide

If you're already using ResponseFactory in your controllers, you can gradually migrate:

1. Keep your existing code working (manual injection)
2. Add the attribute to new controller methods
3. Optionally refactor existing methods to use the attribute
4. Remove ResponseFactory injection from constructors where no longer needed
