<template>
    <div>
        <Button
            label="Add payment schedule"
            @click="doShow"
            class="w-full h-9 text-md"
            size="small"
            icon="pi pi-plus"
        />
        <Dialog
            v-model="isShowing"
            class="w-1/4"
            header="Add payment schedule"
            :visible="isShowing"
            @update:visible="isShowing = $event"
            modal
        >
            <AddPayment v-model="model" @cancel="doCancel" @save="doSave" />
        </Dialog>
    </div>
</template>

<script setup>
import AddPayment from "@/Components/Agreements/AddPayment.vue";
import { onMounted, ref } from "vue";
import { useToast } from "primevue/usetoast";
import { Button, Dialog } from "primevue";

const toast = useToast();
const isShowing = ref(false);
const emit = defineEmits(["close", "save", "update"]);
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
    emit("close");
    toast.add({
        severity: "info",
        summary: "Cancelled",
        detail: "Operation was cancelled",
        life: 3000,
    });
};
const doSave = () => {
    isShowing.value = false;
    emit("save", model.value);
    toast.add({
        severity: "success",
        summary: "Success",
        detail: "Payment schedule saved successfully",
        life: 3000,
    });
};
const doShow = () => {
    isShowing.value = true;
    emit("update", isShowing.value);
};
</script>

<style lang="scss" scoped></style>
