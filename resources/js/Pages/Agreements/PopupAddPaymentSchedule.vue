<template>
    <div>
        <Button label="Add payment schedule" @click="isShowing=true" />
        <Dialog v-model="isShowing" header="Add payment schedule" :visible="isShowing" @update:visible="isShowing = $event">
            <AddPayment v-model="model" @cancel="doCancel" @save="doSave"/>
        </Dialog>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import AddPayment from '@/Components/Agreements/AddPayment.vue';
    import { Button, Dialog } from 'primevue';
    const isShowing = ref(false);
    const emit = defineEmits(['close', 'save']);
    const model = defineModel({
        type: Object,
    });
    const doCancel = () => {
        isShowing.value = false;
        emit('close');
    }
    const doSave = () => {
        isShowing.value = false;
        emit('save', model.value);
    }
</script>

<style lang="scss" scoped>

</style>