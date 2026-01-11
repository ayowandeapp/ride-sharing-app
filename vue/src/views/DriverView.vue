<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">Driver and car details</h1>
        <form  @submit.prevent="handleSaveDriver" method="post">
                <div class="overflow-hidden shadow sm:around-md max-w-sm mx-auto text-left">
                    <div class="bg-white px-4 py-5 sm:p-6">
                        <div>
                            <input
                                v-model="driverDetails.name"
                                type="text"
                                name="name"
                                id="name"
                                placeholder="Full Name"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-grey-300 shadow-sm"
                            />
                        </div>
                        <div class="mt-2">
                            <input
                                v-model="driverDetails.year"
                                type="number"
                                name="year"
                                id="year"
                                placeholder="Car Year"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-grey-300 shadow-sm"
                            />
                        </div>
                        <div class="mt-2">
                            <input
                                v-model="driverDetails.make"
                                type="text"
                                name="make"
                                id="make"
                                placeholder="Make"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-grey-300 shadow-sm"
                            />
                        </div>
                        <div class="mt-2">
                            <input
                                v-model="driverDetails.model"
                                type="text"
                                name="model"
                                id="model"
                                placeholder="Model"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-grey-300 shadow-sm"
                            />
                        </div>
                        <div class="mt-2">
                            <input
                                v-model="driverDetails.color"
                                type="text"
                                name="color"
                                id="color"
                                placeholder="Color"
                                class="mt-1 block w-full px-3 py-2 rounded-md border border-grey-300 shadow-sm"
                            />
                        </div>
                        <div class="mt-2">
                            <input
                                v-model="driverDetails.license_plate"
                                type="text"
                                name="license_plate"
                                id="license_plate"
                                placeholder="License Plate #"
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
</template>

<script setup>
import http from '@/helpers/http'
import { useLocationStore } from '@/stores/location'
import { reactive } from 'vue'
import { useRouter } from 'vue-router'

const location = useLocationStore()
const router = useRouter()

const driverDetails = reactive({
    name: '',
    year: null,
    make: '',
    model: '',
    color: '',
    license_plate: '',
})

const handleSaveDriver = () =>{
    http().post('/api/driver', driverDetails)
    .then(res => {
        router.push({name:'standby'})
    })
}
</script>
