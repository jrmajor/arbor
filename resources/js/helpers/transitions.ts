import { type TransitionConfig } from 'svelte/transition';
import { cubicOut } from 'svelte/easing';

export function flide(
	node: Element,
	{ delay = 0, duration = 400 } = {},
): TransitionConfig {
	const style = getComputedStyle(node);

	const opacityValue = Number(style.opacity);
	const slideValues: Array<[string, number]> = [
		['height', parseFloat(style.height)],
		['padding-top', parseFloat(style.paddingTop)],
		['padding-bottom', parseFloat(style.paddingBottom)],
		['margin-top', parseFloat(style.marginTop)],
		['margin-bottom', parseFloat(style.marginBottom)],
		['border-top-width', parseFloat(style.borderTopWidth)],
		['border-bottom-width', parseFloat(style.borderBottomWidth)],
	];

	return {
		delay,
		duration,
		easing(t) {
			if (t >= 0.5) return t;
			return cubicOut(t * 2) / 2;
		},
		css(t) {
			const slideT = t < 0.5 ? t * 2 : 1;
			const fadeT = t < 0.5 ? 0 : (t - 0.5) * 2;

			const slideStyles = slideValues.map(([key, value]) => `${key}: ${slideT * value}px;`);

			return `opacity: ${fadeT * opacityValue}; ${slideStyles.join('\n')}`;
		},
	};
}
