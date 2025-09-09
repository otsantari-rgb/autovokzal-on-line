<template>
  <div ref="mapContainer" :style="{ width: '100%', height: '400px' }"></div>
</template>

<script>
export default {
  props: {
    mapData: {
      type: Object,
      required: true
    }
  },
  mounted() {
    this.loadYandexMap();
  },
  methods: {
    loadYandexMap() {
      if (typeof ymaps === 'undefined') {
        const script = document.createElement('script');
        const apiKey = import.meta.env.VITE_YANDEX_MAPS_API_KEY;

        if (!apiKey) {
          console.error('YANDEX_MAPS_API_KEY не задан в переменных окружения');
          return;
        }

        script.src = `https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=${apiKey}`;
        script.onload = this.initMap;
        document.head.appendChild(script);
      } else {
        this.initMap();
      }
    },
    initMap() {
      ymaps.ready(() => {
        const { fromCoords, toCoords, fromName, toName } = this.mapData;

        if (
          !Array.isArray(fromCoords) || fromCoords.length !== 2 ||
          !Array.isArray(toCoords) || toCoords.length !== 2
        ) {
          console.error('Некорректный формат координат:', fromCoords, toCoords);
          return;
        }

        const start = [Number(fromCoords[0]), Number(fromCoords[1])];
        const end = [Number(toCoords[0]), Number(toCoords[1])];

        // Используем реальный DOM-элемент вместо id
        const mapElement = this.$refs.mapContainer;
        if (!mapElement) {
          console.error('Элемент для карты не найден!');
          return;
        }

        const map = new ymaps.Map(mapElement, {
          center: start,
          zoom: 7,
          controls: ['zoomControl']
        });

        const multiRoute = new ymaps.multiRouter.MultiRoute({
          referencePoints: [start, end],
          params: {
            routingMode: 'auto'
          }
        }, {
          boundsAutoApply: true
        });

        multiRoute.events.add('requestfail', (e) => {
          console.error('Ошибка построения маршрута:', e);
        });

        multiRoute.model.events.add('requestsuccess', () => {
          const activeRoute = multiRoute.getActiveRoute();
          if (activeRoute) {
            const distanceMeters = activeRoute.properties.get('distance').value; // по трассе
            const straightLineMeters = ymaps.coordSystem.geo.getDistance(start, end); // по прямой
            this.$emit('route-info', {
              distanceMeters,
              straightLineMeters,
            });
          }
        });

        map.geoObjects.add(multiRoute);

        const fromPlacemark = new ymaps.Placemark(start, {
          balloonContent: `Автовокзал ${fromName}`
        });

        const toPlacemark = new ymaps.Placemark(end, {
          balloonContent: `Автовокзал ${toName}`
        });

        map.geoObjects.add(fromPlacemark);
        map.geoObjects.add(toPlacemark);
      });
    }
  }
};
</script>

<style scoped>
#map{
  border: 1px solid #ddd;
  border-radius: 16px;
  overflow: hidden;
} 
</style>