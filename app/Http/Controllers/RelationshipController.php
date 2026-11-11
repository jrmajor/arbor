<?php

namespace App\Http\Controllers;

use App\Http\Resources\People\PersonPageResource;
use App\Http\Resources\People\PersonResource;
use App\Models\Person;
use App\Services\Relationship\RelationshipFinder;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;

class RelationshipController extends Controller
{
    #[Authorize('view', 'person')]
    public function __invoke(Request $request, Person $person, RelationshipFinder $finder): Response
    {
        $other = $request->filled('to') ? Person::query()->findOrFail($request->integer('to')) : null;

        if ($other) {
            $this->authorize('view', $other);
        }

        $relationship = $other ? $finder->find($person, $other) : null;

        $people = $relationship
            ? Person::query()
                ->whereIn('id', [...array_column($relationship->steps, 'id'), ...$relationship->commonAncestors])
                ->get()
                ->keyBy('id')
            : collect();

        return Inertia::render('People/Relationship', [
            'person' => new PersonPageResource($person),
            'other' => $other ? new PersonResource($other) : null,
            'relationship' => $relationship ? [
                'blood' => $relationship->blood,
                'steps' => array_map(fn (array $step) => [
                    'person' => new PersonResource($people[$step['id']]),
                    'step' => $step['step'],
                    'sex' => $people[$step['id']]->sex?->value,
                ], $relationship->steps),
                'commonAncestors' => array_map(
                    fn (int $id) => new PersonResource($people[$id]),
                    $relationship->commonAncestors,
                ),
                'kinship' => $relationship->kinship,
            ] : null,
        ]);
    }
}
