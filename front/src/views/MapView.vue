<script setup>
import { useLocationStore } from "@/stores/location";
import { onMounted, ref, useTemplateRef } from "vue";
import { Loader } from "@googlemaps/js-api-loader"
import http from "@/helpers/http";
import { useRouter } from "vue-router";
import { useTripStore } from "@/stores/trip";

const router = useRouter();

const loader = new Loader({
    apiKey: VITE_APP_GOOGLE_MAPS_API_KEY,
    version: "weekly",
    libraries: ["maps", "places", "marker", "core"],
});
const mapDiv = ref(null)
const location = useLocationStore();
const trip = useTripStore()

const handleConfirmTrip = () => {
    http().post('/api/trip', {
        origin: location.current.geometry,
        destination: location.destination.geometry,
        destination_name: location.destination.name,
    })
        .then((response) => {
            trip.$patch(response.data)
            router.push({
                path: '/trip'
            })
        })
        .catch((error) => {
            console.error(error)
        })
}
if (location.destination.name === '') {
    router.push({
        name: 'location'
    })
}

onMounted(
    async () => {
        await loader.load()
        await location.updateCurrentLocation()
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer();
        console.log("current", location.current)
        const currentPoint = new google.maps.LatLng(location.current.geometry);
        const destinationPoint = new google.maps.LatLng(location.destination.geometry);
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
)

</script>

<template>
    <div class="w-96">
        <h1>
            Here's your trip
        </h1>

        <p>Going to <strong>{{ location.destination.name }}</strong></p>
        <div
            ref="mapDiv"
            class="w-full h-[256px]"
        ></div>
        <button @click="handleConfirmTrip">Let's Go!</button>

    </div>
</template>
