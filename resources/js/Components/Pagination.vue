<script setup>
import { usePage } from '@inertiajs/vue3';
import secureDocsLogo from '../../../public/img/securedocs_logo.png';

// Define the props using defineProps
const fileProps = defineProps({
  page: String,
  files: {
    type: Array,  
    default: () => []  
  }
});

const fileTypeFilter = [
    {name: "View All", active: true},
    {name: "Documents", active: false},
    {name: "Spreadsheets", active: false},
    {name: "PDF", active: false},
    {name: "Images", active: false},
]


const { props } = usePage();
const user = props.auth.user;

console.log("User data: ", user.fullname);
console.log("Documents in table: ", fileProps.files);
</script>

<template>
  <div class="w-full flex justify-end">
    <div class="flex flex-col w-[80%] mt-7">
      <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <div class="inline-block min-w-full align-middle">
          <div class="flex justify-between items-center w-full mb-4 font-semibold">
            <!-- Use fileProps instead of props for page references -->
            <div v-if="fileProps.page === 'projectFiles'" class="w-1/2 flex items-center gap-10 bg-gray-200 rounded-md p-2">
              <h1 
                v-for="type in fileTypeFilter"
                :key="type.name"
                :class="[
                  'hover:opacity-75 hover:cursor-pointer',
                  type.active ? 'bg-white p-2 rounded-md': ''
                ]"
              >
                {{ type.name }}
              </h1>
            </div> 
            
            <div v-if="props.page == 'projectFiles'" class="w-1/2 flex items-center gap-10 bg-gray-200 rounded-md p-2">
                <h1 
                    v-for="type in fileTypeFilter"
                    :key="type.name"
                    :class="[
                        'hover:opacity-75 hover:cursor-pointer',
                        type.active ? 'bg-white p-2 rounded-md': ''
                    ]"
                >
                    {{ type.name }}
                </h1>
                            
            </div>   

            <div v-if="fileProps.page === 'projectFiles'" class="w-[30%] mr-5">
              <input 
                type="search" 
                placeholder="Search"
                class="w-full bg-slate-200 p-2 rounded-md focus:outline-blue-500"
              />
            </div>

            <div v-if="fileProps.page === 'recycleBin'" class="flex justify-between gap-3 w-full bg-gray-300 p-3 rounded-md">
              <h1 class="font-normal">Items in recycle bin will be deleted after 30 days</h1>
              <h1 class="hover:opacity-75 hover:cursor-pointer text-gray-600">
                Empty Trash
              </h1>
            </div>
          </div>

          <!-- Display the table with the correct user data -->
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
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 relative">
                <!-- Loop through the files -->
                <tr v-for="file in fileProps.files" :key="file.name">
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

                    <div class="absolute right-5 top-5 text-xl hover:cursor-pointer hover:opacity-75">
                        <font-awesome-icon :icon="['fas', 'ellipsis-vertical']" />
                    </div>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
