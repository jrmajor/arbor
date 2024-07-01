<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/people')->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        Person::lazy(100)
            ->filter(fn (Person $person) => $person->isVisible())
            ->each(fn (Person $person) => $sitemap->add(
                Url::create("/people/{$person->id}")
                    ->setLastModificationDate($person->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY),
            ));

        return $sitemap->toResponse($request);
    }
}
