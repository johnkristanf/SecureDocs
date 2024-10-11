<script setup>
    import { router } from '@inertiajs/vue3'
    import Swal from 'sweetalert2';
    import { ref, onMounted, onUnmounted, nextTick } from 'vue';

    const modalRef = ref(null);
    const emit = defineEmits(['close']);

    const props = defineProps({
        file: {
            type: Object,
            default: () => null,
        },
        position: {
            type: Object,
            required: true,
        },

        ellipsisRef: Object
    });

    const handleClickOutside = (event) => {
        if (modalRef.value && !modalRef.value.contains(event.target) && !(props.ellipsisRef && props.ellipsisRef.contains(event.target))) {
            closeModal();
        }
    };

    const closeModal = () => emit('close');


    const viewFile = () => {
        const viewerURL = `https://docs.google.com/viewer?url=${encodeURIComponent(props.file.url)}&embedded=true` 
        window.open(viewerURL, '_blank');
        closeModal();
    };

    const downloadFile = () => {
        if (props.file && props.file.url) {
            const link = document.createElement('a');
            link.href = props.file.url;
            link.download = props.file.name;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            closeModal();
        }
    };

    const deleteFile = () => {
        console.log(`Delete action for: ${props.file.name}`);
        closeModal();

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    if (props.file) {
                        const documentName = props.file.name
                        console.log(`Delete action for: ${documentName}`);

                        router.delete(`/delete/${documentName}`, {
                            onSuccess: () => {
                                Swal.fire({
                                    position: "top-end",
                                    icon: "success",
                                    title: "Document Deleted Successfully",
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            },

                            onError: (errors) => console.error("Error in Deleting Document:", errors),
                        });
                    }
                }
        });
    };

    // Use nextTick to ensure the DOM is fully rendered before accessing the ref
    onMounted(async () => {
        await nextTick();
        console.log("Modal Ref after DOM is rendered:", modalRef.value);
        document.addEventListener('click', handleClickOutside);
    });

    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
    });
</script>


<template>
    <div 
      ref="modalRef"
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

    </div>
</template>


<style scoped>
    .modal {
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
</style>
