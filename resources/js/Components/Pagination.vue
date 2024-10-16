<script setup>
  import { ref } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import secureDocsLogo from '../../../public/img/securedocs_logo.png';
  import OptionsModal from './OptionsModal.vue';
  import RecycleBinModal from './RecycleBinModal.vue';

  const fileProps = defineProps({
    page: String,
    documents: {
      type: Array,
      default: () => []
    },
  });

  console.log("documents: ", ref(fileProps.documents));
  

  const { props } = usePage();
  const user = props.auth.user;

  const showOptionsModal = ref(false);
  const showRecycleBinModal = ref(false);

  const modalPosition = ref({ top: '0px', left: '0px' });

  const documentId = ref(null);
  const documentName = ref(null);

  const selectedFile = ref(null);
  const ellipsisRef = ref(null);


  // Options Modal
  const handleOptionsModal = (event, file, documentID) => {
    
    const rect = event.currentTarget.getBoundingClientRect();

    modalPosition.value = {
      top: `${rect.top + window.scrollY + 20}px`, 
      left: `${rect.left + window.scrollX - 300}px` 
    };
    
    ellipsisRef.value = event.currentTarget; // Store the ellipsis icon reference
    selectedFile.value = file;  
    documentId.value = documentID
    showOptionsModal.value = true;    

  };

  const closeOptionsModal = () => {
    showOptionsModal.value = false;
    selectedFile.value = null;
    ellipsisRef.value = null;
  };


  // RecycleBin Modal

  const handleRecycleBinModal = (event, document_name, document_id) => {
    
    const rect = event.currentTarget.getBoundingClientRect();

    modalPosition.value = {
      top: `${rect.top + window.scrollY + 20}px`, 
      left: `${rect.left + window.scrollX - 300}px` 
    };
    
    ellipsisRef.value = event.currentTarget;
    documentName.value = document_name;  
    documentId.value = document_id
    showRecycleBinModal.value = true;    

  };

  const closeRecycleBinModal = () => {
    showRecycleBinModal.value = false;
    documentName.value = null;  
    documentId.value = null
    ellipsisRef.value = null;
  };


  const formatDate = (dateString) => {
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric', 
        hour: 'numeric', 
        minute: 'numeric', 
        hour12: true 
    };
    return new Date(dateString).toLocaleString('en-US', options);
};




</script>

<template>

  <OptionsModal
    v-if="showOptionsModal"
    :style="{ top: modalPosition.top, left: modalPosition.left }"
    :file="selectedFile"
    :position="modalPosition"
    :document_id="documentId"
    @close="closeOptionsModal"
    :ellipsisRef="ellipsisRef"
  />

  <RecycleBinModal 
    v-if="showRecycleBinModal"
    :style="{ top: modalPosition.top, left: modalPosition.left }"
    :position="modalPosition"
    :document_id="documentId"
    :document_name="documentName"
    @close="closeRecycleBinModal"
    :ellipsisRef="ellipsisRef"

  />

  <!-- Table structure -->
  <div class="w-full flex justify-end">
    <div class="flex flex-col w-full md:w-[80%] mt-7">
      <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <div class="inline-block min-w-full align-middle">
          <div class="flex justify-end items-center w-full mb-4 font-semibold">

            <!-- <div v-if="fileProps.page === 'projectFiles'" class="w-1/2 flex items-center gap-10 bg-gray-200 rounded-md p-2">
              <h1 v-for="type in fileTypeFilter" :key="type.name" :class="[
                  'hover:opacity-75 hover:cursor-pointer',
                  type.active ? 'bg-white p-2 rounded-md' : ''
                ]">
                {{ type.name }}
              </h1>
            </div> -->

            <div v-if="fileProps.page === 'projectFiles'" class="w-[30%] mr-5">
              <input type="search" placeholder="Search" class="w-full bg-slate-200 p-2 rounded-md focus:outline-blue-500" />
            </div>

          </div>

          <!-- Table Body -->
          <div class="overflow-hidden max-h-96 overflow-y-auto">

            <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                  <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                    File Name
                  </th>

                  <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                    Uploaded By
                  </th>
                  
                  <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                    {{ fileProps.page === "projectFiles" ? 'Uploaded Date' : 'Deleted Date' }}
                  </th>

                  <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                  </th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">

                <tr v-for="document in fileProps.documents" :key="document.name">

                  <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ document.name }}
                  </td>

                  <td class="flex items-center py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <img class="w-8 h-8 mr-2 rounded-full" :src="secureDocsLogo" alt="logo">
                    <div class="flex flex-col">
                      <h1>{{ user ? user.fullname : 'Unknown User' }}</h1>
                      <p class="opacity-75">{{ user ? user.email : 'No Email' }}</p>
                    </div>
                  </td>

                  <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ formatDate(fileProps.page === 'recycleBin' ? document.updated_at : document.created_at) }}
                  </td>


                  <td class="py-4 px-6 text-lg font-medium text-gray-900 whitespace-nowrap dark:text-white ">
                    <font-awesome-icon 
                      :icon="['fas', 'ellipsis-vertical']" 
                      @click="fileProps.page == 'recycleBin' ? handleRecycleBinModal($event, document.name, document.id) : handleOptionsModal($event, document, document.id)" 
                      class="p-2 rounded-full hover:cursor-pointer hover:opacity-75 hover:bg-gray-300" 
                    />
                  </td>

                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
