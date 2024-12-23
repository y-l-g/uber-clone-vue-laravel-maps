<script setup>

import Tada from "@/components/Tada.vue";
import http from "@/helpers/http";
import router from "@/router";
import { useLocationStore } from "@/stores/location";
import { useTripStore } from "@/stores/trip";
import { Loader } from "@googlemaps/js-api-loader"
import { onMounted, onUnmounted, ref } from "vue";
const loader = new Loader({
    apiKey: VITE_APP_GOOGLE_MAPS_API_KEY,
    version: "weekly",
    libraries: ["maps", "places", "marker", "core"],
});

const destinationMarker = ref(null)
const intervalRef = ref(null)

const currentIcon = document.createElement("img");

const title = ref('Driving to passenger...')
const message = ref('pick up a passenger')

currentIcon.src =
    "https://openmoji.org/php/download_asset.php?type=emoji&emoji_hexcode=1F698&emoji_variant=color";

currentIcon.width = 24
currentIcon.height = 24
const destinationIcon = document.createElement("img");


destinationIcon.width = 24
destinationIcon.height = 24
const location = useLocationStore()
const trip = useTripStore()
const mapDiv = ref(null)

destinationIcon.src =
    "https://openmoji.org/php/download_asset.php?type=emoji&emoji_hexcode=1F920&emoji_variant=color";


onMounted(async () => {
    await loader.load()
    const position = { lat: -25.344, lng: 131.031 };
    const mapOptions = {
        zoom: 14,
        mapId: "mapId"
    }
    const map = new google.maps.Map(mapDiv.value, mapOptions); console.log(location)
    const currentMarker = new google.maps.marker.AdvancedMarkerElement({
        map,
        position: location.current.geometry,
        title: 'Origin',
        content: currentIcon
    });
    destinationMarker.value = new google.maps.marker.AdvancedMarkerElement({
        map,
        position: location.destination.geometry,
        title: 'Destination',
        content: destinationIcon
    });

    updateMapBounds(map)
    intervalRef.value = setInterval(async () => {
        await location.updateCurrentLocation()
        currentMarker.position = location.current.geometry
        if (trip.is_started) {
            destinationIcon.src = "https://openmoji.org/php/download_asset.php?type=emoji&emoji_hexcode=1F3C1&emoji_variant=color"
            destinationMarker.value.position = location.destination.geometry

        }
        updateMapBounds(map)
        broadcastDriverLocation();
    }, 2000)
})

const handlePassengerPickedUp = () => {
    http().post(`/api/trip/${trip.id}/start`)
        .then((response) => {
            title.value = "Travelling to destination"
            message.value = "destination"
            location.$patch({
                destination: {
                    name: response.data.destination_name,
                    geometry: response.data.destination
                }
            })
            trip.$patch(response.data)
            destinationMarker.value.location = null
        })
        .catch((error) => {
            console.log(error)
        })
}

const handleCompleteTrip = () => {
    http().post(`/api/trip/${trip.id}/end`)
        .then((response) => {
            title.value = "Trip Completed"
            trip.$patch(response.data)

            setTimeout(() => {
                trip.$reset()
                location.$reset()
                router.push({
                    name: "standby"
                })
            }, 10000)
        })
        .catch((error) => {
            console.error(error)
        })
}

onUnmounted(() => {
    clearInterval(intervalRef.value)
    intervalRef.value = null
})

const updateMapBounds = (map) => {
    map.fitBounds(
        new google.maps.LatLngBounds()
            .extend(new google.maps.LatLng(location.current.geometry))
            .extend(new google.maps.LatLng(location.destination.geometry))
    )
}

const broadcastDriverLocation = () => {
    http().post(`/api/trip/${trip.id}/location`, {
        driver_location: location.current.geometry
    })
        .then((response) => {

        })
        .catch((error) => {
            console.error(error)
        })
}
</script>

<template>
    <div
        class="w-96"
        v-if="!trip.is_complete"
    >
        <h1>{{ title }}</h1>
        <div
            ref="mapDiv"
            class="w-full h-[256px]"
        ></div>
        <p>Going to <strong>{{ message }}</strong></p>
        <button
            v-if="trip.is_started"
            @click="handleCompleteTrip"
        >Complete trip</button>
        <button
            v-else
            @click="handlePassengerPickedUp"
        >Passenger picked Up</button>


    </div>

    <div
        class="w-96"
        v-else
    >
        <Tada></Tada>
    </div>
</template>
