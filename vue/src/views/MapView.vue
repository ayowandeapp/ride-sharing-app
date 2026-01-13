<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">Here's your trip</h1>
        <div>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap
                            v-if="location.destination.name !== ''"
                            :center="location.destination.geometry"
                            :zoom="11"
                            ref="gMap"
                            style="width: 100%; height: 256px"
                        >
                        </GMapMap>
                    </div>
                    <TripSummary :transaction="trip.transaction" />



                    <div class="mt-2">
                        <p class="text-xl">
                            Going to <strong>{{ location.destination.name }}</strong>
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        @click="handleConfirmTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
                    >
                        Let's Go!
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import TripSummary from '@/components/TripSummary.vue'
import http from '@/helpers/http'
import { useDirections } from '@/helpers/mapPathOnMap'
import { useLocationStore } from '@/stores/location'
import { useTripStore } from '@/stores/trip'
import { onMounted, ref, nextTick } from 'vue'
import { useRouter } from 'vue-router'

const location = useLocationStore()
const trip = useTripStore()
const router = useRouter()

const gMap = ref(null)

const { drawDirections } = useDirections()

const handleConfirmTrip = () => {
    http().post('/api/trip', {
        origin: location.current.geometry,
        destination: location.destination.geometry,
        destination_name: location.destination.name,
        total_cost: trip.transaction.total_cost
    })
    .then(res => {
        trip.$patch(res.data)
        router.push({
            name: 'trip'
        })
    })
    .catch(err => console.log(err.response)
    )

}

onMounted(async () => {
    if (location.destination.name === '') {
        router.push({
            name: 'location',
        })
    }

    //get user current location
    await location.updateCurrentLocation()
    // wait for map to render (important because of v-if)
    await nextTick()

    if (!gMap.value) return

    //draw a path on the map
    
    drawDirections({
        gMap,
        origin: location.current,
        destination: location.destination,
    })

    //calculate trip cost
    trip.getTripCost()
})
</script>
