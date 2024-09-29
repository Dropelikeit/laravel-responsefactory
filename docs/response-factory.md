* [Back](../README.md)
* [Configuration](configuration.md)

## Using Custom-Context

To use your own JMS contexts, use the "withContext" method

To learn more about JMS context, read the JMS Serializer documentation: http://jmsyst.com/libs/serializer/master

```php
    <?php 
    declare(strict_types=1);
    
    namespace App\Http\Controller;

    use Dropelikeit\ResponseFactory\Http\ResponseFactory;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use JMS\Serializer\SerializationContext;

    final class ExampleController extends Controller 
    {
        public function __construct(private ResponseFactory $responseFactory) {}

        public function myAction(): JsonResponse
        {
            $myDataObjectWithSerializerAnnotations = new Object('some data');

            $context = SerializationContext::create()->setSerializeNull(true);

            $this->responseFactory->withContext($context);
            return $this->responseFactory->create($myDataObjectWithSerializerAnnotations);
        }
    }
```

## Using Status-Code

You do not always want to hand over a status code of 200 to the frontend. You can achieve this with the following code. Use the method "withStatusCode" for this

```php
    <?php
    declare(strict_types=1);
    
    namespace App\Http\Controller;

    use Dropelikeit\ResponseFactory\Http\ResponseFactory;
    use Symfony\Component\HttpFoundation\JsonResponse;

    final class ExampleController extends Controller 
    {
        public function __construct(private ResponseFactory $responseFactory) {}

        public function myAction(): JsonResponse
        {
            $myDataObjectWithSerializerAnnotations = new Object('some data');

            $this->responseFactory->withStatusCode(400);
            return $this->responseFactory->create($myDataObjectWithSerializerAnnotations);
        }
    }
```

## Using quiet response

Q: What is a silent response?
A: Quite simply, it is a normal Laravel response that contains the 204 status code. The 204 HTTP code does not represent any content, but it was successful.

You don't need to add the 204 status code yourself, when you call the `createQuietResponse` method, it will be set automatically.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use Dropelikeit\ResponseFactory\Http\ResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ExampleController extends Controller 
{
    public function __construct(private ResponseFactory $responseFactory) {}

    public function myAction(): JsonResponse
    {
        return $this->responseFactory->createQuietResponse();
    }
}
```

## Create a download response for a file by the response factory



```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use Dropelikeit\ResponseFactory\Dtos\Services\StringInput;use Dropelikeit\ResponseFactory\Http\ResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ExampleController extends Controller 
{
    public function __construct(private ResponseFactory $responseFactory) {}

    public function myAction(): JsonResponse
    {
        $myCsv = file_get_contents('csv-file.csv');
        
        $input = StringInput::create($myCsv);
    
        return $this->responseFactory->createByFile($input, 'my-csv.csv');
    }
}
```