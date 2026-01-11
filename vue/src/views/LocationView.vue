<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">Where are we going?</h1>
        <form @submit.prevent="handleLogin" method="post">
            <div class="overflow-hidden shadow sm:around-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapAutocomplete
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-grey-300 shadow-sm"
                            placeholder="My destination"
                            @place_changed="handleLocationChanged"
                        >
                        </GMapAutocomplete>
                        <!-- <gmp-place-autocomplete
                            ref="autocomplete"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-grey-300 shadow-sm"
                            /> -->
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        @click.prevent="handleSelectLocation"
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
                    >
                        Find A Ride
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { useLocationStore } from '@/stores/location'
import { useRouter } from 'vue-router'

const location = useLocationStore()

const router = useRouter()

const handleLocationChanged = (e) => {
    /* dummy data */
    e = {
        name: 'Shoprite Surulere',
        formatted_address: 'Adeniran Ogunsanya St, Surulere, Lagos, Nigeria',
        geometry: {
            location: {
                lat: () => 6.4922,
                lng: () => 3.3581,
            },
        },
    }
    ///

    location.$patch({
        destination: {
            name: e.name,
            address: e.formatted_address,
            geometry: {
                lat: e.geometry.location.lat(),
                lng: e.geometry.location.lng(),
            },
        },
    })
    console.log('handleLocation', e)
}

const handleSelectLocation = () => {
    if(location.destination.name === '') return;

    router.push({
        name: 'map',
    })
}
</script>
