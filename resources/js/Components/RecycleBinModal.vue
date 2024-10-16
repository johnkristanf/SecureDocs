<script setup>
    import { router } from '@inertiajs/vue3'
    import Swal from 'sweetalert2';
    import { ref, onMounted, onUnmounted, nextTick } from 'vue';

    const modalRef = ref(null);
    const emit = defineEmits(['close']);

    const props = defineProps({
        document_id: Number,

        document_name: String,

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

    console.log("id: ", props.document_id);
    console.log("file name: ", props.document_name);
    

    const restoreDocument = () => {
        closeModal();

        router.put(`/restore/${props.document_id}`, {
            onSuccess: () => {
                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Document Restored Successfully!",
                    showConfirmButton: false,
                    timer: 1500
                });

            },

            onError: (errors) => console.error("Error in Restoring Document:", errors),
        });
    }

    const deleteFileForever = () => {
        console.log(`Delete action for: ${props.document_name}`);
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

                    if (props.document_id) {
                        const documentName = props.document_name;
                        const documentID = props.document_id;

                        console.log(`Delete action for: ${documentName}`);

                        router.delete(`/delete/${documentID}/${documentName}`, {
                            onSuccess: () => {
                                Swal.fire({
                                    position: "top-end",
                                    icon: "success",
                                    title: "Document Forever Deleted!",
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

    const closeModal = () => emit('close');


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
      class="absolute bottom-0 right-24 z-[9999] flex flex-col gap-3 bg-gray-200 rounded-md h-[130px] p-5 font-semibold shadow-lg"
      :style="{ top: position.top, left: position.left }"
    >
        
        <h1 class="hover:bg-gray-300 hover:cursor-pointer p-2 rounded-md" @click="restoreDocument">
            <font-awesome-icon :icon="['fas', 'clock-rotate-left']" class="text-md" /> 
            Restore 
        </h1>

        <h1 class="hover:bg-gray-300 hover:cursor-pointer p-2 rounded-md" @click="deleteFileForever">
            <font-awesome-icon :icon="['fas', 'trash-can']" class="text-md"/> 
            Delete Forever
        </h1>

    </div>
</template>


<style scoped>
    .modal {
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
</style>
