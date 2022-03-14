<?php

namespace Tests\Feature\People;

use App\Models\Activity;
use App\Models\Person;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Tests\latestLog;

final class BiographyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->oldBiography = 'Ukończył gimnazjum w Kołomyi i studia wyższe (polonistyka, historia, geografia), zapewne na Uniwersytecie Lwowskim. Pracował jako nauczyciel gimnazjalny w Kołomyi, gdzie był działaczem Sokoła, Towarzystwa Szkoły Ludowej i Polskiego Towarzystwa Tatrzańskiego. Zajmował się organizacją Muzeum Pokuckiego i kolekcjonował huculską sztukę ludową. Pisał artykuły do prasy lokalnej i krajoznawczej. W latach I wojny światowej był oficerem armii astriackiej, od 1919 Wojska Polskiego. Od 1920 w Grudziądzu, gdzie był oficerem 64 pułku piechoty, 1922 referent ds. kultury i oświaty DOK VIII w Toruniu, następnie komendant Powiatowej Komendy Uzupełnień w Grudziądzu. W 1927 przeszedł na emeryturę w stopniu majora.';

        $this->newBiography = "Ukończył w 1893 roku c.k. Wyższe Gimnazjum w Stanisławowie a w 1898 roku Wyższe Seminarium (nauczycielskie) we Lwowie. W latach 1899–1900 odbył służbę wojskowa w Wiedniu. W 1903 zdobył patent nauczycielski Szkół Ludowych Pospolitych. Od 1907 pracował jako nauczyciel gimnastyki w Gimnazjum oraz w Szkole Męskiej im. Jachowicza w Kołomyi. Był działaczem Sokoła, Towarzystwa Szkoły Ludowej i Polskiego Towarzystwa Tatrzańskiego.\n\nOd 1903 kolekcjonował huculską sztukę ludową a od 1910 zajmował się organizacją reaktywowanego przez Kołomyjskie Koło Towarzystwa Szkoły Ludowej Muzeum Pokuckiego. W zbiorach tej placówki znalazły się m.in. eksponaty zoologiczne, geologiczne, etnograficzne oraz obiekty pochodzące z kolekcji Edmunda hr. Starzeńskiego, przekazane przez Bronisławę hr. Starzeńską po śmierci męża.\n\nPisał artykuły do prasy lokalnej i krajoznawczej m.in do: „Kosmosu”, „Pamiętników Towarzystwa Tatrzańskiego”, „Naszych Zdrojów”, „Ziemi” i „Gazety Kołomyjskiej”.\n\nW latach I wojny światowej był oficerem cesarskiej i królewskiej armii, a od 1919 roku Wojska Polskiego. Od 1920 roku w Grudziądzu, gdzie był oficerem 64 pułku piechoty. W 1922 roku zweryfikowany został w stopniu majora ze starszeństwem z dniem 1 czerwca 1919 roku, w korpusie oficerów zawodowych piechoty i mianowany referentem do spraw kultury i oświaty w Dowództwie Okręgu Korpusu Nr VIII w Toruniu. Z dniem 1 listopada 1922 roku został przydzielony do Powiatowej Komendy Uzupełnień Grudziądz na stanowisko komendanta. Z dniem 1 marca 1927 roku został mu udzielony dwumiesięczny urlop z zachowaniem uposażenia, a z dniem 30 kwietnia 1927 roku został przeniesiony w stan spoczynku. W 1934 roku pozostawał na ewidencji Powiatowej Komendy Uzupełnień Grudziądz z przydziałem mobilizacyjnym do Oficerskiej Kadry Okręgowej Nr VIII. W tym czasie przewidziany był do użycia w czasie wojny.\n\nTakże na Pomorzu współpracował z czasopismami geograficznymi, krajoznawczymi i regionalnymi, był współorganizatorem grudziądzkiego oddziału Polskiego Towarzystwa Krajoznawczego. Piastował stanowisko referenta turystyczno-krajoznawczego Rozgłośni Polskiego Radia w Toruniu (zał. 1935). Fotografował m.in. zabytki Grudziądza i Torunia, a także przyrodę Beskidów Wschodnich. Wiele fotografii wydano w formie widokówek. Od 1934 prezes Towarzystwa Fotograficznego „Słońce” w Grudziądzu. Opublikował m.in. Przewodnik po Beskidach Wschodnich, t. 1-2, Lwów 1933–1935 (reprint Kielce 2000). Brał udział w pracach kartograficznych na zlecenie Wojskowego Instytutu Geograficznego.\n\nLata II wojny światowej przeżył w Grudziądzu. Pozostawił pamiętnik z walk o miasto w styczniu-lutym 1945 oraz fotograficzną dokumentację zniszczeń. Od 1945 zatrudniony w Archiwum Miejskim. Wykonał kilka tysięcy zdjęć, przechowywanych w Muzeum Miejskim i w zbiorach archiwalnych.";

        $this->person = Person::factory()->create(['biography' => $this->oldBiography]);
    }

    #[TestDox('guests are asked to log in when attempting to view biography form')]
    public function testFormGuest(): void
    {
        get("people/{$this->person->id}/biography")
            ->assertStatus(302)
            ->assertRedirect('login');
    }

    #[TestDox('guests are asked to log in when attempting to view biography form for nonexistent person')]
    public function testFormGuestNonexistent(): void
    {
        $this->get('people/2137/biography')
            ->assertStatus(302)
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view biography form')]
    public function testFormPermissions(): void
    {
        $this->withPermissions(1)
            ->get("people/{$this->person->id}/biography")
            ->assertStatus(403);
    }

    #[TestDox('users with permissions can view biography form')]
    public function testForm(): void
    {
        $this->withPermissions(2)
            ->get("people/{$this->person->id}/biography")
            ->assertStatus(200);
    }

    #[TestDox('guests cannot edit biography')]
    public function testGuest(): void
    {
        patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography])
            ->assertStatus(302)
            ->assertRedirect('login');

        expect($this->person->fresh()->biography)
            ->toBe($this->oldBiography);
    }

    #[TestDox('users without permissions cannot edit biography')]
    public function testPermissions(): void
    {
        $this->withPermissions(1)
            ->patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography])
            ->assertStatus(403);

        expect($this->person->fresh()->biography)
            ->toBe($this->oldBiography);
    }

    #[TestDox('users with permissions can edit biography')]
    public function testOk(): void
    {
        $this->withPermissions(2)
            ->patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography])
            ->assertStatus(302)
            ->assertRedirect("people/{$this->person->id}");

        expect($this->person->fresh()->biography)
            ->toBe($this->newBiography);
    }

    #[TestDox("biography field can't be longer than 10000 characters")]
    public function testValidation(): void
    {
        $this->withPermissions(2)
            ->patch("people/{$this->person->id}/biography", ['biography' => Str::random(10001)])
            ->assertSessionHasErrors(['biography']);

        expect($this->person->fresh()->biography)
            ->toBe($this->oldBiography);
    }

    #[TestDox('biography addition is logged')]
    public function testLogging(): void
    {
        $this->person->fill(['biography' => null])->save();

        $count = Activity::count();

        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography]);

        $this->travelBack();

        $this->person->refresh();

        expect(Activity::count())->toBe($count + 2); // biography addition and user creation

        expect($log = latestLog())
            ->log_name->toBe('people')
            ->description->toBe('added-biography')
            ->subject->toBeModel($this->person);

        expect((string) $log->created_at)->toBe((string) $this->person->updated_at);

        expect($log->properties->all())->toBe([
            'new' => $this->newBiography,
            'old' => null,
        ]);
    }

    #[TestDox('biography edition is logged')]
    public function testLoggingEdit(): void
    {
        $count = Activity::count();

        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography]);

        $this->travelBack();

        $this->person->refresh();

        expect(Activity::count())->toBe($count + 2); // biography edition and user creation

        expect($log = latestLog())
            ->log_name->toBe('people')
            ->description->toBe('updated-biography')
            ->subject->toBeModel($this->person);

        expect((string) $log->created_at)->toBe((string) $this->person->updated_at);

        expect($log->properties->all())->toBe([
            'new' => $this->newBiography,
            'old' => $this->oldBiography,
        ]);
    }

    #[TestDox('biography deletion is logged')]
    public function testLoggingDelete(): void
    {
        $count = Activity::count();

        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->patch("people/{$this->person->id}/biography", ['biography' => null]);

        $this->travelBack();

        $this->person->refresh();

        expect(Activity::count())->toBe($count + 2); // biography deletion and user creation

        expect($log = latestLog())
            ->log_name->toBe('people')
            ->description->toBe('deleted-biography')
            ->subject->toBeModel($this->person);

        expect((string) $log->created_at)->toBe((string) $this->person->updated_at);

        expect($log->properties->all())->toBe([
            'new' => null,
            'old' => $this->oldBiography,
        ]);
    }
}
