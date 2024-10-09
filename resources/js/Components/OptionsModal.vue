<template>
    <!-- Modal Component -->
    <div 
      class="absolute bottom-0 right-24 z-[9999] flex flex-col gap-3 bg-gray-200 rounded-md h-[190px] p-5 font-semibold shadow-lg"
      :style="{ top: position.top, left: position.left }"
    >
        <h1 class="hover:bg-gray-300 hover:cursor-pointer p-2 rounded-md" @click="viewFile">
            <font-awesome-icon :icon="['fas', 'eye']" class="text-md"/> 
            View File
        </h1>

        <h1 class="hover:bg-gray-300 hover:cursor-pointer p-2 rounded-md" @click="downloadFile">
            <font-awesome-icon :icon="['fas', 'download']" class="text-md"/> 
            Download 
        </h1>

        <h1 class="hover:bg-gray-300 hover:cursor-pointer p-2 rounded-md" @click="deleteFile">
            <font-awesome-icon :icon="['fas', 'trash-can']" class="text-md"/> 
            Move to Trash 
        </h1>

        <!-- <h1 class="hover:bg-gray-300 hover:cursor-pointer p-2 rounded-md text-red-500" @click="closeModal">Close</h1> -->

    </div>
  </template>
  
<script setup>
import { onMounted } from 'vue';


    onMounted(() => {
        document.addEventListener('click', handleClickOutside);
    });
  
    const props = defineProps({
        file: {
        type: Object,
        default: () => null,
        },
        position: {
        type: Object,
        required: true,
        }
    });


    const handleClickOutside = (event) => {
        if (modalRef.value && !modalRef.value.contains(event.target)) {
            closeModal();
        }
    };
    
    const closeModal = () => {
        emit('close');
    };
    
    const viewFile = () => {
        if (props.file && props.file.url) {
        window.open(props.file.url, '_blank');
        }
    };
    
    const downloadFile = () => {
        if (props.file && props.file.url) {
        const link = document.createElement('a');
        link.href = props.file.url;
        link.download = props.file.name;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        }
    };
    
    const deleteFile = () => {
        if (props.file) {
        console.log(`Delete action for: ${props.file.name}`);
        // Trigger any delete logic here
        }
    };
</script>
  
<style scoped>
  .modal {
    transition: transform 0.3s ease, opacity 0.3s ease;
  }
</style>
  