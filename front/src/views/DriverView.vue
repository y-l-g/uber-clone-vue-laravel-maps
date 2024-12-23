<script setup>
import http from '@/helpers/http';
import router from '@/router';
import { ref } from 'vue';


const driverDetails = ref({
    name: "",
    year: "",
    model: "",
    make: "",
    color: "",
    license_plate: ""
})
const handleSaveDriver = () => {
    http().post('/api/driver', driverDetails.value)
        .then((response) => {
            router.push({
                name: 'standby'
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
            Driver and Car Details
        </h1>

        <form
            action=""
            @submit.prevent="handleSaveDriver"
        >
            <input
                type="text"
                name="name"
                id="name"
                placeholder="name"
                v-model="driverDetails.name"
            >
            <input
                type="number"
                name="year"
                id="year"
                placeholder="year"
                v-model="driverDetails.year"
            >
            <input
                type="text"
                name="make"
                id="make"
                placeholder="make"
                v-model="driverDetails.make"
            >
            <input
                type="text"
                name="model"
                id="model"
                placeholder="model"
                v-model="driverDetails.model"
            >
            <input
                type="text"
                name="color"
                id="color"
                placeholder="color"
                v-model="driverDetails.color"
            >
            <input
                type="text"
                name="license_plate"
                id="license_plate"
                placeholder="license_plate"
                v-model="driverDetails.license_plate"
            >
            <button @click.prevent="handleSaveDriver">Continue</button>
        </form>
    </div>
</template>
