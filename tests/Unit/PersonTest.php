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

    public function testDateGettersWork()
    {
        $personWithDates = factory(Person::class)->state('dead')->create([
            'birth_date' => '1957-05-20',
            'death_date' => '2020-11-09',
        ]);
        $personWithSomeDates = factory(Person::class)->state('dead')->create([
            'birth_date' => '1893-00-00',
            'death_date' => '1944-08-00',
        ]);
        $personWithoutDates = factory(Person::class)->create([
            'birth_date' => null,
            'death_date' => null,
        ]);

        $this->assertEquals($personWithDates->birth_year, 1957);
        $this->assertEquals($personWithDates->birth_month, 5);
        $this->assertEquals($personWithDates->birth_day, 20);
        $this->assertEquals($personWithDates->death_year, 2020);
        $this->assertEquals($personWithDates->death_month, 11);
        $this->assertEquals($personWithDates->death_day, 9);

        $this->assertEquals($personWithSomeDates->birth_year, 1893);
        $this->assertNull($personWithSomeDates->birth_month);
        $this->assertNull($personWithSomeDates->birth_day);
        $this->assertEquals($personWithSomeDates->death_year, 1944);
        $this->assertEquals($personWithSomeDates->death_month, 8);
        $this->assertNull($personWithSomeDates->death_day);

        $this->assertNull($personWithoutDates->birth_year);
        $this->assertNull($personWithoutDates->birth_month);
        $this->assertNull($personWithoutDates->birth_day);
        $this->assertNull($personWithoutDates->death_year);
        $this->assertNull($personWithoutDates->death_month);
        $this->assertNull($personWithoutDates->death_day);
    }

    public function testReturnsNullWhenCalculatingAgeWithoutDate()
    {
        $person = factory(Person::class)->create([
            'birth_date' => null,
        ]);

        $at = [
            'y' => 2019,
            'm' => 8,
            'd' => 15,
        ];

        $this->assertNull($person->age($at, true));
        $this->assertNull($person->age($at));
    }

    public function testCanCalculateAgeWithCompleteDates()
    {
        $person = factory(Person::class)->create([
            'birth_date' => '1994-06-22',
        ]);

        $at = [
            'y' => 2019,
            'm' => 8,
            'd' => 15,
        ];

        $this->assertEquals(25, $person->age($at, true));
        $this->assertEquals(25, $person->age($at));
    }

    public function testCanCalculateAgeWithIncompleteBirthDate()
    {
        $person_without_day = factory(Person::class)->create([
            'birth_date' => '1978-04-00',
        ]);

        $person_without_month = factory(Person::class)->create([
            'birth_date' => '1982-00-00',
        ]);

        $at_diffrent_month = [
            'y' => 2017,
            'm' => 6,
            'd' => 15,
        ];

        $at_same_month = [
            'y' => 2006,
            'm' => 4,
            'd' => 16,
        ];

        $this->assertEquals(39, $person_without_day->age($at_diffrent_month, true));
        $this->assertEquals(39, $person_without_day->age($at_diffrent_month));
        $this->assertEquals(28, $person_without_day->age($at_same_month, true)); // 27-28
        $this->assertEquals('~28', $person_without_day->age($at_same_month)); // 27-28
        $this->assertEquals(35, $person_without_month->age($at_diffrent_month, true)); // 34-35
        $this->assertEquals('~35', $person_without_month->age($at_diffrent_month)); // 34-35
    }

    public function testCanCalculateAgeWithIncompleteAtDate()
    {
        $person = factory(Person::class)->create([
            'birth_date' => '1975-03-22',
        ]);

        $without_day = [
            'y' => 2013,
            'm' => 7,
            'd' => null,
        ];

        $without_day_in_same_month = [
            'y' => 2015,
            'm' => 3,
            'd' => null,
        ];

        $without_month = [
            'y' => 2016,
            'm' => null,
            'd' => null,
        ];

        $this->assertEquals(38, $person->age($without_day, true));
        $this->assertEquals(38, $person->age($without_day));
        $this->assertEquals(40, $person->age($without_day_in_same_month, true)); // 39-40
        $this->assertEquals('~40', $person->age($without_day_in_same_month)); // 39-40
        $this->assertEquals(41, $person->age($without_month, true)); // 40-41
        $this->assertEquals('~41', $person->age($without_month)); // 40-41
    }

    public function testCanCalculateAgeWithIncompleteDates()
    {
        $person = factory(Person::class)->create([
            'birth_date' => '1992',
        ]);

        $at = [
            'y' => 2010,
            'm' => 7,
            'd' => null,
        ];

        $this->assertEquals(18, $person->age($at, true)); // 17-18
        $this->assertEquals('~18', $person->age($at)); // 17-18
    }

    public function testCanCalculateCurrentAge()
    {
        $person = factory(Person::class)->create([
            'birth_date' => '1973-05-12',
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
            'birth_date' => '1874-04-08',
            'death_date' => '1941-05-30',
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
            'birth_date' => null,
        ]);

        $this->assertEquals("Zenona Skwierczyńska [№$person->id]", $person->formatName());

        $person->last_name = 'Wojtyła';
        $this->assertEquals("Zenona Wojtyła (z d. Skwierczyńska) [№$person->id]", $person->formatName());
        $person->last_name = null;

        $person->birth_date = '1913-05-01';
        $this->assertEquals("Zenona Skwierczyńska (∗1913) [№$person->id]", $person->formatName());

        $person->dead = true;
        $person->death_date = '1945-00-00';
        $this->assertEquals("Zenona Skwierczyńska (∗1913, ✝1945) [№$person->id]", $person->formatName());

        $person->birth_date = null;
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
