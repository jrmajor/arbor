<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);

expect()->extend('toBeModel', function ($expected) {
    $this->toMatchConstraint(
        Assert::logicalOr(
            new IsInstanceOf(Model::class),
            new IsInstanceOf(Relation::class),
        ),
    );

    Assert::assertTrue(
        $this->value->is($expected),
        'Value is not expected Eloquent model.',
    );

    return $this;
});
