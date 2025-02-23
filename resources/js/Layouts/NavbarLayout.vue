<template>
    <div class="card">
        <Toolbar class="p-4">
            <template #start>
                <IconField>
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText placeholder="Search" />
                </IconField>
            </template>

            <template #end>
                <SplitButton label="Mouylang" :model="items" class="mr-3"></SplitButton>
                <Avatar image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png" style="width: 32px; height: 32px" />
            </template>
        </Toolbar>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import IconField from "primevue/iconfield";
import InputText from "primevue/inputtext";
import InputIcon from "primevue/inputicon";
import Toolbar from 'primevue/toolbar';
import SplitButton from 'primevue/splitbutton';
import Avatar from 'primevue/avatar';


const items = ref([
    {
        label: 'Accounts',
        icon: 'pi pi-user'
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out'
    }
])
</script>

<style scoped>
/* Add custom styles if necessary */
</style>
