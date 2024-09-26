<script setup>
    import Pagination from '../Components/Pagination.vue';
    import { useForm } from '@inertiajs/vue3';

    const formData = useForm({
        file: null
    });

    const uploadTypes = [
        { name: "New Document", icon: ['fas', 'file-lines'], accept: '.doc,.docx' },
        { name: "New Spreadsheet", icon: ['fas', 'table'], accept: '.xls,.xlsx,.csv' },
        { name: "New PDF", icon: ['fas', 'file-pdf'], accept: '.pdf' },
        { name: "New Image", icon: ['fas', 'image'], accept: 'image/*' },
    ];

    const uploadedSizes = [
        { name: "Document", icon: ['fas', 'file-lines'], size: "16gb", bg: "bg-violet-800" },
        { name: "Spreadsheet", icon: ['fas', 'table'], size: "7gb", bg: "bg-blue-800" },
        { name: "PDF", icon: ['fas', 'file-pdf'], size: "8gb", bg: "bg-green-800" },
        { name: "Image", icon: ['fas', 'image'], size: "10gb", bg: "bg-orange-800" },
    ];

    const uploadDocuments = () => {
        formData.post(route('document.upload'), {
            preserveScroll: true,

            onSuccess: () => formData.reset(),
            onError: (errors) => console.error('Error in uploading document: ', errors)
        })
    }

    const handleFileChange = (event, type) => {
        const file = event.target.files[0];
        if (file) {
            formData.file = file
            console.log(`Uploaded ${file.name} as ${type.name}`);
        }

        uploadDocuments();
        
    };


    const triggerFileInput = (index) => {
        const fileInput = document.getElementById(`file-input-${index}`);
        if (fileInput) fileInput.click();
    };

</script>

<template>
    <Head title="Project Files" />

    <div class="w-[55%] flex justify-center ">
        <h1 class="text-3xl font-semibold dark:text-white">Project Files</h1>
    </div>

    <div class="w-full flex justify-end mt-12 gap-8 ">
        <div 
            v-for="(type, index) in uploadTypes"
            :key="type.name"
            class="w-[18%] h-32 rounded-md border border-gray-200 p-3 hover:cursor-pointer hover:shadow-md hover:shadow-black transition-shadow duration-300 dark:text-white dark:hover:shadow-white hover:shadow-md"
            @click="triggerFileInput(index)"
        >
            <div class="flex flex-col justify-around items-start gap-7 h-full">
                <div class="flex w-full justify-between ">
                    <font-awesome-icon :icon="type.icon" class="bg-black text-white rounded-md p-3"/>
                    <font-awesome-icon :icon="['fas', 'plus']" class="text-lg"/>
                </div>
                <h1 class="font-semibold text-lg">{{ type.name }}</h1>
            </div>

            <input 
                type="file" 
                :accept="type.accept" 
                :id="`file-input-${index}`" 
                class="hidden" 
                @change="(event) => handleFileChange(event, type)" 
            />
        </div>
    </div>

    <div class="flex flex-col w-full items-start mt-12">
        <div class="w-[58%] flex justify-center font-semibold text-xl dark:text-white">
            <h1>Total Uploaded File Size</h1>
        </div>

        <div class="w-full flex justify-end mt-4 gap-8 ">
            <div 
                v-for="size in uploadedSizes"
                :key="size.name"
                class="w-[18%] h-32 rounded-md border border-gray-200 p-3 font-semibold flex flex-col gap-1 items-center justify-center dark:text-white"
            >
                <font-awesome-icon 
                    :icon="size.icon" 
                    :class="`text-xl ${size.bg} rounded-full text-white p-3`"
                />
                <h1>{{ size.name }}</h1>
                <p class="opacity-75">{{ size.size }}</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col w-full items-start mt-16">
        <div class="w-[48%] flex justify-center font-semibold text-2xl ">
            <h1 class="dark:text-white">All File</h1>
        </div>

        <Pagination page="projectFiles" />
    </div>
</template>
