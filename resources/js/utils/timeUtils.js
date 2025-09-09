export const getRefundTimeText = (time) => {
    const days = Math.floor(time / 1440); // Количество полных дней
    const remainingMinutes = time % 1440; // Оставшиеся минуты
    const hours = Math.floor(remainingMinutes / 60); // Количество полных часов
    const minutes = remainingMinutes % 60; // Оставшиеся минуты после подсчёта часов

    const getWord = (value, forms) => {
        const lastDigit = value % 10;
        const lastTwoDigits = value % 100;

        if (lastDigit === 1 && lastTwoDigits !== 11) {
            return forms[0]; // Например: 'день', 'час', 'минута'
        } else if ([2, 3, 4].includes(lastDigit) && ![12, 13, 14].includes(lastTwoDigits)) {
            return forms[1]; // Например: 'дня', 'часа', 'минуты'
        } else {
            return forms[2]; // Например: 'дней', 'часов', 'минут'
        }
    };

    let timeParts = [];

    if (time !== 1440 && time !== 30) {
        if (days > 0) {
            timeParts.push(`${days} ${getWord(days, ['день', 'дня', 'дней'])}`);
        }
        if (hours > 0) {
            timeParts.push(`${hours} ${getWord(hours, ['час', 'часа', 'часов'])}`);
        }
        if (minutes > 0) {
            timeParts.push(`${minutes} ${getWord(minutes, ['минута', 'минуты', 'минут'])}`);
        }
    } else {
        timeParts.push(time === 1440 ? 'сутки' : 'полчаса');
    }

    return timeParts.join(' ');
};
