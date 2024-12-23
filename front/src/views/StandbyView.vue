<script setup>
import { Loader } from "@googlemaps/js-api-loader"

import { onMounted, ref } from 'vue';
import { useTripStore } from '@/stores/trip';
import Spin from "@/components/Spin.vue";
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;
import { useLocationStore } from "@/stores/location";
import http from "@/helpers/http";
import router from "@/router";


const trip = useTripStore();

const title = ref("Waiting for ride request...")

const location = useLocationStore()

const loader = new Loader({
    apiKey: VITE_APP_GOOGLE_MAPS_API_KEY,
    version: "weekly",
    libraries: ["maps", "places", "marker", "core"],
});

const mapDiv = ref(null)

onMounted(async () => {
    const initMap =
        async () => {
            await loader.load()
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer();
            const currentPoint = new google.maps.LatLng(trip.origin);
            const destinationPoint = new google.maps.LatLng(trip.destination);
            const mapOptions = {
                zoom: 14,
                center: currentPoint
            }
            const map = new google.maps.Map(mapDiv.value, mapOptions);
            directionsRenderer.setMap(map);
            const request = {
                origin: currentPoint,
                destination: destinationPoint,
                travelMode: google.maps.TravelMode.DRIVING
            }
            directionsService.route(request, function (response, status) {
                if (status == 'OK') {
                    directionsRenderer.setDirections(response);
                };
            })
        }
    await location.updateCurrentLocation()
    const echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        wssPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    })

    echo.channel('drivers')
        .listen('TripCreated', (e) => {
            trip.$patch(
                e.trip)
            title.value = 'Ride Requested'
            console.log('TripCreated', e)
            setTimeout(initMap, 2000)
        }
        )

})

const handleDecline = () => {
    trip.reset();
    title.value = 'waiting for ride request...'
}
const handleAccept = () => {
    http().post(`/api/trip/${trip.id}/accept`, {
        driver_location: location.current.geometry
    })
        .then((response) => {
            console.log(response)
            location.$patch({
                destination: {
                    name: 'Passenger',
                    geometry: response.data.origin
                }
            })
            router.push({
                name: 'driving'
            })
        })
        .catch((error) => {
            console.error(error)
        })
}
</script>

<template>
    <div class="w-96">
        <h1>
            {{ title }}
        </h1>
        <Spin v-if="!trip.id"></Spin>
        <div v-else>
            <div
                ref="mapDiv"
                class="w-full h-[256px]"
            ></div>
            <p>Going to <strong>{{ trip.destination_name }}</strong></p>
            <button @click="handleDecline">Decline</button>
            <button @click="handleAccept">Accept</button>
        </div>

    </div>
</template>
