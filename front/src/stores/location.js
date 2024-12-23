import { reactive, ref } from 'vue'
import { defineStore } from 'pinia'


const getUserLocation = async () => {
    return new Promise((res, rej) => {
        navigator.geolocation.getCurrentPosition(res, rej)
    })
}

function generateRandomCoordinate(min, max) {
    return (Math.random() * (max - min) + min);
}

export const useLocationStore = defineStore('location', () => {
    const destination = ref({
        name: '',
        address: "",
        geometry: {
            lat: null,
            lng: null
        }
    })

    const current = ref({
        geometry: {
            lat: null,
            lng: null
        }
    })

    const updateCurrentLocation = async () => {
        const userLocation = await getUserLocation()
        current.value.geometry = {
            // lat: userLocation.coords.latitude,
            // lng: userLocation.coords.longitude
            lat: generateRandomCoordinate(48.8, 48.9),
            lng: generateRandomCoordinate(2, 3)
        }
    }

    const $reset = () => {
        destination.value = {
            name: '',
            address: "",
            geometry: {
                lat: null,
                lng: null
            }
        }
        current.value = {
            lat: null,
            lng: null
        }
    }


    return {
        destination,
        current,
        updateCurrentLocation,
        $reset
    }
})
