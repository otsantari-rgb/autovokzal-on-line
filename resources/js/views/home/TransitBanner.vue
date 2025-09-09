<template>
    <div class="transit-banner-container">
        <img class="img-banner" alt="" src="/img/commercial_graphics/tranzit.webp" loading="lazy">
    </div>
</template>

<script setup>

</script>

<style scoped>
.transit-banner-container {
    border: none;
    border-radius: 20px;
    max-width: 800px;
    min-width: 200px;
    width: 100%;
    overflow: hidden; /* Избегаем переполнение */
    text-align: left;
    padding:0 15px;
    min-height: 285px;
}

.img-banner {
    width: 100%;
    border-radius: 15px;
}

@media (max-width: 480px) {
    .transit-banner-container {
        min-height: 167px;
    }
}
</style>
