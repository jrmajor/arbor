<?php

namespace Tests\Unit;

use App\Marriage;
use App\Person;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PersonTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanDetermineItsVisibility()
    {
        $alive = factory(Person::class)->state('alive')->create();
        $dead = factory(Person::class)->state('dead')->create();

        $alive->visibility = false;
        $this->assertFalse($alive->isVisible());
        $alive->visibility = true;
        $this->assertTrue($alive->isVisible());

        $dead->visibility = false;
        $this->assertTrue($dead->isVisible());
        $dead->visibility = true;
        $this->assertTrue($dead->isVisible());
    }

    public function testTellsIfCanBeViewedByGivenUser()
    {
        $user = factory(User::class)->create();
        $person = factory(Person::class)->state('alive')->create();

        $this->assertFalse($person->canBeViewedBy($user));
        $user->permissions = 1;
        $this->assertTrue($person->canBeViewedBy($user));

        $this->assertFalse($person->canBeViewedBy(null));
    }

    public function testCanGetMother()
    {
        $mother = factory(Person::class)->state('woman')->create();
        $person = factory(Person::class)->create(['mother_id' => $mother->id]);

        $this->assertEquals($mother->id, $person->mother->id);
    }

    public function testCanGetFather()
    {
        $father = factory(Person::class)->state('man')->create();
        $person = factory(Person::class)->create(['father_id' => $father->id]);

        $this->assertEquals($father->id, $person->father->id);
    }

    public function testCanGetSiblingsAndHalfSiblings()
    {
        $person = $this->personWithParents();

        factory(Person::class, 2)->create([
            'mother_id' => $person->mother_id,
            'father_id' => $person->father_id,
        ]);

        factory(Person::class, 1)->create([
            'mother_id' => $person->mother_id,
            'father_id' => factory(Person::class)->state('man')->create(),
        ]);
        factory(Person::class, 2)->create([
            'mother_id' => $person->mother_id,
            'father_id' => null,
        ]);

        factory(Person::class, 3)->create([
            'mother_id' => factory(Person::class)->state('woman')->create(),
            'father_id' => $person->father_id,
        ]);
        factory(Person::class, 1)->create([
            'mother_id' => null,
            'father_id' => $person->father_id,
        ]);

        $this->assertCount(2, $person->siblings);

        $this->assertCount(3, $person->siblings_mother);

        $this->assertCount(4, $person->siblings_father);
    }

    public function testCanGetMarriages()
    {
        $person = factory(Person::class)->state('woman')->create();

        factory(Person::class, 3)->state('man')->create()
            ->each(function ($partner) use ($person){
                factory(Marriage::class)->create([
                    'woman_id' => $person->id,
                    'man_id' => $partner->id,
                ]);
            });

        $this->assertCount(3, $person->marriages);
    }

    public function testCanGetChildren()
    {
        $father = factory(Person::class)->state('man')->create();

        factory(Person::class, 2)->state('woman')->create()
            ->each(function ($mother) use ($father){
                factory(Person::class)->create([
                    'mother_id' => $mother->id,
                    'father_id' => $father->id,
                ]);
            });

        $child = factory(Person::class)->create([
            'mother_id' => null,
            'father_id' => $father->id,
        ]);

        $this->assertCount(3, $father->children);
        $this->assertTrue($father->children->contains($child));
    }

    public function testYearGettersWork()
    {
        $personWithDates = factory(Person::class)->state('dead')->create([
            'birth_date_from' => '1957-05-20',
            'birth_date_to' => '1957-05-20',
            'death_date_from' => '2020-11-09',
            'death_date_to' => '2020-11-09',
        ]);
        $personWithSomeDates = factory(Person::class)->state('dead')->create([
            'birth_date_from' => '1893-01-01',
            'birth_date_to' => '1893-12-31',
            'death_date_from' => '1944-08-01',
            'death_date_to' => '1944-08-31',
        ]);
        $personWithoutDates = factory(Person::class)->create([
            'birth_date_from' => null,
            'birth_date_to' => null,
            'death_date_from' => null,
            'death_date_to' => null,
        ]);

        $this->assertEquals(1957, $personWithDates->birth_year);
        $this->assertEquals(2020, $personWithDates->death_year);

        $this->assertEquals(1893, $personWithSomeDates->birth_year);
        $this->assertEquals(1944, $personWithSomeDates->death_year);

        $this->assertNull($personWithoutDates->birth_year);
        $this->assertNull($personWithoutDates->death_year);
    }

    public function testReturnsNullWhenCalculatingAgeWithoutDate()
    {
        $person = factory(Person::class)->create([
            'birth_date_from' => null,
            'birth_date_to' => null,
        ]);

        $at = Carbon::create(2019, 8, 15);

        $this->assertNull($person->age($at, true));
        $this->assertNull($person->age($at));
    }

    public function testCanCalculateAgeWithCompleteDates()
    {
        $person = factory(Person::class)->create([
            'birth_date_from' => '1994-06-22',
            'birth_date_to' => '1994-06-22',
        ]);

        $at = Carbon::create(2019, 8, 15);

        $this->assertEquals(25, $person->age($at, true));
        $this->assertEquals(25, $person->age($at));
    }

    public function testCanCalculateAgeWithIncompleteBirthDate()
    {
        $person_without_day = factory(Person::class)->create([
            'birth_date_from' => '1978-04-01',
            'birth_date_to' => '1978-04-30',
        ]);

        $person_without_month = factory(Person::class)->create([
            'birth_date_from' => '1982-01-01',
            'birth_date_to' => '1982-12-31',
        ]);

        $at_diffrent_month = Carbon::create(2017, 6, 15);

        $at_same_month = Carbon::create(2006, 4, 16);

        $this->assertEquals(39, $person_without_day->age($at_diffrent_month, true));
        $this->assertEquals(39, $person_without_day->age($at_diffrent_month));
        $this->assertEquals(27, $person_without_day->age($at_same_month, true)); // 27-28
        $this->assertEquals('27-28', $person_without_day->age($at_same_month));
        $this->assertEquals(34, $person_without_month->age($at_diffrent_month, true)); // 34-35
        $this->assertEquals('34-35', $person_without_month->age($at_diffrent_month));
    }

    public function testCanCalculateAgeWithIncompleteAtDate()
    {
        $person = factory(Person::class)->create([
            'birth_date_from' => '1975-03-22',
            'birth_date_to' => '1975-03-22',
        ]);

        $without_day = [Carbon::create(2013, 7, 01), Carbon::create(2013, 7, 31)];

        $without_day_in_same_month = [Carbon::create(2015, 3, 01), Carbon::create(2015, 3, 31)];

        $without_month = [Carbon::create(2016, 01, 01), Carbon::create(2016, 12, 31)];

        $this->assertEquals(38, $person->age($without_day, true));
        $this->assertEquals(38, $person->age($without_day));
        $this->assertEquals(39, $person->age($without_day_in_same_month, true)); // 39-40
        $this->assertEquals('39-40', $person->age($without_day_in_same_month));
        $this->assertEquals(40, $person->age($without_month, true)); // 40-41
        $this->assertEquals('40-41', $person->age($without_month));
    }

    public function testCanCalculateAgeWithIncompleteDates()
    {
        $person = factory(Person::class)->create([
            'birth_date_from' => '1992-01-01',
            'birth_date_to' => '1992-12-31',
        ]);

        $at = [Carbon::create(2010, 7, 01), Carbon::create(2010, 7, 31)];

        $this->assertEquals(17, $person->age($at, true)); // 17-18
        $this->assertEquals('17-18', $person->age($at));
    }

    public function testCanCalculateCurrentAge()
    {
        $person = factory(Person::class)->create([
            'birth_date_from' => '1973-05-12',
            'birth_date_to' => '1973-05-12',
        ]);

        $today = Carbon::create('2016-11-10');
        Carbon::setTestNow($today);

        $this->assertEquals('2016-11-10', Carbon::now()->format('Y-m-d'));
        $this->assertEquals(43, $person->currentAge(true));
        $this->assertEquals(43, $person->currentAge());

        Carbon::setTestNow();
    }

    public function testCanCalculateAgeAgeAtDeath()
    {
        $person = factory(Person::class)->create([
            'birth_date_from' => '1874-04-08',
            'birth_date_to' => '1874-04-08',
            'death_date_from' => '1941-05-30',
            'death_date_to' => '1941-05-30',
        ]);

        $this->assertEquals(67, $person->ageAtDeath(true));
        $this->assertEquals(67, $person->ageAtDeath());
    }

    public function testCanFormatName()
    {
        $person = factory(Person::class)->state('alive')->create([
            'name' => 'Zenona',
            'middle_name' => 'Ludmiła',
            'family_name' => 'Skwierczyńska',
            'last_name' => null,
            'birth_date_from' => null,
            'birth_date_to' => null,
        ]);

        $this->assertEquals("Zenona Skwierczyńska [№$person->id]", $person->formatName());

        $person->last_name = 'Wojtyła';
        $this->assertEquals("Zenona Wojtyła (z d. Skwierczyńska) [№$person->id]", $person->formatName());
        $person->last_name = null;

        $person->birth_date_from = '1913-05-01';
        $person->birth_date_to = '1913-05-01';
        $this->assertEquals("Zenona Skwierczyńska (∗1913) [№$person->id]", $person->formatName());

        $person->dead = true;
        $person->death_date_from = '1945-01-01';
        $person->death_date_to = '1945-12-31';
        $this->assertEquals("Zenona Skwierczyńska (∗1913, ✝1945) [№$person->id]", $person->formatName());

        $person->birth_date_from = null;
        $person->birth_date_to = null;
        $this->assertEquals("Zenona Skwierczyńska (✝1945) [№$person->id]", $person->formatName());
    }

    public function testCanFindByPytlewskiId()
    {
        $person = factory(Person::class)->create([
            'id_pytlewski' => 1140,
        ]);

        $this->assertTrue($person->is(Person::findByPytlewskiId(1140)));
        $this->assertNull(Person::findByPytlewskiId(null));
        $this->assertNull(Person::findByPytlewskiId(2137));
    }

    public function testCanListFirstLetters()
    {
        collect([
            [
                'family_name' => 'Šott',
                'last_name' => null,
            ],
            [
                'family_name' => 'Żygowska',
                'last_name' => 'Šott',
            ],
            [
                'family_name' => 'Mazowiecki',
                'last_name' => null,
            ],
            [
                'family_name' => 'Major',
                'last_name' => 'Hoffman',
            ],
        ])->each(function ($names) {
            factory(Person::class)->create($names);
        });

        $this->assertEquals(
            [
                ['letter' => 'M', 'total' => 2],
                ['letter' => 'Š', 'total' => 1],
                ['letter' => 'Ż', 'total' => 1],
            ],
            Person::letters('family')->map(fn($std) => (array) $std)->toArray()
        );

        $this->assertEquals(
            [
                ['letter' => 'H', 'total' => 1],
                ['letter' => 'M', 'total' => 1],
                ['letter' => 'Š', 'total' => 2],
            ],
            Person::letters('last')->map(fn($std) => (array) $std)->toArray()
        );
    }
}
