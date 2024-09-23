<script setup>
import Pagination from '../Components/Pagination.vue';

    
</script>

<template>
    <div class="flex flex-col w-full items-start">

        <div class="w-[52%] flex justify-center font-semibold text-3xl dark:text-white">
            <h1>Recycle Bin</h1>
        </div>

        <Pagination page="recycleBin" />

    </div>
</template>