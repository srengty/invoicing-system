export function convertCurrencyToWords(num) {
    if (isNaN(num)) return "";

    const decimalPart = Math.round((num % 1) * 100);
    const wholePart = Math.floor(num);

    if (wholePart === 0 && decimalPart === 0) {
        return "Zero Real";
    }

    const wholeWords = convertNumberToWords(wholePart);
    const decimalWords = decimalPart > 0 ? convertNumberToWords(decimalPart) : null;

    // Singular if exactly 1, else plural
    let result = wholeWords + " Real" + (wholePart === 1 ? "" : "s");

    if (decimalWords) {
        result += " and " + decimalWords + " cent" + (decimalPart === 1 ? "" : "s");
    }

    // Capitalize first letter
    return result.charAt(0).toUpperCase() + result.slice(1);
}

function convertNumberToWords(num) {
    if (num === 0) return "zero";

    const units = ["", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];
    const teens = ["ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen"];
    const tens = ["", "ten", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"];

    let words = [];

    if (num >= 1000000) {
        const millions = Math.floor(num / 1000000);
        words.push(convertNumberToWords(millions) + " million" + (millions === 1 ? "" : "s"));
        num %= 1000000;
    }

    if (num >= 1000) {
        const thousands = Math.floor(num / 1000);
        words.push(convertNumberToWords(thousands) + " thousand");
        num %= 1000;
    }

    if (num >= 100) {
        const hundreds = Math.floor(num / 100);
        words.push(units[hundreds] + " hundred");
        num %= 100;
    }

    if (num >= 20) {
        const ten = Math.floor(num / 10);
        words.push(tens[ten]);
        num %= 10;
    } else if (num >= 10) {
        words.push(teens[num - 10]);
        num = 0;
    }

    if (num > 0) {
        words.push(units[num]);
    }

    return words.join(" ");
}
