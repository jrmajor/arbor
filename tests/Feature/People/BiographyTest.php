<?php

use App\Person;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->oldBiography = "Ukończył gimnazjum w Kołomyi i studia wyższe (polonistyka, historia, geografia), zapewne na Uniwersytecie Lwowskim. Pracował jako nauczyciel gimnazjalny w Kołomyi, gdzie był działaczem Sokoła, Towarzystwa Szkoły Ludowej i Polskiego Towarzystwa Tatrzańskiego. Zajmował się organizacją Muzeum Pokuckiego i kolekcjonował huculską sztukę ludową. Pisał artykuły do prasy lokalnej i krajoznawczej. W latach I wojny światowej był oficerem armii astriackiej, od 1919 Wojska Polskiego. Od 1920 w Grudziądzu, gdzie był oficerem 64 pułku piechoty, 1922 referent ds. kultury i oświaty DOK VIII w Toruniu, następnie komendant Powiatowej Komendy Uzupełnień w Grudziądzu. W 1927 przeszedł na emeryturę w stopniu majora.";

    $this->newBiography = "Ukończył w 1893 roku c.k. Wyższe Gimnazjum w Stanisławowie a w 1898 roku Wyższe Seminarium (nauczycielskie) we Lwowie. W latach 1899–1900 odbył służbę wojskowa w Wiedniu. W 1903 zdobył patent nauczycielski Szkół Ludowych Pospolitych. Od 1907 pracował jako nauczyciel gimnastyki w Gimnazjum oraz w Szkole Męskiej im. Jachowicza w Kołomyi. Był działaczem Sokoła, Towarzystwa Szkoły Ludowej i Polskiego Towarzystwa Tatrzańskiego.\n\nOd 1903 kolekcjonował huculską sztukę ludową a od 1910 zajmował się organizacją reaktywowanego przez Kołomyjskie Koło Towarzystwa Szkoły Ludowej Muzeum Pokuckiego. W zbiorach tej placówki znalazły się m.in. eksponaty zoologiczne, geologiczne, etnograficzne oraz obiekty pochodzące z kolekcji Edmunda hr. Starzeńskiego, przekazane przez Bronisławę hr. Starzeńską po śmierci męża.\n\nPisał artykuły do prasy lokalnej i krajoznawczej m.in do: „Kosmosu”, „Pamiętników Towarzystwa Tatrzańskiego”, „Naszych Zdrojów”, „Ziemi” i „Gazety Kołomyjskiej”.\n\nW latach I wojny światowej był oficerem cesarskiej i królewskiej armii, a od 1919 roku Wojska Polskiego. Od 1920 roku w Grudziądzu, gdzie był oficerem 64 pułku piechoty. W 1922 roku zweryfikowany został w stopniu majora ze starszeństwem z dniem 1 czerwca 1919 roku, w korpusie oficerów zawodowych piechoty i mianowany referentem do spraw kultury i oświaty w Dowództwie Okręgu Korpusu Nr VIII w Toruniu. Z dniem 1 listopada 1922 roku został przydzielony do Powiatowej Komendy Uzupełnień Grudziądz na stanowisko komendanta. Z dniem 1 marca 1927 roku został mu udzielony dwumiesięczny urlop z zachowaniem uposażenia, a z dniem 30 kwietnia 1927 roku został przeniesiony w stan spoczynku. W 1934 roku pozostawał na ewidencji Powiatowej Komendy Uzupełnień Grudziądz z przydziałem mobilizacyjnym do Oficerskiej Kadry Okręgowej Nr VIII. W tym czasie przewidziany był do użycia w czasie wojny.\n\nTakże na Pomorzu współpracował z czasopismami geograficznymi, krajoznawczymi i regionalnymi, był współorganizatorem grudziądzkiego oddziału Polskiego Towarzystwa Krajoznawczego. Piastował stanowisko referenta turystyczno-krajoznawczego Rozgłośni Polskiego Radia w Toruniu (zał. 1935). Fotografował m.in. zabytki Grudziądza i Torunia, a także przyrodę Beskidów Wschodnich. Wiele fotografii wydano w formie widokówek. Od 1934 prezes Towarzystwa Fotograficznego „Słońce” w Grudziądzu. Opublikował m.in. Przewodnik po Beskidach Wschodnich, t. 1-2, Lwów 1933–1935 (reprint Kielce 2000). Brał udział w pracach kartograficznych na zlecenie Wojskowego Instytutu Geograficznego.\n\nLata II wojny światowej przeżył w Grudziądzu. Pozostawił pamiętnik z walk o miasto w styczniu-lutym 1945 oraz fotograficzną dokumentację zniszczeń. Od 1945 zatrudniony w Archiwum Miejskim. Wykonał kilka tysięcy zdjęć, przechowywanych w Muzeum Miejskim i w zbiorach archiwalnych.";

    $this->person = factory(Person::class)->create(['biography' => $this->oldBiography]);
});

