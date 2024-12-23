<script setup>
import { useLocationStore } from "@/stores/location";
import { onMounted, useTemplateRef } from "vue";
import { Loader } from "@googlemaps/js-api-loader"

const loader = new Loader({
    apiKey: VITE_APP_GOOGLE_MAPS_API_KEY,
    version: "weekly",
    libraries: ["maps", "places", "marker", "core"],
});


const addressInput = useTemplateRef('addressInput')

const location = useLocationStore();

onMounted(() => {
    loader.load().then(async () => {
        const options = {
            componentRestrictions: { country: "fr" },
            fields: ["formatted_address", "geometry", "name"],
            strictBounds: false,
        };
        const autocomplete = new google.maps.places.Autocomplete(addressInput.value, options);
        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();
            console.log(place)
            location.$patch({
                destination: {
                    name: place.name,
                    address: place.formatted_address,
                    geometry: {
                        lat: place.geometry.location.lat(),
                        lng: place.geometry.location.lng()
                    }
                }
            })
        })
    })
})
</script>

<template>
    <div class="w-96">
        <h1>Where are we going?</h1>
        <input ref="addressInput"></input>
        <router-link
            v-if="location.destination.name != ''"
            to="/map"
        >
            Find a Ride
        </router-link>
    </div>
</template>
