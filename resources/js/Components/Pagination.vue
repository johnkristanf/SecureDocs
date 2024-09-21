<script setup>
    import secureDocsLogo from '../../../public/img/securedocs_logo.png';

    const props = defineProps({
        page: String
    })

    const fileTypeFilter = [
        {name: "View All", active: true},
        {name: "Documents", active: false},
        {name: "Spreadsheets", active: false},
        {name: "PDF", active: false},
        {name: "Images", active: false},
    ]

</script>

<template>
    <div class="w-full flex justify-end">
        <div class="flex flex-col w-[80%] mt-7">
            <div class="overflow-x-auto shadow-md sm:rounded-lg">
                <div class="inline-block min-w-full align-middle">
                    <div class="flex justify-between items-center w-full mb-4 font-semibold">

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

                        <div v-if="props.page == 'projectFiles'" class="w-[30%] mr-5">
                            <input 
                                type="search" 
                                placeholder="Search"
                                class="w-full bg-slate-200 p-2 rounded-md focus:outline-blue-500"
                            />
                        </div>

                        <div v-if="props.page == 'recycleBin'" class="flex justify-between gap-3 w-full bg-gray-300 p-3 rounded-md">
                            <h1 class="font-normal">Items in recycle bin will be deleted after 30 days</h1>
                            <h1 
                                class="hover:opacity-75 hover:cursor-pointer text-gray-600"
                            >
                                Empty Trash
                            </h1>

                        </div>

                    </div>
                    <div class="overflow-hidden ">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                                        File Name
                                    </th>
                                    <th v-if="props.page == 'projectFiles'" scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                                        Uploaded By
                                    </th>
                                    <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                                        {{ page == "projectFiles" ? 'Uploaded Date': 'Deleted Date' }}
                                    </th>

                                    
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 relative">

                                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    document.docs
                                </td>

                                <td v-if="props.page == 'projectFiles'" class="flex items-center py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">

                                    <img class="w-8 h-8 mr-2 rounded-full" :src="secureDocsLogo" alt="logo">

                                    <div class="flex flex-col">
                                        <h1>John Kristan F. Torremocha</h1> 
                                        <p class="opacity-75">jake@gmail.com</p> 
                                    </div>
                                </td>

                                <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Jan 4, 2002
                                </td>

                                <div class="absolute right-5 top-3 text-xl hover:cursor-pointer hover:opacity-75">
                                    <font-awesome-icon :icon="['fas', 'ellipsis-vertical']" />
                                </div>
                            
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
</template>