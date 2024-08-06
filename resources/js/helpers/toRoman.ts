export default function toRoman(number: number) {
	let roman = '';
	for (const numeral in lookup) {
		while (number >= lookup[numeral]) {
			roman += numeral;
			number -= lookup[numeral];
		}
	}
	return roman;
}

const lookup: Record<string, number> = {
	M: 1000,
	CM: 900,
	D: 500,
	CD: 400,
	C: 100,
	XC: 90,
	L: 50,
	XL: 40,
	X: 10,
	IX: 9,
	V: 5,
	IV: 4,
	I: 1,
};
