<template>
    <div class="main-nav-wrapper">
        <nav class="main-nav relative">
            <ol>
                <li :class="{ active: $page.url === '/' }">
                    <Link href="/">Dashboard</Link>
                </li>
                <li :class="{ active: $page.url === '/quotations' }">
                    <Link href="/quotations">Quotations</Link>
                </li>
                <li :class="{ active: $page.url === '/agreements' }">
                    <Link href="/agreements">Agreements</Link>
                </li>
                <li :class="{ active: $page.url === '/invoices' }">
                    <Link href="/invoices">Invoices</Link>
                </li>
                <li :class="{ active: $page.url === '/receives' }">
                    <Link href="/receives">Receives</Link>
                </li>
                <li :class="{ active: $page.url === '/settings' }">
                    <Link href="/settings">Settings</Link>
                </li>
            </ol>
        </nav>
    </div>
</template>
<script setup>
import { Link, usePage } from "@inertiajs/vue3";
const page = usePage();
</script>
<style>
.main-nav-wrapper {
    @apply pl-8;
}
.main-nav ol {
    list-style: none;
    counter-reset: muffins;
}
.main-nav ol li {
    counter-increment: muffins;
    line-height: 1.6rem;
}
.main-nav ol::before {
    position: absolute;
    top: 8px;
    left: 0.6rem;
    z-index: 1;
    content: "\00a0";
    width: 0.3rem;
    height: 80%;
    @apply bg-green-600;
    margin-right: 8px;
}
.main-nav ol li::before {
    content: "\00a0\00a0"counter(muffins);
    color: white;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    @apply bg-green-600;
    margin-top: 0.25rem;
    position: absolute;
    z-index: 2;
    left: 0px;
}
.main-nav ol li a {
    line-height: 2rem;
    padding-left: 2rem;
}
.main-nav ol li:hover {
    @apply bg-green-100;
}
.main-nav ol li.active {
    @apply bg-green-200;
}
</style>
