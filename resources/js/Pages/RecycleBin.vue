<script setup>

    import Pagination from '../Components/Pagination.vue';
    import { ThemeSelector } from '../../helpers/ThemeSelector';

    ThemeSelector();


    const props = defineProps({
        documents: Array,
    })

</script>

<template>
    <div class="flex flex-col w-full items-start">

        <div class="w-full md:w-[52%] flex justify-center font-semibold text-3xl dark:text-white">
            <h1>Recycle Bin</h1>
        </div>

        <div class="w-full flex justify-end mt-6">
            <div class="w-full md:w-[80%] bg-gray-200 flex justify-center p-3 rounded-md font-semibold">
                <h1>Items in Recycle Bin wil be deleted forever after 30days</h1>
            </div>
        </div>
        

        <Pagination page="recycleBin" :documents="props.documents" />

    </div>
</template>