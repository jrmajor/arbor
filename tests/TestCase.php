<?php

namespace Tests;

use App\Person;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\Assert;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function assertRouteUsesFormRequest(string $routeName, string $formRequest)
    {
        $controllerAction = collect(Route::getRoutes())->filter(function (\Illuminate\Routing\Route $route) use ($routeName) {
            return $route->getName() == $routeName;
        })->pluck('action.controller');

        Assert::assertNotEmpty($controllerAction, 'Route "' . $routeName . '" is not defined.');
        Assert::assertCount(1, $controllerAction, 'Route "' . $routeName . '" is defined multiple times, route names should be unique.');

        $controller = $controllerAction->first();
        $method = '__invoke';
        if (strstr($controllerAction->first(), '@')) {
            [$controller, $method] = explode('@', $controllerAction->first());
        }

        $this->assertActionUsesFormRequest($controller, $method, $formRequest);
    }

    public function assertActionUsesFormRequest(string $controller, string $method, string $formRequest)
    {
        Assert::assertTrue(is_subclass_of($formRequest, 'Illuminate\\Foundation\\Http\\FormRequest'), $formRequest . ' is not a type of Form Request');

        try {
            $reflector = new \ReflectionClass($controller);
            $action = $reflector->getMethod($method);
        } catch (\ReflectionException $exception) {
            Assert::fail('Controller action could not be found: ' . $controller . '@' . $method);
        }

        Assert::assertTrue($action->isPublic(), 'Action "' . $method . '" is not public, controller actions must be public.');

        $actual = collect($action->getParameters())->contains(function ($parameter) use ($formRequest) {
            return $parameter->getType() instanceof \ReflectionNamedType && $parameter->getType()->getName() === $formRequest;
        });

        Assert::assertTrue($actual, 'Action "' . $method . '" does not have validation using the "' . $formRequest . '" Form Request.');
    }

    protected function personWithParents()
    {
        $mother = factory(Person::class)->state('woman')->create([
            'birth_date_from' => $this->faker->dateTimeBetween('-80 years', '-60 years')->format('Y-m-d'),
            'birth_date_to' => fn($person) => $person['birth_date_from'],
        ]);
        $father = factory(Person::class)->state('man')->create([
            'birth_date_from' => $this->faker->dateTimeBetween('-80 years', '-60 years')->format('Y-m-d'),
            'birth_date_to' => fn($person) => $person['birth_date_from'],
        ]);

        return factory(Person::class)->create([
            'mother_id' => $mother->id,
            'father_id' => $father->id,
            'birth_date_from' => $this->faker->dateTimeBetween('-40 years', '-30 years')->format('Y-m-d'),
            'birth_date_to' => fn($person) => $person['birth_date_from'],
        ]);
    }
}
