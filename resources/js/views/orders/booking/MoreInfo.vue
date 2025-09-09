<template>
    <div>
        <div id="more-info" v-show="showMoreInfo">
            <!-- Полная информация -->
            Сумма возврата зависит от условий Перевозчика, а также от времени, которое осталось до поездки. Чем раньше вы оформили возврат, тем большую сумму вернете. Билет можно вернуть только до отправления автобуса, после отправления автобуса билет возврату не подлежит.
            <br>
            <ul v-if="refundLimit">
                <li class="text-danger">
                    На данный маршрут оформить возврат билета возможно не позднее, чем за {{ getRefundTimeText(refundLimit) }} до отправления.
                    При оформлении возврата удержится 5% от стоимости проезда (сервисные сборы возврату не подлежат)
                </li>
            </ul>
            <br>У большинства перевозчиков при возврате пассажиром билета по причине отказа от поездки, производятся следующие удержания:<br>

            <ol>
                <li>Возврат билета более чем за 2 часа до отправления — удерживается 5% от стоимости проезда (сервисные сборы возврату не подлежат);</li>
                <li>Возврат билета в течение 2 часов до отправления — удерживается 15% от стоимости проезда (сервисные сборы возврату не подлежат);</li>
                <li>
                    <p class="text-danger" style="margin: 0">Внимание! Менее, чем за полчаса до отправления автобуса возврат билета оформляется только через подачу письменного заявления на кассах автовокзала;</p>
                </li>
                <li>В случае отмены рейса возвращается 100% от стоимости билета.</li>
            </ol>
            Штатно удерживается комиссия в размере перечисленных процентов от стоимости билета. У некоторых перевозчиков возможно удержание дополнительной комиссии за возврат билета.
            Обращаем ваше внимание, что возврат средств осуществляется в течение 2 - 5 рабочих дней и зависит от скорости обработки заявки банком.
            Если у вас возникнут какие-либо трудности при оформлении возврата, вы можете связаться с нами по номеру телефона <a href="tel:83012268003">+7 (3012) 26-80-03</a> (с 4 до 12 ч. по МСК времени в будни) или написать нам на служебную почту <a href="mailto:info@biletavto.ru">info@biletavto.ru</a>.
        </div>
        <div id="short-info" v-show="!showMoreInfo">
            <!-- Сокращенная информация -->
            Сумма возврата зависит от условий Перевозчика, а также от времени, которое осталось до...
        </div>
        <button type="button" class="more-info-btn" @click="toggleMoreInfo">
            {{ showMoreInfo ? 'Скрыть' : 'Подробнее' }}
            <svg v-show="!showMoreInfo" id="caret-down" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
            </svg>
            <svg v-show="showMoreInfo" id="caret-up" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-up-fill" viewBox="0 0 16 16">
                <path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
            </svg>
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { getRefundTimeText } from '../../../utils/timeUtils.js'

const props = defineProps({
    refundLimit: {
        type: [Number, null],
        required: true
    }
});

const showMoreInfo = ref(false)
const toggleMoreInfo = () => {
    showMoreInfo.value = !showMoreInfo.value
}
</script>

<style scoped>
.more-info-btn {
    background: none;
    color: #9c9ba2;
    border: none;
    display: contents;
    padding: 0;
    font-size: 18px;
    transition: background-color 0.3s;
    margin-top: 4px;
    border-radius: 8px;
}
.more-info-btn:hover {
    background: none;
    color: #5e5d65;
    border: none;
}
.more-info-btn:focus, .more-info-btn:active {
    outline: none;
}
#more-info, #short-info {
    margin-top: 16px;
    text-align: start;
}
</style>
