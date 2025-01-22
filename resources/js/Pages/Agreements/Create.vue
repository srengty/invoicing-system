<template>
    <Head title="Create Agreement" />
    <GuestLayout>
        <h1>Create Agreement</h1>
        <form @submit.prevent="submit">
            <div class="create-agreement flex flex-row gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-1">
                        <span>Quotation No.</span>
                        <input v-model="form.quotation_no" />
                        <span>Agreement No.</span>
                        <input v-model="form.agreement_no" />
                        <span>Date</span>
                        <input type="date" pattern="dd/mm/yyyy" v-model="form.date" />
                        <span>Customer</span>
                        <input v-model="form.customer" />
                        <span>Address</span>
                        <input v-model="form.address" />
                        <span>Agreement doc</span>
                        <input type="file" @input="form.avatar = $event.target.files[0]" />
                        <span :class="[{ 'hidden': !form.progress }]">Uploading</span>
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-1">
                        <span class="col-span-2 text-xl mb-5">Agreement summary</span>
                        <span>Start date</span>
                        <input />
                        <span>End date</span>
                        <input />
                        <span>Agreement amount (exclude tax)</span>
                        <input type="date" pattern="dd/mm/yyyy" />
                        <span>Tax amount</span>
                        <input />
                        <span>Payment schedule</span>
                        <input />
                        <span>Agreement doc</span>
                        <input type="file" @input="form.avatar = $event.target.files[0]" />
                        <span :class="[{ 'hidden': !form.progress }]">Uploading</span>
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-1">
                        <span>Attachment</span>
                        <input type="file" @input="form.avatar = $event.target.files[0]" />
                        <span :class="[{ 'hidden': !form.progress }]">Uploading</span>
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                    </div>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
const toast = useToast();
const form = useForm({
    quotation_no: null,
    agreement_no: null,
    date: '21/01/2025',
    customer: null,
    address: null,
    avatar: null,
    progress: null,
});
const submit = () => {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Agreement created successfully' });
}
</script>

<style lang="scss" scoped></style>