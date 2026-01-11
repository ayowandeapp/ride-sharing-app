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
    }
  
  return { id, user_id, origin, destination, driver_location, destination_name, is_complete, is_started, driver, reset }
})
