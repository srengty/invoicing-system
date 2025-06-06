<script setup>
import { ref, onMounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
import MainNavTree from "./MainNavTree.vue";

const isDark = ref(false);

onMounted(() => {
  const saved = localStorage.getItem("dark-mode");
  if (saved !== null) {
    isDark.value = saved === "true";
  } else {
    isDark.value = window.matchMedia("(prefers-color-scheme: dark)").matches;
  }
  updateDarkClass();
});

function toggleDarkMode() {
  isDark.value = !isDark.value;
  localStorage.setItem("dark-mode", isDark.value.toString());
  updateDarkClass();
}

function updateDarkClass() {
  const html = document.documentElement;
  if (isDark.value) {
    html.classList.add("dark");
  } else {
    html.classList.remove("dark");
  }
}
</script>

<template>
  <div
    class="flex min-h-screen flex-col items-center bg-gray-100 sm:justify-center dark:bg-[#000000]"
  >
    <div
      class="w-full h-screen overflow-y-auto bg-white shadow-md sm:rounded-lg  dark:bg-[#1d1d1d]  grid grid-cols-1 md:grid-cols-6"
    >
      <div
        class="row-span-2 bg-slate-100 dark:bg-[#0a0a0a] sticky top-0 h-screen overflow-auto flex flex-col"
      >
        <div class="flex flex-col items-center justify-center py-2 pb-5 mb-3">
          <Link href="/dashboard">
            <img src="/logo.png" alt="Logo" class="h-20 w-20" />
          </Link>
          <div class="text-sm mb-2 text-gray-700 dark:text-gray-300 font-bold">
            <p>Institute of Technology of Cambodia</p>
          </div>
        </div>
        <MainNavTree />
      </div>

      <div class="md:col-span-5">
        <slot />
      </div>
    </div>
  </div>
</template>