test('guests are asked to log in when attempting to view biography form', function () {
    get("people/{$this->person->id}/biography")
        ->assertStatus(302)
        ->assertRedirect('login');
});

test('guests are asked to log in when attempting to view biography form for nonexistent person')
    ->get('people/2137/biography')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view biography form', function () {
    withPermissions(1)
        ->get("people/{$this->person->id}/biography")
        ->assertStatus(403);
});

test('users with permissions can view biography form', function () {
    withPermissions(2)
        ->get("people/{$this->person->id}/biography")
        ->assertStatus(200);
});

test('guests cannot edit biography', function () {
    patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography])
        ->assertStatus(302)
        ->assertRedirect('login');

    assertEquals($this->oldBiography, $this->person->fresh()->biography);
});

test('users without permissions cannot edit biography', function () {
    withPermissions(1)
        ->patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography])
        ->assertStatus(403);

    assertEquals($this->oldBiography, $this->person->fresh()->biography);
});

test('users with permissions can edit biography', function () {
    withPermissions(2)
        ->patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography])
        ->assertStatus(302)
        ->assertRedirect("people/{$this->person->id}");

    assertEquals($this->newBiography, $this->person->fresh()->biography);
});

test('biography filed can\'t be longer than 10000 characters', function () {
    withPermissions(2)
        ->patch("people/{$this->person->id}/biography", ['biography' => Str::random(10001)])
        ->assertSessionHasErrors(['biography']);

    assertEquals($this->oldBiography, $this->person->fresh()->biography);
});

test('biography addition is logged', function () {
    $this->person->fill(['biography' => null])->save();

    $count = Activity::count();

    travel('+1 minute');

    withPermissions(2)
        ->patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography]);

    travel('back');

    $person = $this->person->fresh();

    assertEquals($count + 2, Activity::count()); // visibility change and user creation

    $log = latestLog();

    assertEquals('people', $log->log_name);
    assertEquals('added-biography', $log->description);
    assertTrue($person->is($log->subject));

    assertEquals($person->updated_at, $log->created_at);

    assertCount(2, $log->properties);

    assertNull($log->properties['old']);
    assertIsString($log->properties['new']);

    assertEquals($this->newBiography, $log->properties['new']);
});

test('biography edition is logged', function () {
    $count = Activity::count();

    travel('+1 minute');

    withPermissions(2)
        ->patch("people/{$this->person->id}/biography", ['biography' => $this->newBiography]);

    travel('back');

    $person = $this->person->fresh();

    assertEquals($count + 2, Activity::count()); // visibility change and user creation

    $log = latestLog();

    assertEquals('people', $log->log_name);
    assertEquals('updated-biography', $log->description);
    assertTrue($person->is($log->subject));

    assertEquals($person->updated_at, $log->created_at);

    assertCount(2, $log->properties);

    assertIsString($log->properties['old']);
    assertIsString($log->properties['new']);

    assertEquals($this->oldBiography, $log->properties['old']);
    assertEquals($this->newBiography, $log->properties['new']);
});

test('biography deletion is logged', function () {
    $count = Activity::count();

    travel('+1 minute');

    withPermissions(2)
        ->patch("people/{$this->person->id}/biography", ['biography' => null]);

    travel('back');

    $person = $this->person->fresh();

    assertEquals($count + 2, Activity::count()); // visibility change and user creation

    $log = latestLog();

    assertEquals('people', $log->log_name);
    assertEquals('deleted-biography', $log->description);
    assertTrue($person->is($log->subject));

    assertEquals($person->updated_at, $log->created_at);

    assertCount(2, $log->properties);

    assertIsString($log->properties['old']);
    assertNull($log->properties['new']);

    assertEquals($this->oldBiography, $log->properties['old']);
});
