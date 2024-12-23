<script setup>
import { vMaska } from "maska/vue"
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import http from "@/helpers/http";

const router = useRouter();

const email = ref(null)
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
    http().post('/api/login', {
        email: email.value
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
    http().post('/api/login/verify', {
        login_code: login_code.value,
        email: email.value
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
                Enter your Email
            </h1>

            <form
                action=""
                @submit.prevent="handleLogin"
            >
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="your@email.com"
                    v-model="email"
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
