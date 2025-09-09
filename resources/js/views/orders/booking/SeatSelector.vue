<template>
    <div class="bus-scheme">
        <div class="bus-scheme--title">Выберите места</div>
        <div class="place-info d-flex flex-row">
            <div class="p-4">
                <div class="row">
                    <div class="occupied"></div>
                    занято
                </div>
            </div>
            <div class="p-4">
                <div class="row">
                    <div class="free"></div>
                    свободно
                </div>
            </div>
            <div class="p-4">
                <div class="row">
                    <div class="selected-info"></div>
                    выбрано вами
                </div>
            </div>
        </div>

        <div class="bus-scheme--desktop">
            <div v-for="(row, i) in seatMap[0]" :key="'row-' + i" class="bus-scheme__row">
                <span v-if="i === seatMap[0].length - 1" class="bus-scheme__place bus-scheme__place--wheel"></span>
                <span v-else class="bus-scheme__place bus-scheme__place--way"></span>

                <template v-for="(col, j) in seatMap" :key="'col-' + j">
          <span v-if="col[i] && col[i] !== 0"
                :class="[
                  'bus-scheme__place',
                  {'bus-scheme__place--sold': bookedSeats.includes(col[i])},
                  {'bus-scheme__place--free j-booking-place': !bookedSeats.includes(col[i])},
                  {'selected': selectedSeats.includes(col[i])}
                ]"
                :data-position="col[i]"
                @click="toggleSeat(col[i])">
            <span>{{ col[i] }}</span>
          </span>
                    <span v-else class="bus-scheme__place bus-scheme__place--way"><span></span></span>
                </template>
            </div>
        </div>


        <div class="bus-scheme--mobile">
            <!-- Колесо перед первым рядом -->
            <span class="bus-scheme__place bus-scheme__place--wheel"></span>

            <!-- Ряды мест -->
            <div
                v-for="(row, i) in seatMap"
                :key="'mobile-row-' + i"
                class="bus-scheme__row"
            >
                <!-- Места в ряду -->
                <template v-for="(seat, j) in row.slice().reverse()" :key="'mobile-seat-' + j">
            <span
                v-if="seat && seat !== 0"
                :class="[
                    'bus-scheme__place',
                    { 'bus-scheme__place--sold': bookedSeats.includes(seat) },
                    { 'bus-scheme__place--free j-booking-place': !bookedSeats.includes(seat) },
                    { 'selected': selectedSeats.includes(seat) }
                ]"
                :data-position="seat"
                @click="toggleSeat(seat)"
            >
                <span>{{ seat }}</span>
            </span>
                    <span
                        v-else
                        class="bus-scheme__place bus-scheme__place--way"
                    >
                <span></span>
            </span>
                </template>
            </div>
        </div>
        <div class="bus-scheme--footer">
            Схема мест предоставлена перевозчиком и может отличаться от реального расположения мест.
            В случаях неисправности или изменения спроса автобус может быть заменён на другой.
            <br><br>
            Для покупки билетов с детскими тарифами посетите сайт <a href="https://biletavto.ru">biletavto.ru</a>.
        </div>
    </div>
</template>

<script>
import { computed } from 'vue';

export default {
    name: 'SeatSelector',
    props: {
        seatMap: {
            type: Array,
            required: true,
            validator: (value) =>
                Array.isArray(value) && value.every((row) => Array.isArray(row)),
        },
        bookedSeats: {
            type: Array,
            required: true,
        },
        modelValue: {
            type: Array,
            default: () => [],
        },
        maxSeats: {
            type: Number,
            default: 5,
        },
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const selectedSeats = computed({
            get: () => props.modelValue,
            set: (value) => emit('update:modelValue', value),
        });

        const toggleSeat = (seat) => {
            if (props.bookedSeats.includes(seat)) return;

            const newSelectedSeats = [...selectedSeats.value];
            const index = newSelectedSeats.indexOf(seat);

            if (index > -1) {
                newSelectedSeats.splice(index, 1);
            } else if (newSelectedSeats.length < props.maxSeats) {
                newSelectedSeats.push(seat);
            } else {
                alert(`Вы можете выбрать не более ${props.maxSeats} мест.`);
                return;
            }

            selectedSeats.value = newSelectedSeats;
        };

        return {
            selectedSeats,
            toggleSeat,
        };
    },
};
</script>

