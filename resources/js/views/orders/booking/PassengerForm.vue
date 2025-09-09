<template>
    <div class="passenger-info-div">
        <div class="card-title">
            <span class="legend">{{ passengerIndex + 1 }}. Пассажир</span>
            <button v-if="hasPassengerData" @click="clearSelectedDoc" class="clear-button">
                Очистить форму
            </button>
        </div>

        <input
            type="hidden"
            :name="`passenger[${passengerIndex}][place]`"
            :value="seat"
        />
        <div v-if="user && userDocs.length">
            <label for="docSelect">Выбрать документ:</label>
            <ul class="row">
                <li
                    class="saved-docs-mini col"
                    v-for="doc in userDocs"
                    :key="doc.id"
                    @click="selectDoc(doc)"
                :class="{ 'selected': selectedDocId === doc.id }"
                >
                <div class="saved-docs-mini-type">
                    {{ doc.translated_type }}
                </div>
                <div class="saved-docs-mini-name">{{ doc.firstname }} {{ doc.lastname }}</div>
                </li>
            </ul>
        </div>

        <div class="row passenger-info-row">
            <div class="col">
                <div class="row">
                    <label :for="`passenger[${passengerIndex}][lastname]`">Фамилия:</label>
                </div>
                <div class="row">
                    <input
                        v-model="localPassenger.lastname"
                        :name="`passenger[${passengerIndex}][lastname]`"
                        :id="`passenger[${passengerIndex}][lastname]`"
                        autocomplete="new-lastname"
                        class="passenger_lastname"
                        :required="bookingStore.getNeedDocs"
                    />
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <label :for="`passenger[${passengerIndex}][firstname]`">Имя:</label>
                </div>
                <div class="row">
                    <input
                        v-model="localPassenger.firstname"
                        :name="`passenger[${passengerIndex}][firstname]`"
                        :id="`passenger[${passengerIndex}][firstname]`"
                        autocomplete="new-firstname"
                        class="passenger_firstname"
                        :required="bookingStore.getNeedDocs"
                    />
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <label :for="`passenger[${passengerIndex}][patronymic]`">Отчество:</label>
                </div>
                <div class="row">
                    <input
                        v-model="localPassenger.patronymic"
                        :name="`passenger[${passengerIndex}][patronymic]`"
                        :id="`passenger[${passengerIndex}][patronymic]`"
                        autocomplete="new-patronymic"
                        class="passenger_patronymic"
                        :disabled="localPassenger.no_patronymic"
                    />
                </div>
                <div class="row mt-2">
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            v-model="localPassenger.no_patronymic"
                            class="no_patronymic_check"
                        />
                        <span class="checkmark"></span>
                        <span class="no_patronymic_text">Нет отчества</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row passenger-info-row">
            <div class="col">
                <div class="row">
                    <label :for="`passenger[${passengerIndex}][gender]`">Пол:</label>
                </div>
                <div class="row radio_group" role="radiogroup" aria-labelledby="gender-label">
                    <div>
                        <label class="radio-label" data-qa="male-radioItem">
                            <input
                                type="radio"
                                :id="`male-radio[${passengerIndex}]`"
                                autocomplete="off"
                                class="radio-input"
                                :name="`passenger[${passengerIndex}][gender]`"
                                value="male"
                                v-model="localPassenger.gender"
                            />
                            <span class="radio-text" data-qa="male-radioItem-text">Мужской</span>
                        </label>
                    </div>
                    <div>
                        <label class="radio-label" data-qa="female-radioItem">
                            <input
                                type="radio"
                                :id="`female-radio[${passengerIndex}]`"
                                autocomplete="off"
                                class="radio-input"
                                :name="`passenger[${passengerIndex}][gender]`"
                                value="female"
                                v-model="localPassenger.gender"
                            />
                            <span class="radio-text" data-qa="female-radioItem-text">Женский</span>
                        </label>
                    </div>
                    <div
                        class="switcher"
                        :class="{
              'switcher-male': localPassenger.gender === 'male',
              'switcher-female': localPassenger.gender === 'female'
            }"
                        aria-hidden="true"
                    ></div>
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <label :for="`passenger[${passengerIndex}][birthday]`">Дата рождения:</label>
                </div>
                <div class="row">
                    <input
                        type="date"
                        v-model="localPassenger.birthday"
                        :name="`passenger[${passengerIndex}][birthday]`"
                        autocomplete="off"
                        :id="`passenger[${passengerIndex}][birthday]`"
                        class="passenger_birthday"
                        :required="bookingStore.getNeedDocs"
                    />
                </div>
            </div>
            <div class="col">
                <!-- Пустая колонка, если потребуется -->
            </div>
        </div>
        <div class="row passenger-info-row">
            <div class="col">
                <div class="row">
                    <label :for="`passenger[${passengerIndex}][document_type]`">Документ:</label>
                </div>
                <div class="row">
                    <select
                        v-model="localPassenger.document_type"
                        :name="`passenger[${passengerIndex}][document_type]`"
                        :id="`passenger[${passengerIndex}][document_type]`"
                        class="document-type"
                        autocomplete="off"
                        :required="bookingStore.getNeedDocs"
                    >
                        <option value="passport">Паспорт</option>
                        <option value="birth_certificate">Свидетельство о рождении</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <label :for="`passenger[${passengerIndex}][docs_number]`">Серия и номер:</label>
                </div>
                <div class="row">
                    <input
                        v-model="localPassenger.docs_number"
                        :name="`passenger[${passengerIndex}][docs_number]`"
                        :id="`passenger[${passengerIndex}][docs_number]`"
                        autocomplete="off"
                        class="docs_number"
                        :required="bookingStore.getNeedDocs"
                    />
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <label :for="`passenger[${passengerIndex}][tariff_type]`">Тариф:</label>
                </div>
                <div class="row">
                    <select
                        v-model="localPassenger.tariff_type"
                        :name="`passenger[${passengerIndex}][tariff_type]`"
                        :id="`passenger[${passengerIndex}][tariff_type]`"
                        class="tariff-type"
                        autocomplete="off"
                        disabled
                        :required="bookingStore.getNeedDocs"
                    >
                        <option value="full">Полный</option>
                        <option value="child">Детский</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <p align="justify" style="margin-top: 20px;">
  Указание персональных данных не является обязательным. Однако если вам необходимо предоставить билет для отчётности в организацию, рекомендуем указать их.
    </p>
