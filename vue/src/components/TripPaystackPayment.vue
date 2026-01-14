<template>
    <div v-if="showPaymentStatus" class="bg-gray-50 px-4 py-3 text-right sm:px-6">
        <h3>Payment Successful! 🎉</h3>
    </div>
    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
        <button
            :disabled="isLoading"
            @click="initiateCheckout"
            class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none"
        >
            {{ isLoading ? 'Processing...' : 'Proceed to Paystack' }}
        </button>
    </div>
</template>

<script setup>
import http from '@/helpers/http'
import { ref } from 'vue'
import PaystackPop from '@paystack/inline-js'

const props = defineProps(['trip'])

const isLoading = ref(props.initialLoading ?? false)
const checkoutUrl = ref(null)
const showPaymentStatus = ref(false)

const initiateCheckout = async () => {
    try {
        isLoading.value = true

        const response = await http().get(`/api/trip/${props.trip.id}/paystack/checkout`)

        if (response.data && response.data.status) {
            const data = response.data
            console.log('data', data)
            // Open popup
            openPaymentLink(data)
            // const newWindow = window.open(response.data.url, '_blank')

            // if (!newWindow) {
            //     alert('Please allow pop-ups for this site to complete checkout')
            // }
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

const openPaymentLink = (data) => {
    if (data.status) {
        const popup = new PaystackPop()
        popup.resumeTransaction(data.data.access_code)

        // Start polling for payment verification
        startPaymentVerification(data.data.reference)
    }
}
const startPaymentVerification = async (transactionReference) => {
    if (!transactionReference) return

    // Poll for payment status every 5 seconds
    const interval = setInterval(async () => {
        try {
            const response = await http().get(`/api/paystack-checkout/verify`, {
                params: { reference: transactionReference },
            })

            if (response.data.status) {
                const data = response.data.data

                if (data.status === 'success') {
                    clearInterval(interval)
                    handlePaymentSuccess(data)
                } else if (data.status === 'failed') {
                    clearInterval(interval)
                    handlePaymentFailed(data)
                }
            }
        } catch (error) {
            console.error('Payment verification error:', error)
        }
    }, 5000) // Poll every 5 seconds

    // Stop polling after 10 minutes
    setTimeout(
        () => {
            clearInterval(interval)
        },
        10 * 60 * 1000,
    )
}

const handlePaymentSuccess = (data) => {
    showSuccessModal.value = 'Payment completed successfully!'
}
const handlePaymentFailed = (data) => {
    showSuccessModal.value = 'Payment failed. Please try again.'
}
</script>

<style scoped></style>
