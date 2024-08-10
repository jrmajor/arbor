<script lang="ts">
	import type { MarriagePage } from '@/types/resources/marriages';
	import { t } from '@/helpers/translations';

	let { marriage }: { marriage: MarriagePage } = $props();
</script>

<span class:invalid={marriage.woman.isDead}>
	{marriage.woman.name} {marriage.woman.familyName}
</span>
+
<span class:invalid={marriage.man.isDead}>
	{marriage.man.name} {marriage.man.familyName}
</span>

<small class="text-lg">
	[{t('marriages.marriage')} №{marriage.id}]
</small>
