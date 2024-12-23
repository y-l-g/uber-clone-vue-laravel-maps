<script setup>
import http from '@/helpers/http';
import router from '@/router';


const handleStartDriving = () => {
    http().get('/api/driver')
        .then((response) => {
            if (response.data.driver) {
                router.push({
                    name: "standby"
                })
            } else {
                router.push({
                    name: "driver"
                })
            }
        })
        .catch((error) => {
            console.error(error)
        })
}
</script>

<template>
    <div class="w-96">
        <h1>
            Andrewber
        </h1>

        <button @click="handleStartDriving">
            Start Driving
        </button>

        <router-link to="/location">
            Find a Ride
        </router-link>
    </div>
</template>
