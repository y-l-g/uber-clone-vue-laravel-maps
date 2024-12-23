import { defineStore } from 'pinia'
import { Loader } from '@googlemaps/js-api-loader';

export const useLoaderStore = defineStore('loader', () => {
    const loader = new Loader({
        apiKey: VITE_APP_GOOGLE_MAPS_API_KEY,
        version: "weekly",
        libraries: ["maps", "places"],
    });

    return { loader }
})
