import dayjs from 'dayjs';
import customParseFormat from 'dayjs/plugin/customParseFormat';

export const timeCalculation = ( 
    departureDate,
    departureTime,
    arrivalDate,
    arrivalTime
)  => {
   

    const departure = dayjs(
        `${departureDate} ${departureTime}`,
        'DD.MM.YYYY HH:mm'
    );
    const arrival = dayjs(
        `${arrivalDate} ${arrivalTime}`,
        'DD.MM.YYYY HH:mm'
    );
    const duration = arrival.diff(departure, 'minute');
    const hours = Math.floor(duration / 60);
    const minutes = duration % 60;
    return `${hours} ч ${minutes} м`;
}

export default  timeCalculation;