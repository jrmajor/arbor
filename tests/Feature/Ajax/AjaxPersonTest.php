<?php

namespace Tests\Feature\Ajax;

use App\Person;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AjaxPersonTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestsCannotAccessEndpoint()
    {
        $response = $this->postJson('/ajax/person', [
            'search' => 'ezechiel',
        ]);

        $response->assertStatus(401);
    }

    public function testItReturnsCorrectJson()
    {
        $people = factory(Person::class, 2)->state('man')->create(['name' => 'Gwidon']);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'search' => 'gwi',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertExactJson([
            ['id' => $people[0]->id, 'name' => $people[0]->formatName(),],
            ['id' => $people[1]->id, 'name' => $people[1]->formatName(),],
        ]);

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xx',
            'search' => 'gwi',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(0);
        $response->assertExactJson([]);
    }

    public function testDoesntReturnMoreThan_10People()
    {
        factory(Person::class, 15)->state('man')->create(['name' => 'Gwidon']);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xy',
            'search' => 'gwi',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(10);
    }

    public function testUserCanSearchById()
    {
        $person = factory(Person::class)->state('woman')->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xx',
            'search' => $person->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function testUsersCanSearchByIdAndSex()
    {
        $person = factory(Person::class)->state('woman')->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xx',
            'search' => $person->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xy',
            'search' => $person->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }

    public function testWhenUsersCanSearchBySexItCanBeNull()
    {
        $person = factory(Person::class)->state('woman')->create([
            'name' => 'Bronisława',
        ]);
        $person = factory(Person::class)->state('man')->create([
            'name' => 'Bronisław',
            'sex' => null,
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xx',
            'search' => 'bron',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function testUserCanSearchByName()
    {
        factory(Person::class)->state('woman')->create([
            'name' => 'Czesława',
            'family_name' => 'x',
            'last_name' => 'x',
        ]);
        factory(Person::class)->state('man')->create([
            'name' => 'Czesław',
            'family_name' => 'x',
            'last_name' => 'x',
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'search' => 'czesl',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function testUserCanSearchByNameAndSex()
    {
        factory(Person::class)->state('woman')->create([
            'name' => 'Czesława',
            'family_name' => 'x',
            'last_name' => 'x',
        ]);
        factory(Person::class)->state('man')->create([
            'name' => 'Czesław',
            'family_name' => 'x',
            'last_name' => 'x',
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xx',
            'search' => 'czesl',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function testUserCanSearchByFamilyName()
    {
        factory(Person::class)->state('woman')->create([
            'name' => 'x',
            'family_name' => 'Zbyrowska',
            'last_name' => 'x',
        ]);
        factory(Person::class)->state('man')->create([
            'name' => 'x',
            'family_name' => 'Zbyrowski',
            'last_name' => 'x',
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'search' => 'zb',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function testUserCanSearchByFamilyNameAndSex()
    {
        factory(Person::class)->state('woman')->create([
            'name' => 'x',
            'family_name' => 'Zbyrowska',
            'last_name' => 'x',
        ]);
        factory(Person::class)->state('man')->create([
            'name' => 'x',
            'family_name' => 'Zbyrowski',
            'last_name' => 'x',
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xy',
            'search' => 'zb',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function testUserCanSearchByLastName()
    {
        factory(Person::class)->state('woman')->create([
            'name' => 'x',
            'family_name' => 'x',
            'last_name' => 'Charzyńska'
        ]);
        factory(Person::class)->state('man')->create([
            'name' => 'x',
            'family_name' => 'x',
            'last_name' => 'Charzyński'
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'search' => 'charz',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function testUserCanSearchByLastNameAndSex()
    {
        factory(Person::class)->state('woman')->create([
            'name' => 'x',
            'family_name' => 'x',
            'last_name' => 'Pytlewska'
        ]);
        factory(Person::class)->state('man')->create([
            'name' => 'x',
            'family_name' => 'x',
            'last_name' => 'Pytlewski'
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xy',
            'search' => 'pyt',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function testUserCanSearchByNameAndFamilyName()
    {
        factory(Person::class)->state('woman')->create([
            'name' => 'Krystyna',
            'family_name' => 'Charzyńska',
            'last_name' => null,
        ]);
        factory(Person::class)->state('man')->create([
            'name' => 'Krystian',
            'family_name' => 'Charzyński',
            'last_name' => null,
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'search' => 'kry charz',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function testUserCanSearchByNameLastNameAndSex()
    {
        factory(Person::class)->state('woman')->create([
            'name' => 'Franciszka',
            'family_name' => 'Pytlewska',
            'last_name' => null,
        ]);
        factory(Person::class)->state('man')->create([
            'name' => 'Franciszek',
            'family_name' => 'Pytlewski',
            'last_name' => null,
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/ajax/person', [
            'sex' => 'xy',
            'search' => 'fran pyt',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }
}
