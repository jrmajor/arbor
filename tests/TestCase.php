<?php

namespace Tests;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use Psl\Iter;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutDeprecationHandling();

        Http::preventStrayRequests();
    }

    public function withPermissions(int $permissions): self
    {
        return $this->actingAs(
            User::factory()->createOne(['permissions' => $permissions]),
        );
    }

    /**
     * @param list<string> $keys
     * @param iterable<mixed, mixed> $iterable
     */
    public static function assertDoesNotHaveKeys(array $keys, iterable $iterable): void
    {
        foreach ($keys as $key) {
            self::assertFalse(
                Iter\contains_key($iterable, $key),
                "Failed asserting that a iterable does not have \"{$key}\" key.",
            );
        }
    }

    public static function assertSameModel(Model|Relation $expected, mixed $actual): void
    {
        self::assertThat($actual, Assert::logicalOr(
            new IsInstanceOf(Model::class),
            new IsInstanceOf(Relation::class),
        ));

        self::assertTrue(
            $actual->is($expected),
            'Value is not expected Eloquent model.',
        );
    }

    /**
     * @param array{class-string, string}&callable $action
     */
    public function assertActionUsesFormRequest(array $action, string $formRequest): void
    {
        [$controller, $method] = $action;

        $this->assertTrue(
            is_subclass_of($formRequest, FormRequest::class),
            "{$formRequest} should be a subclass of FormRequest.",
        );

        try {
            $action = new ReflectionClass($controller)->getMethod($method);
        } catch (ReflectionException) {
            $this->fail("Controller action does not exist: {$controller}@{$method}");
        }

        $this->assertTrue(
            $action->isPublic(), "Action {$controller}@{$method} is not public.",
        );

        $this->assertTrue(
            Iter\any(
                $action->getParameters(),
                function (ReflectionParameter $parameter) use ($formRequest) {
                    return $parameter->getType() instanceof ReflectionNamedType
                        && $parameter->getType()->getName() === $formRequest;
                },
            ),
            "Failed asserting that {$controller}@{$method} uses {$formRequest} form request.",
        );
    }
}
