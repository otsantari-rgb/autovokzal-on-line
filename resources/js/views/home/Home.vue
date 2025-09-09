<template>
    <div class="content">
        <div class="main-content" id="main-content">
            <ads></ads>
            <Carousel></Carousel>
            <Overwiev></Overwiev>
            <transit-banner></transit-banner>
        </div>
    </div>
</template>

<script setup>
import Carousel from "./Carousel.vue";
import Ads from "./Ads.vue";
import TransitBanner from "./TransitBanner.vue";
import Overwiev from "./Overview.vue";
</script>
<style>
.content {
    background: #eff1f4 50%;
}
.main-content {
    padding-top: 50px;
    padding-bottom: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    flex-direction: column;
    border-bottom: 1px solid lightgray;
    gap: 50px;
    background: linear-gradient(to bottom, #007bff 17%, #eff1f4 17%);

}
</style>
