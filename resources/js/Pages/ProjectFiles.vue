<script setup>
    import { reactive, watch } from 'vue';
    import Pagination from '../Components/Pagination.vue';
    import { useForm } from '@inertiajs/vue3';
    import Swal from 'sweetalert2';
    import { ThemeSelector } from '../../helpers/ThemeSelector';

    ThemeSelector();


    const props = defineProps({
        documents: Array,
        sizes: Object,
    })

    const formData = useForm({
        file: null
    });

    const uploadTypes = [
        { name: "New Document", icon: ['fas', 'file-lines'], accept: '.doc,.docx' },
        { name: "New Spreadsheet", icon: ['fas', 'table'], accept: '.xls,.xlsx,.csv' },
        { name: "New PDF", icon: ['fas', 'file-pdf'], accept: '.pdf' },
        { name: "New Image", icon: ['fas', 'image'], accept: 'image/*' },
    ];

    const uploadedSizes = reactive([
        { name: "Documents", icon: ['fas', 'file-lines'], size: "0MB", bg: "bg-violet-800" },
        { name: "Spreadsheets", icon: ['fas', 'table'], size: "0MB", bg: "bg-blue-800" },
        { name: "PDFs", icon: ['fas', 'file-pdf'], size: "0MB", bg: "bg-green-800" },
        { name: "Images", icon: ['fas', 'image'], size: "0MB", bg: "bg-orange-800" },
    ]);


    watch(() => props.sizes, (newSizes) => {
        uploadedSizes.forEach((uploadSize) => {
            const groupName = uploadSize.name;  
            if (newSizes[groupName] !== undefined && newSizes[groupName] < 1000) {
                uploadSize.size = `${newSizes[groupName]}MB`; 
            } else {
                uploadSize.size = `${newSizes[groupName]}GB`; 
            }
        });
    }, { immediate: true });

    console.log("uploadedSizes: ", uploadedSizes)

    const uploadDocuments = () => {
        formData.post('/upload', {
            preserveScroll: true,

            onSuccess: () => {
                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Document Uploaded Successfully",
                    showConfirmButton: false,
                    timer: 1500
                });

                formData.reset()
            },
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

    console.log("sizes: ", props.sizes)

</script>

<template>
    <Head title="Project Files" />

    <div class="w-full md:w-[55%] flex justify-center ">
        <h1 class="text-3xl font-semibold dark:text-white">Project Files</h1>
    </div>

    <div class="w-full flex justify-end mt-12 gap-8 ">
        <div 
            v-for="(type, index) in uploadTypes"
            :key="type.name"
            class="w-[20%] md:w-[18%] h-32 rounded-md border border-gray-200 p-3 hover:cursor-pointer hover:shadow-md hover:shadow-black transition-shadow duration-300 dark:text-white dark:hover:shadow-white hover:shadow-md"
            @click="triggerFileInput(index)"
        >
            <div class="flex flex-col justify-around items-start gap-7 h-full">
                <div class="flex w-full justify-between ">
                    <font-awesome-icon :icon="type.icon" class="bg-black dark:bg-gray-600 text-white rounded-md p-3"/>
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
        <div class="w-full md:w-[58%] flex justify-center font-semibold text-xl dark:text-white">
            <h1>Total Uploaded File Size</h1>
        </div>

        <div class="w-full flex  justify-start md:justify-end mt-4 gap-12 md:gap-8">
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
        <div class="w-full md:w-[60%] flex justify-center font-semibold text-2xl ">
            <h1 class="dark:text-white">Uploaded Documents</h1>
        </div>

        <Pagination page="projectFiles" :documents="props.documents" />
    </div>
</template>
