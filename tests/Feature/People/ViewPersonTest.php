<?php

namespace Tests\Feature\People;

use App\Models\Marriage;
use App\Models\Person;
use App\Services\Pytlewski\PytlewskiScraper;
use App\Services\Wielcy\WielcyScraper;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\TestDox;
use Psl\File;
use Tests\TestCase;

final class ViewPersonTest extends TestCase
{
    #[TestDox('guest cannot see hidden alive person')]
    public function testGuestHiddenAlive(): void
    {
        $person = Person::factory()->alive()->create();

        $this->get("people/{$person->id}")
            ->assertForbidden();
    }

    #[TestDox('guest cannot see hidden dead person')]
    public function testGuestHiddenDead(): void
    {
        $person = Person::factory()->dead()->create();

        $this->get("people/{$person->id}")
            ->assertForbidden();
    }

    #[TestDox('guest can see visible alive person')]
    public function testGuestVisibleAlive(): void
    {
        $person = Person::factory()->alive()->create([
            'visibility' => true,
        ]);

        $this->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('guest can see visible dead person')]
    public function testGuestVisibleDead(): void
    {
        $person = Person::factory()->dead()->create([
            'visibility' => true,
        ]);

        $this->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('user with permissions can see hidden alive person')]
    public function testUserHiddenAlive(): void
    {
        $person = Person::factory()->alive()->create();

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('user with permissions can see hidden dead person')]
    public function testUserHiddenDead(): void
    {
        $person = Person::factory()->dead()->create();

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('user with permissions can see visible alive person')]
    public function testUserVisibleAlive(): void
    {
        $person = Person::factory()->alive()->create([
            'visibility' => true,
        ]);

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('user with permissions can see visible dead person')]
    public function testUserVisibleDead(): void
    {
        $person = Person::factory()->dead()->create([
            'visibility' => true,
        ]);

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk();
    }

