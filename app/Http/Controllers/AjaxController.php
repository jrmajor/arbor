<?php

namespace App\Http\Controllers;

use App\Services\Pytlewski\PytlewskiScraper;
use App\Services\Wielcy\WielcyScraper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psl\Str;
use Psl\Vec;

class AjaxController extends Controller
{
    public function __construct(
        private PytlewskiScraper $pytlewskiScraper,
        private WielcyScraper $wielcyScraper,
    ) { }

    public function pytlewskiName(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);

        $pytlewski = $this->pytlewskiScraper->find($request->integer('id'));

        if (! $pytlewski) {
            return response()->json(['result' => null]);
        }

        $result = "{$pytlewski->name} ";

        if ($pytlewski->middleName) {
            $result .= "{$pytlewski->middleName} ";
        }

        $result .= $pytlewski->lastName
            ? "{$pytlewski->lastName} ({$pytlewski->familyName})"
            : $pytlewski->familyName;

        return response()->json(['result' => $result]);
    }

    public function wielcyName(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|string|max:20']);

        $wielcy = $this->wielcyScraper->find($request->string('id')->toString());

        if (! $wielcy) {
            return response()->json(['result' => null]);
        }

        $names = [$wielcy->name, $wielcy->middleName, $wielcy->surname];

        return response()->json(['result' => Str\join(Vec\filter_nulls($names), ' ')]);
    }
}
