import { ref, computed, reactive } from 'vue'
import { defineStore } from 'pinia'

export const useLocationStore = defineStore('location', () => {

  const destination = reactive({
    name: '',
    address: '',
    geometry: {
      lat: null,
      lng: null
    }
  })

  const current = reactive({
    geometry: {
      lat: null,
      lng: null
    }
  })

  const getUserLocation = async () => {
    return new Promise((res, rej) =>{
      navigator.geolocation.getCurrentPosition(res, rej)
    })
  }

  const updateCurrentLocation = async () => {
    const userLocation = await getUserLocation();

    current.geometry = {
      lat: userLocation.coords.latitude,
      lng: userLocation.coords.longitude,
    }
  }

  const reset = () => {
    Object.assign(destination, {
      name: '',
      address: '',
      geometry: {
        lat: null,
        lng: null
      }
    })
    
    Object.assign(current, {
      geometry: {
        lat: null,
        lng: null
      }
    })
  }
  
  return { destination, updateCurrentLocation, current, reset }
})
