<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function withPermissions(int $permissions): self
    {
        $user = User::factory()
            ->create(['permissions' => $permissions]);

        return $this->actingAs($user);
    }

    public function assertActionUsesFormRequest(array $action, string $formRequest)
    {
        [$controller, $method] = $action;

        $this->assertTrue(
            is_subclass_of($formRequest, FormRequest::class),
            "{$formRequest} should be a subclass of FormRequest.",
        );

        try {
            $action = (new ReflectionClass($controller))->getMethod($method);
        } catch (ReflectionException) {
            $this->fail("Controller action does not exist: {$controller}@{$method}");
        }

        $this->assertTrue(
            $action->isPublic(), "Action {$controller}@{$method} is not public.",
        );

        $actual = collect($action->getParameters())
            ->contains(function (ReflectionParameter $parameter) use ($formRequest) {
                return $parameter->getType() instanceof ReflectionNamedType
                    && $parameter->getType()->getName() === $formRequest;
            });

        $this->assertTrue(
            $actual, "Failed asserting that {$controller}@{$method} uses {$formRequest} form request.",
        );
    }
}