    #[TestDox('it shows person with multiple marriages without lazy loading')]
    public function testMultipleMarriages(): void
    {
        $person = Person::factory()->male()->create([
            'visibility' => true,
            'birth_date_from' => '1900-01-01',
            'birth_date_to' => '1900-01-01',
        ]);

        Marriage::factory()->count(2)->create(['man_id' => $person->id]);

        $this->get("people/{$person->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('person.marriages', 2)
                ->etc());

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('person.marriages', 2)
                ->etc());
    }

    #[TestDox('it shows parent ids of children')]
    public function testChildrenParentIds(): void
    {
        $person = Person::factory()->male()->create(['visibility' => true]);
        $wife = Person::factory()->female()->create();

        Person::factory()->create([
            'father_id' => $person->id,
            'mother_id' => $wife->id,
            'visibility' => true,
            'birth_date_from' => '1930-01-01',
            'birth_date_to' => '1930-01-01',
        ]);

        Person::factory()->create([
            'father_id' => $person->id,
            'mother_id' => null,
            'birth_date_from' => '1940-01-01',
            'birth_date_to' => '1940-01-01',
        ]);

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('person.children', 2)
                ->where('person.children.0.fatherId', $person->id)
                ->where('person.children.0.motherId', $wife->id)
                ->where('person.children.1.fatherId', $person->id)
                ->where('person.children.1.motherId', null)
                ->etc());
    }

    #[TestDox('it shows parent ids of hidden children')]
    public function testHiddenChildrenParentIds(): void
    {
        $person = Person::factory()->male()->create(['visibility' => true]);
        $wife = Person::factory()->female()->create();

        Person::factory()->create([
            'father_id' => $person->id,
            'mother_id' => $wife->id,
        ]);

        $this->get("people/{$person->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('person.children.0.visible', false)
                ->where('person.children.0.fatherId', $person->id)
                ->where('person.children.0.motherId', $wife->id)
                ->etc());
    }

    #[TestDox('it counts siblings born before person')]
    public function testSiblingsBefore(): void
    {
        $father = Person::factory()->male()->create();
        $mother = Person::factory()->female()->create();

        $createChild = fn (?string $from, ?string $to = null) => Person::factory()->create([
            'father_id' => $father->id,
            'mother_id' => $mother->id,
            'visibility' => true,
            'birth_date_from' => $from,
            'birth_date_to' => $to ?? $from,
        ]);

        $createChild(null);
        $createChild('1890-01-01');
        $createChild('1895-01-01');
        $createChild('1900-01-01', '1910-12-31');
        $createChild('1920-01-01');

        $person = $createChild('1895-01-01');
        $unknown = $createChild(null);

        $this->get("people/{$person->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('person.siblings', 6)
                ->where('person.siblingsBefore', 4)
                ->etc());

        $this->get("people/{$unknown->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('person.siblingsBefore', 1)
                ->etc());
    }

    #[TestDox('guest see 404 when attempting to view nonexistent person')]
    public function testGuestNonexistent(): void
    {
        $this->get('people/1')
            ->assertNotFound();
    }

    #[TestDox('user with insufficient permissions see 404 when attempting to view nonexistent person')]
    public function testPermissionsNonexistent(): void
    {
        $this->withPermissions(1)
            ->get('people/1')
            ->assertNotFound();
    }

    #[TestDox('it shows data scraped from external sources')]
    public function testExternalSources(): void
    {
        Http::fake([
            PytlewskiScraper::url(556) => Http::response(File\read(__DIR__ . '/../../Datasets/Pytlewscy/556.html')),
            WielcyScraper::url('psb.6305.1') => Http::response(File\read(__DIR__ . '/../../Datasets/Wielcy/psb.6305.1.html')),
        ]);

        $person = Person::factory()->create([
            'id_pytlewski' => 556,
            'id_wielcy' => 'psb.6305.1',
        ]);

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('person.pytlewskiUrl', PytlewskiScraper::url(556))
                ->where('person.pytlewski.familyName', 'Major')
                ->where('person.wielcyId', 'psb.6305.1')
                ->where('person.wielcyUrl', WielcyScraper::url('psb.6305.1'))
                ->where('person.wielcy.surname', 'Gąsiorowski')
                ->where('person.wielcy.father.name', 'Leodgard')
                ->etc());
    }

    #[TestDox('it links external sources when they are unavailable')]
    public function testExternalSourcesUnavailable(): void
    {
        Http::fake([
            PytlewskiScraper::url(556) => Http::failedConnection(),
            WielcyScraper::url('psb.6305.1') => Http::response(status: 500),
        ]);

        $person = Person::factory()->create([
            'id_pytlewski' => 556,
            'id_wielcy' => 'psb.6305.1',
        ]);

        $this->withPermissions(1)
            ->get("people/{$person->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('person.pytlewskiUrl', PytlewskiScraper::url(556))
                ->where('person.pytlewski', null)
                ->where('person.wielcyId', 'psb.6305.1')
                ->where('person.wielcyUrl', WielcyScraper::url('psb.6305.1'))
                ->where('person.wielcy', null)
                ->etc());
    }

    #[TestDox('guest see 404 when attempting to view deleted person')]
    public function testGuestDeleted(): void
    {
        $person = Person::factory()->create(['deleted_at' => now()]);

        $this->get("people/{$person->id}")
            ->assertNotFound();
    }

    #[TestDox('users without permissions see 404 when attempting to view deleted person')]
    public function testPermissionsDeleted(): void
    {
        $person = Person::factory()->create(['deleted_at' => now()]);

        $this->withPermissions(2)
            ->get("people/{$person->id}")
            ->assertNotFound();
    }

    #[TestDox('user with permissions are redirected to edits history when attempting to view deleted person')]
    public function testRedirectDeleted(): void
    {
        $person = Person::factory()->create(['deleted_at' => now()]);

        $this->withPermissions(3)
            ->get("people/{$person->id}")
            ->assertFound()
            ->assertRedirect("people/{$person->id}/history");
    }
}
