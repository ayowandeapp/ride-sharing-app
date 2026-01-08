<template>
    <div class="pt-16">
        <div v-if="!waitingOnVerification">
            <h1 class="text-3xl font-semibold mb-4">Enter phone number</h1>
            <form  @submit.prevent="handleLogin" method="post">
                <div class="overflow-hidden shadow sm:around-md max-w-sm mx-auto text-left">
                    <div class="bg-white px-4 py-5 sm:p-6">
                        <div>
                            <input
                                v-model="credentials.phone"
                                type="text"
                                v-maska="'(+234) ### ### ####'"
                                data-maska-placeholder="_"
                                name="phone"
                                id="phone"
                                placeholder="+2349090909090"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-grey-300 shadow-sm"
                            />
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                        <button
                            type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
                        >
                            Continue
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div v-else>
            <h1 class="text-3xl font-semibold mb-4">Enter verification code</h1>
            <form  @submit.prevent="handleVerification" method="post">
                <div class="overflow-hidden shadow sm:around-md max-w-sm mx-auto text-left">
                    <div class="bg-white px-4 py-5 sm:p-6">
                        <div>
                            <input
                                v-model="credentials.login_code"
                                type="text"
                                v-maska="'######'"
                                data-maska-placeholder="_"
                                name="phone"
                                id="phone"
                                placeholder="123456"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-grey-300 shadow-sm"
                            />
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                        <button
                            type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
                        >
                            Verify
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</template>
<script setup>
import axios from 'axios'
import { vMaska } from 'maska/vue'
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter();

const credentials = reactive({
    phone: null,
})

const waitingOnVerification = ref(true);

onMounted(() => {
    if(localStorage.getItem('token')){
        router.push({
            name: 'landing'
        })
    }
})

const getformattedCredentials = () =>{
    return {
        phone: credentials.phone.replaceAll(' ', '').replace('(+', '').replace(')', ''),
        login_code: credentials.login_code
    }
}

const handleLogin = () => {
    axios
        .post('http://127.0.0.1:8000/api/login', getformattedCredentials())
        .then((res) => {
            console.log(res.data)
            waitingOnVerification.value = true
        })
        .catch((err) => {
            console.log(err)
            alert(err.response.data.message)
        })
}

const handleVerification = () =>{
    axios.post('http://127.0.0.1:8000/api/login/verify', getformattedCredentials())
    .then((res) => {
        console.log(res.data)
        localStorage.setItem('token', res.data.token)
        router.push({
            name: 'landing'
        })
    })
    .catch((err) => {
        console.log(err)
        alert(err.response.data.message)
    })
}
</script>
<style scoped></style>