</template>

<script setup>
import { ref, reactive, watch, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useBookingStore } from '@/stores/booking';
import axios from "axios";

// Определение пропов
const props = defineProps({
    passengerIndex: Number,
    seat: Number,
    passenger: Object,
    bookingData: Object,
    userDocs: Array,
});

const authStore = useAuthStore();
const bookingStore = useBookingStore();
const selectedDocId = ref("");

// Создаём локальную реактивную копию объекта passenger,
// чтобы не мутировать проп напрямую.
const localPassenger = reactive({ ...props.passenger });

// Синхронизация: если родитель обновит passenger, то обновляем и нашу копию
watch(
    () => props.passenger,
    (newVal) => {
        Object.assign(localPassenger, newVal);
    },
    { deep: true }
);

// При изменении localPassenger обновляем родительский объект,
// если это необходимо для дальнейшей обработки
watch(
    localPassenger,
    (newVal) => {
        Object.assign(props.passenger, newVal);
    },
    { deep: true }
);

const user = computed(() => authStore.user);

const selectDoc = (doc) => {
    selectedDocId.value = doc.id; // Устанавливаем id выбранного документа
    fillFromDoc(doc); // Заполняем данные на основе выбранного документа
};
// Функция заполнения формы на основе выбранного документа
const fillFromDoc = (doc) => {
    localPassenger.lastname = doc.lastname;
    localPassenger.firstname = doc.firstname;
    localPassenger.patronymic = doc.patronymic || "";
    localPassenger.gender = doc.gender ? (doc.gender.toLowerCase() === 'male' ? 'male' : 'female') : '';
    localPassenger.birthday = doc.birthday;
    localPassenger.document_type = doc.type;
    localPassenger.docs_number = doc.number;
    localPassenger.no_patronymic = !doc.patronymic;
};

const clearSelectedDoc = () => {
    selectedDocId.value = "";
    localPassenger.lastname = "";
    localPassenger.firstname = "";
    localPassenger.patronymic = "";
    localPassenger.gender = "male";
    localPassenger.birthday = "";
    localPassenger.document_type = "passport";
    localPassenger.docs_number = "";
    localPassenger.no_patronymic = false;
};

const hasPassengerData = computed(() => {
    return localPassenger.lastname ||
        localPassenger.firstname ||
        localPassenger.patronymic ||
        localPassenger.birthday ||
        localPassenger.docs_number;
});
</script>

