<template>
    <div>
        <Button label="Add payment schedule" @click="doShow" />
        <Dialog v-model="isShowing" header="Add payment schedule" :visible="isShowing" @update:visible="isShowing = $event" modal>
            <AddPayment v-model="model" @cancel="doCancel" @save="doSave"/>
        </Dialog>
    </div>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import AddPayment from '@/Components/Agreements/AddPayment.vue';
    import { Button, Dialog } from 'primevue';
    const isShowing = ref(false);
    const emit = defineEmits(['close', 'save', 'update']);
    const model = defineModel({
        type: Object,
    });
    // const props = defineProps({
    //     name: {
    //         type: Array,
    //         default: () => ({}),
    //     },
    // });
    // onMounted(() => {
    //     console.log('props', props.name);
    // });
    const doCancel = () => {
        isShowing.value = false;
        emit('close');
    }
    const doSave = () => {
        isShowing.value = false;
        emit('save', model.value);
    }
    const doShow = () => {
        isShowing.value = true;
        emit('update', isShowing.value);
    }
</script>

<style lang="scss" scoped>

</style>