<style scoped>
.bus-scheme {
    background: #f9f9f9;
    padding: 28px 32px 32px;
    border-radius: 16px;
    border: 1px solid #ddd;
    box-shadow: 0 4px 12px -4px hsla(60,4%,60%,.35),0 0 2px hsla(60,4%,60%,.3);
    text-align: start;
}
.bus-scheme--desktop {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 40px;
    border: 1px solid #ddd;
    border-radius: 16px;
    background-color: #f9f9f9;
    width: max-content;
    margin-top: 20px;
    margin-bottom: 20px;
}

.bus-scheme--mobile {
    margin: 20px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #f9f9f9;
    width: max-content;
}

.bus-scheme__row {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 10px;
}

.bus-scheme__place {
    width: 32px;
    height: 32px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 5px;
    font-size: 14px;
    font-weight: bold;
    position: relative;
}

.bus-scheme__place--wheel {
    width: 32px; /* Ширина для колеса */
    height: 32px; /* Высота колеса */
}

.bus-scheme__place--way {
    width: 32px; /* Ширина для прохода */
    background: none;
}

.bus-scheme__place--free {
    background-color: #f9f9f9; /* Цвет для свободного места */
    border: 2px solid #0072ff;
    color: #0072ff;
    cursor: pointer;
    transition: background-color 0.3s;
}

.bus-scheme__place--free:hover {
    color:white;
    background-color: #0072ff;
    transform: scale(1.1);
    box-shadow: 0 4px 20px rgba(0, 123, 255, 0.4);
}

.bus-scheme__place.selected {
    color: #864a5d;
    border: 2px solid #ffeb3b;
    background-color: rgba(255, 235, 59, 0.7);
}

.bus-scheme__place--sold {
    background-color: #f44336;
    border: 2px solid #bf1c1c;
    color: #312800;
}
.bus-scheme--title {
    font-size: 18px;
    font-weight: 600;
}
.bus-scheme--footer {
    font-weight: 600;
}
.occupied {
    width: 16px;
    height: 16px;
    background: #f44336;
    border-radius: 4px;
    border: 1px solid #bf1c1c;
    margin-right: 5px;
}
.free {
    width: 16px;
    height: 16px;
    background: #f9f9f9;
    border-radius: 4px;
    border: 1px solid #0072ff;
    margin-right: 5px;
}
.selected-info {
    width: 16px;
    height: 16px;
    background: rgba(255, 235, 59, 0.7);
    border-radius: 4px;
    border: 1px solid #ffeb3b;
    margin-right: 5px;
}

input[type="checkbox"] {
    position: absolute;
    opacity: 0;
}

input[type="checkbox"] + .free-place {
    display: inline-block;
}

.place-info {
    margin: 0;
    line-height: 16px;
}
@media (max-width: 768px) { /* Замените 768px на ваше значение медиапараметра */
    .bus-scheme--desktop {
        display: none;
    }
    .bus-scheme--mobile {
        display: block;
    }
    .bus-scheme__place--wheel {
        background: url("/img/wheel.svg"); /* Цвет для колеса */
    }
    .bus-scheme {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
}

@media (min-width: 768px) {
    .bus-scheme--desktop {
        display: block;
    }
    .bus-scheme--mobile {
        display: none;
    }
    .bus-scheme__place--wheel {
        background: url("/img/driver.svg"); /* Цвет для колеса */
    }
    .bus-scheme {
        display: block;
    }
}
</style>
