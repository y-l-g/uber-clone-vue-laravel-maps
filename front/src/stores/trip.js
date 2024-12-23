import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useTripStore = defineStore('trip', () => {
    const id = ref(null)
    const user_id = ref(null)

    const origin = ref({
        lat: null,
        lng: null
    })

    const destination = ref({
        lat: null,
        lng: null
    })

    const driver_location = ref({
        lat: null,
        lng: null
    })

    const destination_name = ref('')

    const driver = ref({
        id: null,
        year: null,
        make: null,
        model: null,
        license_plate: null,
        user: {
            name: null
        }
    })

    const is_started = ref(false)
    const is_complete = ref(false)


    const $reset = () => {
        id.value = null
        user_id.value = null
        origin.value = {
            lat: null,
            lng: null
        }
        destination.value = {
            lat: null,
            lng: null
        }
        destination_name.value = ''
        is_complete.value = false
        is_started.value = false
        driver_location.value = {
            lat: null,
            lng: null
        }
        driver.value = {
            id: null,
            year: null,
            make: null,
            model: null,
            license_plate: null,
            user: {
                name: null
            }
        }
    }

    return {
        id,
        user_id,
        origin,
        destination,
        destination_name,
        is_complete,
        is_started,
        driver_location,
        driver,
        $reset
    }
})
