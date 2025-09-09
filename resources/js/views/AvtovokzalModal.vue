<script setup>
const props = defineProps({
    station: {
        type: Object,
        default: null
    },
    isVisible: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['close']);
</script>

<template>
    <div v-if="isVisible" class="avtovokzal-modal">
        <div class="avtovokzal-modal-content">
            <span class="avtovokzal-close" @click="$emit('close')">&times;</span>
            <h2>{{ station?.name || 'Информация недоступна' }}</h2>
            <div class="img-info-row">
                <div class="img-col">
                    <img class="avtovokzal-modal-image" :src="station?.modalImageLink || '/img/placeholder.png'" :alt="station?.name" />
                </div>
                <div class="info-col">
                    <p class="address">{{ station?.address || 'Адрес отсутствует' }}</p>
                    <p class="working-hours">Часы работы: {{ station?.openingHours || 'Не указано' }}</p>
                    <p class="lunch-time">Обед: {{ station?.lunch || 'Нет данных' }}</p>
                    <p><strong>Телефоны:</strong></p>
                    <p class="phone">{{ station?.dispatcherPhone || 'Нет телефона' }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.avtovokzal-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.avtovokzal-modal-content {
    background: white;
    border-radius: 10px;
    padding: 20px;
    max-width: 600px;
    width: 90%;
    position: relative;
}

.avtovokzal-close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 20px;
    cursor: pointer;
    color: #333;
}

.img-info-row {
    display: flex;
    gap: 20px;
}

.img-col img {
    max-width: 100%;
    border-radius: 10px;
}

.info-col {
    flex-grow: 1;
}
</style>
