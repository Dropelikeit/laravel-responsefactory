[![Library tester](https://github.com/Dropelikeit/laravel-responsefactory/actions/workflows/app-tester.yml/badge.svg)](https://github.com/Dropelikeit/laravel-responsefactory/actions/workflows/app-tester.yml)
[![Coverage Status](https://coveralls.io/repos/github/Dropelikeit/laravel-responsefactory/badge.svg?branch=main)](https://coveralls.io/github/Dropelikeit/laravel-responsefactory?branch=main)
[![PHP Version Require](http://poser.pugx.org/dropelikeit/laravel-responsefactory/require/php)](https://packagist.org/packages/dropelikeit/laravel-responsefactory)
[![Monthly Downloads](https://poser.pugx.org/dropelikeit/laravel-responsefactory/d/monthly)](https://packagist.org/packages/dropelikeit/laravel-responsefactory)
[![Daily Downloads](https://poser.pugx.org/dropelikeit/laravel-responsefactory/d/daily)](https://packagist.org/packages/dropelikeit/laravel-responsefactory)
[![Total Downloads](https://poser.pugx.org/dropelikeit/laravel-responsefactory/downloads)](https://packagist.org/packages/dropelikeit/laravel-responsefactory)
[![Latest Stable Version](http://poser.pugx.org/dropelikeit/laravel-responsefactory/v)](https://packagist.org/packages/dropelikeit/laravel-responsefactory)
[![License](https://poser.pugx.org/dropelikeit/laravel-responsefactory/license)](https://packagist.org/packages/dropelikeit/laravel-responsefactory)
[![composer.lock](https://poser.pugx.org/dropelikeit/laravel-responsefactory/composerlock)](https://packagist.org/packages/dropelikeit/laravel-responsefactory)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Flaravel-responsefactory)](https://dashboard.stryker-mutator.io/reports/laravel-responsefactory)

# ResponseFactory for Laravel

The ResponseFactory is a successor to the [laravel-jms-serializer](https://github.com/Dropelikeit/laravel-jms-serializer) 
package that focused on this. Using the JMS serializer 
within the ResponseFactory to create Laravel responses and thus remain in the OOP environment and 
not use functions or own response objects within the controller. In addition, it should be ensured that everything is 
processed in an object-oriented manner up to the output.

JMS-Serializer: [https://github.com/schmittjoh/serializer](https://github.com/schmittjoh/serializer)

### Installation

```bash
 composer require dropelikeit/laravel-responsefactory
 ```

### Support note
| Laravel |    PHP    | Package Version | Status  |
|:-------:|:---------:|:---------------:|:-------:|
|   11    |    8.3    |     v1.x.x      | Support |
|   11    |    8.3    |     v1.x.x      | Support |
|   11    | 8.3 + 8.4 |     v1.x.x      | Support |

### How to use

Laravel uses Package Auto-Discovery, so you do not need to add the service provider manually. 

For example, to use the ResponseFactory in a controller, simply insert the ResponseFactory in the constructor.

```php
    <?php 
    namespace App\Http\Controller;

    use Dropelikeit\ResponseFactory\Http\Responses\ResponseFactory;
    use Symfony\Component\HttpFoundation\JsonResponse;

    final class ExampleController extends Controller 
    {
        public function __construct(private ResponseFactory $responseFactory) {}

        public function myAction(): JsonResponse
        {
            $myDataObjectWithSerializerAnnotations = new Object('some data');
            return $this->responseFactory->create($myDataObjectWithSerializerAnnotations);
        }
    }
```

Publish the ResponseFactory Config with the command

```bash 
    php artisan vendor:publish
```

After that you will see a config file in your config folder named "responsefactory.php".

## Upgrade
It is the first stable version, therefore it make no sense to read an upgrade guide. 

## Documentation

* [Configuration](docs/configuration.md)
* [ResponseFactory](docs/response-factory.md)

---

## Collaborators

<!-- readme: collaborators -start -->
<table>
	<tbody>
		<tr>
            <td align="center">
                <a href="https://github.com/Dropelikeit">
                    <img src="https://avatars.githubusercontent.com/u/13794109?v=4" width="100;" alt="Dropelikeit"/>
                    <br />
                    <sub><b>Marcel Strahl</b></sub>
                </a>
            </td>
		</tr>
	<tbody>
</table>
<!-- readme: collaborators -end -->

## Contributors

<!-- readme: contributors -start -->
<table>
	<tbody>
		<tr>
            <td align="center">
                <a href="https://github.com/Dropelikeit">
                    <img src="https://avatars.githubusercontent.com/u/13794109?v=4" width="100;" alt="Dropelikeit"/>
                    <br />
                    <sub><b>Marcel Strahl</b></sub>
                </a>
            </td>
		</tr>
	<tbody>
</table>
<!-- readme: contributors -end -->

# Contribution

---

Help us to improve this project by reporting bugs, enhancement requests or other suggestions via the issue tracker.

After creating the issue tracker, it is also possible to create a PR with reference to the issue.

Thank you very much for your support!