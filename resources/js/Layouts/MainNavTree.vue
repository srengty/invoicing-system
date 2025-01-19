
<template>
    <div class="card flex justify-center">
        <PanelMenu :model="items" class="w-full md:w-80" :pt="{panel:{class:'border-0 bg-transparent'}}">
            <template #item="{ item }">
                <div :class="['flex items-center cursor-pointer group',{'active': page.url === item.href}]">
                    <Link :href="item.href" v-ripple class="flex items-center px-4 py-2 group">
                        <span :class="['rounded-full me-2 w-8 h-8 bg-green-300 flex items-center justify-center',{'hidden':!item.id}]">{{item.id}}</span>
                        <span :class="[item.icon, 'text-primary group-hover:text-inherit']" />
                        <span :class="['ml-2', { 'font-semibold': item.items }]">{{ item.label }}</span>
                    </Link>
                    <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                    <span v-if="item.shortcut" class="ml-auto rounded text-muted-color text-xl p-1 w-8"><span class="pi pi-angle-right"/></span>
                </div>
            </template>
        </PanelMenu>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { PanelMenu,Badge } from "primevue";
import { Link, usePage } from "@inertiajs/vue3";
const page = usePage();
const items = ref([
    {
        id: 1,
        label: 'Dashboard',
        href: '/',
        icon: 'pi pi-home',
        badge: null,
        items: [
            {
                label: 'Compose',
                href: '/compose',
                icon: 'pi pi-file-edit',
                shortcut: '⌘+N'
            },
            {
                label: 'Inbox',
                href: '/inbox',
                icon: 'pi pi-inbox',
                badge: 5
            },
            {
                label: 'Sent',
                href: '/sent',
                icon: 'pi pi-send',
                shortcut: '⌘+S'
            },
            {
                label: 'Trash',
                href: '/trash',
                icon: 'pi pi-trash',
                shortcut: '⌘+T'
            }
        ]
    },
    {
        id: 2,
        label: 'Quotations',
        href: '/quotations',
        icon: 'pi pi-chart-bar',
        shortcut: '⌘+R',
        items: [
            {
                label: 'Sales',
                href: '/sales',
                icon: 'pi pi-chart-line',
                badge: 3
            },
            {
                label: 'Products',
                href: '/products',
                icon: 'pi pi-list',
                badge: 6
            }
        ]
    },
    {
        id: 3,
        label: 'Agreements',
        href: '/agreements',
        icon: 'pi pi-user',
        shortcut: '>',
        items: [
            {
                label: 'Settings',
                href: '/settings',
                icon: 'pi pi-cog',
                shortcut: '⌘+O'
            },
            {
                label: 'Privacy',
                href: '/privacy',
                icon: 'pi pi-shield',
                shortcut: '⌘+P'
            }
        ]
    },
    {
        id: 4,
        label: 'Invoices',
        href: '/invoices',
        icon: 'pi pi-money-bill',
        shortcut: '⌘+W',
        items: [
            {
                label: 'Settings',
                href: '/settings',
                icon: 'pi pi-cog',
                shortcut: '⌘+O'
            },
            {
                label: 'Privacy',
                href: '/privacy',
                icon: 'pi pi-shield',
                shortcut: '⌘+P'
            }
        ]
    }
]);
</script>
<style>
    :root {
        --p-panelmenu-panel-background: transparent;
        --p-panelmenu-panel-border-color: transparent;
        --p-panelmenu-item-focus-background: #fff;
    }
    .active {
        @apply bg-primary text-white;
    }
</style>