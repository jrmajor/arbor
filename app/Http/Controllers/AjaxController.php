<?php

namespace App\Http\Controllers;

use App\Services\Pytlewski\PytlewskiFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function __construct(
        private PytlewskiFactory $pytlewskiFactory,
    ) { }

    public function pytlewskiName(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);

        $pytlewski = $this->pytlewskiFactory->find($request->integer('id'));

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
}
