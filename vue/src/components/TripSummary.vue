<div class="bg-white px-4 py-5 sm:p-6"></div><template>
    
    <div class="max-w-sm mx-auto mt-6 rounded-2xl bg-white shadow-lg p-5">
        <h2 class="text-lg font-semibold mb-4">Trip Summary</h2>

        <div class="space-y-3 text-sm">

            <div class="flex justify-between font-semibold text-lg">
                <span>Total</span>
                <span>₦{{ transaction.total_cost }}</span>
            </div>
        </div>
    </div>
</template>
<script setup>
const props = defineProps(['transaction'])

console.log(props.transaction)
</script>
<style>
</style>