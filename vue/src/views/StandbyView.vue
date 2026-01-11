<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>
        <div v-if="!trip.id" class="mt-8 flex justify-center">
            <Loader />
        </div>
        <div v-else>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap
                            :center="trip.destination"
                            :zoom="14"
                            ref="gMap"
                            style="width: 100%; height: 256px"
                        >
                        </GMapMap>
                    </div>
                    <div class="mt-2">
                        <p class="text-xl">
                            Going to <strong>{{ trip.destination_name }}</strong>
                        </p>
                    </div>
                </div>
                <div class="flex justify-between bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        @click="handleDeclineTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
                    >
                        Decline
                    </button>
                    <button
                        @click="handleAcceptTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
                    >
                        Accept
                    </button>
                </div>
            </div>
        </div>
        <!-- <div>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">stand by</div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <span> when a driver accepts the trip, their info will appear here. </span>
                </div>
            </div>
        </div> -->
    </div>
</template>

<script setup>
import Loader from '@/components/loader.vue'
import echo from '@/helpers/echo'
import http from '@/helpers/http'
import { useDirections } from '@/helpers/mapPathOnMap'
import { useLocationStore } from '@/stores/location'
import { useTripStore } from '@/stores/trip'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const location = useLocationStore()

const trip = useTripStore()

const router = useRouter()

const { drawDirections } = useDirections()

const title = ref('Waiting for ride request...')
const gMap = ref(null)

const handleDeclineTrip = () => {
    trip.reset()
    title.value = 'Waiting for ride request...'
}

const handleAcceptTrip = () => {
    http()
        .post(`/api/trip/${trip.id}/accept`, {
            driver_location: location.current.geometry,
        })
        .then((res) => {
            location.$patch({
                destination: {
                    name: 'Passenger',
                    geometry: res.data.origin,
                },
            })

            router.push({ name: 'driving' })
        })
        .catch((err) => console.log(err, 'err'))
}

onMounted(async () => {
    await location.updateCurrentLocation()

    echo.channel('drivers').listen('TripCreated', (e) => {
        title.value = 'Ride requested:'

        trip.$patch(e.trip)
        console.log('TripCreated', e)

        setTimeout(() => {
            drawDirections({
                gMap,
                origin: trip.origin,
                destination: trip.destination,
            })
        }, 2000)
    })

    echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ Pusher connected!')
    })
})

// const initMapDirections = initMapDirections(gMap, trip)
</script>
