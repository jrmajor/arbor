<?php

namespace Tests;

use App\Application;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Pest\Livewire\InteractsWithLivewire;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithLivewire;
    use LazilyRefreshDatabase;

    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function withPermissions(int $permissions): self
    {
        return $this->actingAs(
            User::factory()->createOne(['permissions' => $permissions]),
        );
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
