import axios from "axios"

const http = () => {
    const options = {
        baseURL: import.meta.env.VITE_BASE_URL,
        headers: {}
    }

    if (localStorage.getItem('token')) {
        options.headers.Authorization = `Bearer ${localStorage.getItem('token')}`
    }

    return axios.create(options)
}

export default http
