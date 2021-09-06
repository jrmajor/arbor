<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate sitemap.';

    public function handle(): void
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
            ->add(Url::create('/people')->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        Person::lazy(100)
            ->filter(fn (Person $person) => $person->isVisible())
            ->each(fn (Person $person) => $sitemap->add(
                Url::create("/people/{$person->id}")
                    ->setLastModificationDate($person->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY),
            ));

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap has been generated successfully.');
    }
}
