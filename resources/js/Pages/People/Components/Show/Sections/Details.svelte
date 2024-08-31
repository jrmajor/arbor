<script lang="ts">
	import { route } from 'ziggy-js';
	import { inertia } from '@inertiajs/svelte';
	import type { ShowPersonResource } from '@/types/resources/people';
	import { t } from '@/helpers/translations';
	import toRoman from '@/helpers/toRoman';
	import DataList from '@/Components/Primitives/DataList.svelte';
	import Name from '@/Components/Name.svelte';
	import OptionalDatePlace from '../OptionalDatePlace.svelte';
	import PytlewskiSection from '../DetailsSections/PytlewskiSection.svelte';

	let { person }: { person: ShowPersonResource } = $props();

	let wielcy = $derived(person.wielcy);
</script>

<div class="p-6 bg-white rounded-lg shadow">
	<DataList>
		<PytlewskiSection {person}/>

		<!-- wielcy -->
		{#if wielcy}
			<dt>
				{t('people.id_in')}
				<a href="http://www.wielcy.pl/" target="_blank" class="a">wielcy.pl</a>
			</dt>
			<dd>
				<a href={wielcy.url} target="_blank" class="a">
					{wielcy.id}
					{#if wielcy.name}
						<small>{t('people.wielcy.as')} {wielcy.name}</small>
					{/if}
				</a>
			</dd>
		{/if}

		<!-- names -->
		{#if person.middleName}
			<dt>{t('people.names')}</dt>
			<dd>{person.name} {person.middleName}</dd>
		{:else}
			<dt>{t('people.name')}</dt>
			<dd>{person.name}</dd>
		{/if}

		<dt>{t('people.family_name')}</dt>
		<dd>{person.familyName}</dd>

		{#if person.lastName}
			<dt>{t('people.last_name')}</dt>
			<dd>{person.lastName}</dd>
		{/if}

		<!-- birth -->
		{#if person.birthDate || person.birthPlace || person.age.estimatedBirthDate}
			<dt>{t('people.birth')}</dt>
			<dd class="indent-children-except-first">
				<OptionalDatePlace date={person.birthDate} place={person.birthPlace} multiline/>
				{#if !person.isDead && person.age.current && person.age.prettyCurrent}
					<p>
						{t('people.current_age')}:
						{t('misc.year', { rawAge: person.age.current, age: person.age.prettyCurrent })}
					</p>
				{/if}
				{#if person.age.estimatedBirthDate}
					<p>
						{t('people.estimated_birth_date')}: {person.age.estimatedBirthDate}
						{#if person.age.estimatedBirthDateError !== null}
							<small>
								(<strong>{person.age.estimatedBirthDateError}</strong>
								{t('misc.years_of_error', { age: person.age.estimatedBirthDateError! })})
							</small>
						{/if}
					</p>
				{/if}
			</dd>
		{/if}

		<!-- death -->
		{#if person.isDead}
			<dt>{t('people.death')}</dt>
			{#if person.deathDate || person.deathPlace || person.deathCause}
				<dd class="indent-children-except-first">
					<OptionalDatePlace date={person.deathDate} place={person.deathPlace} multiline/>
					{#if person.deathCause}
						<p>{person.deathCause}</p>
					{/if}
					{#if person.age.atDeath && person.age.prettyAtDeath}
						<p>
							{t('people.death_age')}:
							{t('misc.year', { rawAge: person.age.atDeath, age: person.age.prettyAtDeath })}
						</p>
					{/if}
				</dd>
			{:else}
				<dd>&#10013;&#xFE0E;</dd>
			{/if}
		{/if}

		<!-- funeral -->
		{#if person.funeralDate || person.funeralPlace}
			<dt>{t('people.funeral')}</dt>
			<dd class="indent-children-except-first">
				<OptionalDatePlace date={person.funeralDate} place={person.funeralPlace} multiline/>
			</dd>
		{/if}

		<!-- burial -->
		{#if person.burialDate || person.burialPlace}
			<dt>{t('people.burial')}</dt>
			<dd class="indent-children-except-first">
				<OptionalDatePlace date={person.burialDate} place={person.burialPlace} multiline/>
			</dd>
		{/if}

		<!-- parents -->
		{#if person.mother}
			<dt>{t('people.mother')}</dt>
			<dd><Name person={person.mother}/></dd>
		{/if}
		{#if person.father}
			<dt>{t('people.father')}</dt>
			<dd><Name person={person.father}/></dd>
		{/if}

		<!-- siblings -->
		{#if person.siblings.length}
			<dt>{t('people.siblings')} ({person.siblings.length})</dt>
			<dd>
				<ul>
					{#each person.siblings as sibling}
						<li><Name person={sibling}/></li>
					{/each}
				</ul>
			</dd>
		{/if}

		<!-- przyr. od str. matki -->
		{#if person.siblingsMother.length}
			<dt>{t('people.siblings_mother')} ({person.siblingsMother.length})</dt>
			<dd>
				<ul>
					{#each person.siblingsMother as sibling}
						<li><Name person={sibling}/></li>
					{/each}
				</ul>
			</dd>
		{/if}

		<!-- przyr. od str. ojca -->
		{#if person.siblingsFather.length}
			<dt>{t('people.siblings_father')} ({person.siblingsFather.length})</dt>
			<dd>
				<ul>
					{#each person.siblingsFather as sibling}
						<li><Name person={sibling}/></li>
					{/each}
				</ul>
			</dd>
		{/if}

		<!-- marriages -->
		{#if person.marriages.length}
			<dt>{t('people.marriages')}</dt>
			<dd>
				<ul>
					{#each person.marriages as marriage}
						{#if marriage.perm.view}
							<li class="indent-children-except-first">
								{#if person.marriages.length > 1 && marriage.order}
									{toRoman(marriage.order).toLowerCase()}.
								{/if}
								{#if marriage.rite}
									<strong>{t(`marriages.rites.${marriage.rite}`)}:</strong>
								{/if}

								<Name person={marriage.partner}/>

								{#if marriage.perm.update}
									<a use:inertia href={route('marriages.edit', { marriage })} class="a">
										<small>[{t('marriages.marriage')} №{marriage.id}]</small>
									</a>
									<!-- todo: check correct permission -->
									<a
										use:inertia
										href={route('people.create', {
											mother: person.sex === 'xy' ? marriage.partner.id : person.id,
											father: person.sex === 'xy' ? person.id : marriage.partner.id,
										})}
										class="a"
									>
										<small>[+]</small>
									</a>
								{/if}

								{#if marriage.firstEvent}
									<OptionalDatePlace
										date={marriage.firstEvent.date}
										place={marriage.firstEvent.place}
										prefix={marriage.firstEvent.type ? t(`marriages.event_types.${marriage.firstEvent.type}`) : null}
									/>
								{/if}
								{#if marriage.secondEvent}
									<OptionalDatePlace
										date={marriage.secondEvent.date}
										place={marriage.secondEvent.place}
										prefix={marriage.secondEvent.type ? t(`marriages.event_types.${marriage.secondEvent.type}`) : null}
									/>
								{/if}

								{#if marriage.divorced}
									<OptionalDatePlace
										date={marriage.divorceDate}
										place={marriage.divorcePlace}
										prefix={t('marriages.divorced').toLowerCase()}
									/>
								{/if}
							</li>
						{:else}
							<li>
								{#if marriage.order}
									{toRoman(marriage.order).toLowerCase()}.
								{/if}

								<small>[{t('misc.hidden')}]</small>
								<small>[{t('marriages.marriage')} №{marriage.id}]</small>
							</li>
						{/if}
					{/each}
				</ul>
			</dd>
		{/if}

		<!-- children -->
		{#if person.children.length}
			<dt>{t('people.children')} ({person.children.length})</dt>
			<dd>
				<ul>
					{#each person.children as child}
						<li><Name person={child}/></li>
					{/each}
				</ul>
			</dd>
		{/if}

		<!-- sources -->
		{#if person.sources.length}
			<dt>{t('people.sources')}</dt>
			<dd>
				<ul>
					{#each person.sources as source}
						<li><small class="text-black">{@html source}</small></li>
					{/each}
				</ul>
			</dd>
		{/if}

		<!-- notes -->
		<!--
		<dt>
			Notes
			<a href="" data-toggle="tooltip" data-html="true" title="show_notes_versions">
				<small>[total 234_version]</small>
			</a>&nbsp;
			<a href="" data-toggle="tooltip" title="edit_notes" target="_blank">
				<small>[+]</small>
			</a>&nbsp;
		</dt>
		<dd>
			notes<br>
			note_deleted_you_can_see <a href="">earlier_versions</a> or <a href="">add_new_one</a>.
		</dd>
		<dt>Notes <small>[no_notes]</small></dt>
		<dd>
			<a href="" data-toggle="tooltip" data-html="true" title="click_to_create_note" target="_blank">[+]</a>
		</dd>
		-->
	</DataList>
</div>
