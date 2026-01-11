<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>
        <div>
            <div v-if="!trip.is_complete" class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap
                            :zoom="14"
                            ref="gMap"
                            :center="location.current.geometry"
                            style="width: 100%; height: 256px"
                        >
                            <GMapMarker :icon="currentIcon" :position="location.current.geometry" />
                            <GMapMarker
                                :icon="destinationIcon"
                                :position="location.destination.geometry"
                            />
                        </GMapMap>
                    </div>
                    <div class="mt-2">
                        <p class="text-xl">Going to <strong>pick up a passenger</strong></p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button v-if="trip.is_started"
                        @click="handleCompleteTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
                    >
                        Complete Trip
                    </button>
                    <button v-else
                        @click="handlePassengerPickedUp"                        
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
                    >
                        Passenger Picked Up
                    </button>
                </div>
            </div>
            <div v-else class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <Tada />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Tada from '@/components/Tada.vue'
import http from '@/helpers/http'
import { useLocationStore } from '@/stores/location'
import { useTripStore } from '@/stores/trip'
import { onMounted, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const location = useLocationStore()
const trip = useTripStore()

const router = useRouter()

const gMap = ref(null)

const intervalRef = ref(null);

const title = ref('Driving to passenger...');

const currentIcon = {
    url: 'https://openmoji.org/data/color/svg/1F698.svg',
    scaledSize: {
        width: 24,
        height: 24,
    },
}

const destinationIcon = {
    url: 'https://openmoji.org/data/color/svg/1F920.svg',
    scaledSize: {
        width: 24,
        height: 24,
    },
}

const handlePassengerPickedUp = () =>{
    http().post(`/api/trip/${trip.id}/start`, )
        .then(res => {
            title.value = 'Travelling to destination....'
            location.$patch({
                destination: {
                    name: res.data.destination_name,
                    geometry: res.data.destination
                }
            })

            trip.$patch(res.data)
        })
        .catch(err => console.log('err', err))
}

const handleCompleteTrip = () =>{
    http().post(`/api/trip/${trip.id}/end`, )
        .then(res => {
            title.value = 'Trip Completed....'
            
            trip.$patch(res.data)

            setTimeout(() => {

                trip.reset()
                location.reset()
    
                router.push({name:'standby'})
            }, 3000)

        })
        .catch(err => console.log('err', err))
}
//adject the bound to fit the origin and destination
const updateMapBounds = (mapObject) => {
    let originPoint = new google.maps.LatLng(location.current.geometry),
        destinationPoint = new google.maps.LatLng(location.destination.geometry),
        latLngBounds = new google.maps.LatLngBounds()

    latLngBounds.extend(originPoint)
    latLngBounds.extend(destinationPoint)

    mapObject.fitBounds(latLngBounds)
}

const broadcastSriverLocation = () => {
    http()
        .post(`/api/trip/${trip.id}/location`, {
            driver_location: location.current.geometry,
        })
        .then((res) => console.log(res.data))
        .catch((err) => console.log(err))
}
onMounted(() => {
    //wait for when the map loads and
    gMap.value.$mapPromise.then((mapObject) => {
        updateMapBounds(mapObject)

        intervalRef.value = setInterval(async () => {
            //update the drivers current position and
            await location.updateCurrentLocation()

            //update the drivers location in the database
            broadcastSriverLocation()

            //update the map bounds
            updateMapBounds(mapObject)
        }, 5000)
    })
})

onUnmounted(() => {
    clearInterval(intervalRef.value)
    intervalRef.value = null
})
</script>
