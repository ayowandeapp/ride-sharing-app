import { ref, computed, reactive } from 'vue'
import { defineStore } from 'pinia'

export const useTripStore = defineStore('trip', () => {

    const id = ref(null);
    const user_id = ref(null)

    const is_started = ref(null)

    const is_complete = ref(null)

    const origin = reactive({
        lat:null,
        lng:null
    });

    const destination = reactive({
        lat:null,
        lng:null
    });

    const driver_location = reactive({
        lat:null,
        lng:null
    });

    const destination_name = ref('');

    const driver = reactive({
      id:null,
      year: null,
      make: null,
      model: null,
      license_plate: null,
      user: {
        name: null
      }
    });

    const transaction = reactive({
      id: null,
      total_cost: null,
      status: null
    })

    
  const getTripCost = () => {
    const distanceMeters = 12450;
    const durationSeconds = 780 //780s
    const pricing = {
      baseFare: 500,          // ₦500
      costPerKm: 150,         // ₦150 per km
      costPerMinute: 30,      // ₦30 per minute
      minimumFare: 1000, 
    };
    
    const distanceKm = distanceMeters / 1000
    const durationMinutes = durationSeconds / 60

    let cost =
      pricing.baseFare +
      distanceKm * pricing.costPerKm +
      durationMinutes * pricing.costPerMinute

    // enforce minimum fare
    cost = Math.max(cost, pricing.minimumFare)

    console.log('cost', cost)

    transaction.total_cost = cost
  }


    const reset= () => {
      id.value = null,
      user_id.value = null,

      is_started.value = null,

      is_complete.value = null,

      origin.lat = null;
      origin.lng = null;

      destination.lat = null;
      destination.lng = null;

      driver_location.lat = null;
      driver_location.lng = null;

      destination_name.value = ''

      Object.assign(driver, {
        id:null,
        year: null,
        make: null,
        model: null,
        license_plate: null,
        user: {
          name: null
        }
      })
      
      Object.assign(transaction, {
        id:null,
        total_cost: null,
        status: null
      })
    }
  
  return { id, user_id, origin, destination, driver_location, destination_name, is_complete, is_started, driver, reset, transaction, getTripCost }
})
