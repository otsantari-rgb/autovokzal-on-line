<template>
    <div v-if="isLoading" class="loading-overlay">
        <div class="loading-box">
            <span class="loading-text">Загрузка...</span>
            <div class="loader"></div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const isLoading = ref(true); // Можно динамически управлять
</script>

<style scoped>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-box {
    background: #fff;
    width: 400px;
    height: 200px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 16px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    gap: 20px;
}

.loading-text {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
    animation: blink 1.5s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.loader {
    width: 50px;
    padding: 8px;
    aspect-ratio: 1;
    border-radius: 50%;
    background: #007bff;
    --_m:
        conic-gradient(#0000 10%,#000),
        linear-gradient(#000 0 0) content-box;
    -webkit-mask: var(--_m);
    mask: var(--_m);
    -webkit-mask-composite: source-out;
    mask-composite: subtract;
    animation: l3 1s infinite linear;
}
@keyframes l3 {to{transform: rotate(1turn)}}
</style>
