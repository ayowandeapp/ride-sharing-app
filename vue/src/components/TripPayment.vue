<template>
    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
        <button
            :disabled="isLoading"
            @click="initiateCheckout"
            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
        >
            {{ isLoading ? 'Processing...' : 'Proceed to Checkout' }}
        </button>
    </div>

</template>

<script setup>
import http from '@/helpers/http'
import { ref } from 'vue'

const props = defineProps(['trip', 'initialLoading'])

const isLoading = ref(props.initialLoading ?? false)
const checkoutUrl = ref(null)


const initiateCheckout = async () => {
    try {
        isLoading.value = true

        const response = await http().get(`/api/trip/${props.trip.id}/checkout`)

        if (response.data && response.data.url) {
            // Open Stripe checkout in a new tab
            const newWindow = window.open(response.data.url, '_blank')

            if (!newWindow) {
                alert('Please allow pop-ups for this site to complete checkout')
            }
        } else {
            throw new Error('No checkout URL received')
        }
    } catch (error) {
        console.error('Checkout error:', error)
        alert('Failed to initiate checkout. Please try again.')
    } finally {
        isLoading.value = false
    }
}
</script>

<style scoped></style>
