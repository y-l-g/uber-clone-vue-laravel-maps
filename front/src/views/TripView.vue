<script setup>

import { useLocationStore } from "@/stores/location";
import { useTripStore } from "@/stores/trip";
import { Loader } from "@googlemaps/js-api-loader"
import { onMounted, ref } from "vue";
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import router from "@/router";
window.Pusher = Pusher;
const loader = new Loader({
    apiKey: VITE_APP_GOOGLE_MAPS_API_KEY,
    version: "weekly",
    libraries: ["maps", "places", "marker", "core"],
});

const currentIcon = document.createElement("img");

currentIcon.src =
    "https://openmoji.org/php/download_asset.php?type=emoji&emoji_hexcode=1F920&emoji_variant=color";

currentIcon.width = 24
currentIcon.height = 24

const driverIcon = document.createElement("img");

driverIcon.src =
    "https://openmoji.org/php/download_asset.php?type=emoji&emoji_hexcode=1F698&emoji_variant=color";
driverIcon.width = 24
driverIcon.height = 24

const location = useLocationStore()
const trip = useTripStore()

const mapDiv = ref(null)

const title = ref('Waiting for a DriverView..')
const message = ref('When a driver accepts the trip, their info will appear here')

onMounted(async () => {
    await loader.load()
    const mapOptions = {
        zoom: 14,
        center: location.current.geometry,
        mapId: "mapId"
    }
    const map = new google.maps.Map(mapDiv.value, mapOptions);
    const currentMarker = new google.maps.marker.AdvancedMarkerElement({
        map,
        position: location.current.geometry,
        title: 'Current',
        content: currentIcon
    });

    const driverMarker = new google.maps.marker.AdvancedMarkerElement({
        map,
        title: 'Driver',
        content: driverIcon
    });

    const echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        wssPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    })

    echo.channel(`passenger_${trip.user_id}`)
        .listen('TripAccepted', (e) => {
            console.log(e)
            trip.$patch(e.trip)
            const newPosition = new google.maps.LatLng(e.trip.driver_location.lat, e.trip.driver_location.lng);
            driverMarker.position = newPosition;
            title.value = 'A driver is on the way'
            message.value = `${e.trip.driver.user.name} is coming in a ${e.trip.driver.year} ${e.trip.driver.color} ${e.trip.driver.make} ${e.trip.driver.model} with a license plate ${e.trip.driver.license_plate}`
            console.log('TripCreated', e)
        })
        .listen('TripLocationUpdated', (e) => {
            trip.$patch(
                e.trip
            )
            if (trip.is_started) {
                currentIcon.src = "https://openmoji.org/php/download_asset.php?type=emoji&emoji_hexcode=1F3C1&emoji_variant=color"
                currentMarker.position = trip.destination
            }
            const updatedPosition = new google.maps.LatLng(e.trip.driver_location.lat, e.trip.driver_location.lng);
            driverMarker.position = updatedPosition;
            console.log("current", currentMarker.position)
            console.log("e", e)
            map.fitBounds(

                new google.maps.LatLngBounds()
                    .extend(currentMarker.position)
                    .extend(driverMarker.position)
            )
        })
        .listen('TripStarted', (e) => {
            trip.$patch(
                e.trip
            )
            title.value = "You're on the way!"
            message.value = `You are headed to ${e.trip.destination_name}`
        })
        .listen('TripEnded', (e) => {
            trip.$patch(
                e.trip
            )
            title.value = "You've arrived!"
            message.value = `Hope you enjoyed your ride with ${e.trip.driver.user.name}`
            setTimeout(() => {
                trip.$reset()
                location.$reset()
                router.push({
                    name: 'landing'
                })
            }, 10000)

        })
})
</script>

<template>
    <div class="w-96">
        <h1>
            {{ title }}
        </h1>

        <div
            ref="mapDiv"
            class="w-full h-[256px]"
        ></div>
        <p>{{ message }}</p>

    </div>
</template>
