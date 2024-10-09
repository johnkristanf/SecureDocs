<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import secureDocsLogo from '../../../public/img/securedocs_logo.png';
import OptionsModal from './OptionsModal.vue';

const fileProps = defineProps({
  page: String,
  documents: {
    type: Array,
    default: () => []
  },
});

const { props } = usePage();
const user = props.auth.user;

const showModal = ref(false);
const modalPosition = ref({ top: '0px', left: '0px' });
const selectedFile = ref(null);


const handleEllipsisClick = (event, file) => {
  
  const rect = event.currentTarget.getBoundingClientRect();

  modalPosition.value = {
    top: `${rect.top + window.scrollY + 20}px`, 
    left: `${rect.left + window.scrollX - 300}px` 
  };
  
  selectedFile.value = file;  
  showModal.value = true;    
};

const closeModal = () => {
  showModal.value = false;
  selectedFile.value = null;
};

</script>

<template>

  <OptionsModal
    v-if="showModal"
    :style="{ top: modalPosition.top, left: modalPosition.left }"
    :file="selectedFile"
    :position="modalPosition"
    @close="closeModal"
  />

  <!-- Table structure -->
  <div class="w-full flex justify-end">
    <div class="flex flex-col w-[80%] mt-7">
      <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <div class="inline-block min-w-full align-middle">
          <div class="flex justify-between items-center w-full mb-4 font-semibold">

            <div v-if="fileProps.page === 'projectFiles'" class="w-1/2 flex items-center gap-10 bg-gray-200 rounded-md p-2">
              <h1 v-for="type in fileTypeFilter" :key="type.name" :class="[
                  'hover:opacity-75 hover:cursor-pointer',
                  type.active ? 'bg-white p-2 rounded-md' : ''
                ]">
                {{ type.name }}
              </h1>
            </div>
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

                  <th v-if="fileProps.page === 'projectFiles'" scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                    Uploaded By
                  </th>
                  
                  <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                    {{ fileProps.page === "projectFiles" ? 'Uploaded Date' : 'Deleted Date' }}
                  </th>

                  <th v-if="fileProps.page === 'projectFiles'" scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                  </th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">

                <tr v-for="file in fileProps.documents" :key="file.name">

                  <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ file.name }}
                  </td>

                  <td v-if="fileProps.page === 'projectFiles'" class="flex items-center py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <img class="w-8 h-8 mr-2 rounded-full" :src="secureDocsLogo" alt="logo">
                    <div class="flex flex-col">
                      <h1>{{ user ? user.fullname : 'Unknown User' }}</h1>
                      <p class="opacity-75">{{ user ? user.email : 'No Email' }}</p>
                    </div>
                  </td>

                  <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    Jan 4, 2002
                  </td>

                  <td class="py-4 px-6 text-lg font-medium text-gray-900 whitespace-nowrap dark:text-white ">
                    <font-awesome-icon 
                      :icon="['fas', 'ellipsis-vertical']" 
                      @click="handleEllipsisClick($event, file)" 
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
