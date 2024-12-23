<script setup>
import { vMaska } from "maska/vue"
import axios from "axios";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
const router = useRouter();

const phone = ref(null)
const login_code = ref(null)
const waitingOnVerification = ref(false)

onMounted(() => {
    if (localStorage.getItem('token')) {
        router.push({
            name: 'landing'
        })
    }
})

const handleLogin = () => {
    axios.post('http://ride-share.test/api/login', {
        phone: phone.value
    })

        .then((response) => {
            console.log(response.data)
            waitingOnVerification.value = true
        })
        .catch((error) => {
            console.log(error)
            alert(error.response.data.message)
        })
}

const handleVerify = () => {
    axios.post('http://ride-share.test/api/login/verify', {
        login_code: login_code.value,
        phone: phone.value
    })
        .then((response) => {
            console.log(response.data)
            localStorage.setItem('token', response.data)
            router.push({
                name: 'landing'
            })
        })
        .catch((error) => {
            console.log(error)
            alert(error.response.data.message)
        })
}
</script>
<template>
    <div>
        <div v-if="!waitingOnVerification">
            <h1>
                Enter your phone number
            </h1>

            <form
                action=""
                @submit.prevent="handleLogin"
            >
                <input
                    type="text"
                    name="phone"
                    id="phone"
                    placeholder="336595959"
                    v-maska
                    data-maska="###########"
                    v-model="phone"
                >
                <button
                    type="submit"
                    @submit.prevent="handleLogin"
                >Log In</button>
            </form>
        </div>
        <div>
            <h1>
                Enter your login code
            </h1>
            <form
                action=""
                @submit.prevent="handleVerify"
            >
                <input
                    type="text"
                    name="login_code"
                    id="login_code"
                    placeholder="123456"
                    v-maska
                    data-maska="######"
                    v-model="login_code"
                >
                <button
                    type="submit"
                    @submit.prevent="handleVerify"
                >Submit</button>
            </form>
        </div>
    </div>

</template>
