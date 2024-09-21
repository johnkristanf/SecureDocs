<script setup>
import Pagination from '../Components/Pagination.vue';

    
</script>

<template>
    <div class="flex flex-col w-full items-start mt-16">

        <div class="w-1/2 flex justify-center font-semibold text-2xl ">
            <h1>Recycle Bin</h1>
        </div>

        <Pagination page="recycleBin" />

    </div>
</template>