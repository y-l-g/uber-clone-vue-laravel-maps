<script setup>

import { onMounted, useTemplateRef } from "vue";
import { Loader } from "@googlemaps/js-api-loader"

const loader = new Loader({
    apiKey: VITE_APP_GOOGLE_MAPS_API_KEY,
    version: "weekly",
    libraries: ["places"],
});

const addressInput = useTemplateRef('addressInput')

onMounted(() => {
    loader.load().then(async () => {
        new google.maps.places.Autocomplete(addressInput.value);
    })
})
</script>

<template>
    <input ref="addressInput"></input>
</template>