<style scoped>
.legend {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
}
.passenger-info-row, .docs-tariff {
    margin: 0;
}
.passenger_firstname,
.passenger_lastname,
.passenger_patronymic,
.docs_number,
.document-type,
.passenger_birthday,
.tariff-type {
    max-width: 200px;
    min-width: 200px;
    font-size: 16px;
    height: 44px;
    border-radius: 8px;
    color: #0d0d0f;
    padding-right: 12px;
    padding-left: 12px;
    background-color: rgb(255, 255, 255);
    border: 1px solid rgb(227, 225, 220);

}

.passenger_firstname:hover,
.passenger_lastname:hover,
.passenger_patronymic:hover,
.docs_number:hover,
.document-type:hover,
.passenger_birthday:hover
    /*.tariff-type:hover*/
{
    border: 1px solid rgb(179, 179, 178);
}
.passenger_firstname:focus,
.passenger_lastname:focus,
.passenger_patronymic:focus,
.docs_number:focus,
.document-type:focus,
.passenger_birthday:focus,
    /*.tariff-type:focus,*/
.passenger_firstname:active,
.passenger_lastname:active,
.passenger_patronymic:active,
.docs_number:active,
.document-type:active,
.passenger_birthday:active
    /*.tariff-type:active*/
{
    border: 1px solid #ffeb3b;
    outline: 1px solid #ffeb3b;
}
.checkmark {
    position: absolute;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}
.checkbox-label {
    position: relative;
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 14px;
    user-select: none;
}

.checkbox-label input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: relative;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    display: inline-block;
    margin-right: 10px;
}

.checkbox-label input:checked ~ .checkmark {
    background-color: #007bff;
    border-color: #007bff;
}

.checkmark::after {
    content: "";
    position: absolute;
    display: none;
    left: 6px;
    top: 2px;
    width: 6px;
    height: 12px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.checkbox-label input:checked ~ .checkmark::after {
    display: block;
}
input:disabled, select:disabled {
    background: #f9f9f9;
}
.no_patronymic_text {
    margin: 0;
    left: 30px;
    font-size: 14px;
    font-weight: 600;
}
.row label {
    font-size: 14px;
    font-weight: 600;
}
.radio_group {
    position: relative;
    margin-bottom: 20px;
    background: #efecec;
    border-radius: 8px;
    height: 44px;
    padding: 4px 2px;
    width: max-content;
    min-width: 208px;
}
.radio-label {
    cursor: pointer; /* Курсор указателя при наведении */
    display: inline-block; /* Позволяет размещать метки рядом */
    padding: 6px 18px; /* Отступы по бокам */
    position: relative; /* Установить контекст позиционирования для переключателя */
    border-radius: 8px;
}

.radio-input {
    display: none; /* Скрыть стандартный стиль радио-кнопок */
}

/* Для выделенного текста радио-кнопки */
.radio-input:checked {
    font-weight: bold; /* Делает текст выделенным для выбранной радио-кнопки */
}

.radio-text {
    display: inline-block;
    position: relative;
    z-index: 1; /* Чтобы текст находился поверх переключателя */
    font-size: 16px;
    font-weight: 500;
}

.switcher {
    width: 50%;
    height: 36px;
    position: absolute; /* Абсолютное позиционирование */
    background-color: #FFFFFF; /* Цвет фона переключателя */
    border-radius: 8px; /* Закругленные углы */
    box-shadow: 0 4px 12px -4px hsla(60, 4%, 60%, .35), 0 0 2px hsla(60, 4%, 60%, .3);
    transition: left 0.3s ease; /* Плавное смещение переключателя */
    z-index: 0;
    left: 1%; /* Начальная позиция переключателя для "Мужской" */
}

/* Когда выбрано "Мужской", переключатель двигается влево */
.switcher-male {
    left: 1%;
}

/* Когда выбрано "Женский", переключатель двигается вправо */
.switcher-female {
    left: 47%; /* Перемещается на 49% для женского */
}
ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}
.saved-docs-mini {
    padding: 15px;
    background: #ffffff;
    margin: 5px;
    min-width: max-content;
    max-height: 75px;
    cursor: pointer;
    transition: background 0.3s;
    border-radius: 10px;
}

.saved-docs-mini:hover {
    border: 1px solid blue;
}

.saved-docs-mini-type {
    color: gray;
    font-size: 14px;
    position: relative;
}

.saved-docs-mini.selected {
    background-color: #e0f7fa; /* Выделение выбранного документа */
}

.saved-docs-mini-name {
    font-weight: bold;
}

.clear-button {
    background: #ffffff;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    height: 30px;
}
.clear-button:hover {
    border: 1px solid blue;
}

.card-title {
    display: flex;
    justify-content: space-between;
}
</style>
