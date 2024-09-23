<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import secureDocsLogo from '../../../public/img/securedocs_logo.png';

const links = [
    {name:"Project Files", route: 'project_files.page', component: 'ProjectFiles', icon: ['fas', 'file']},
    {name:"Recycle Bin", route: 'recycle_bin.page', component: 'RecycleBin', icon: ['fas', 'trash-can']},
];

const logout = useForm({
    payload: null
});

const isModalOpen = ref(false);

const toggleModal = () => {
    isModalOpen.value = !isModalOpen.value;
};

const closeModalWhenSelect = () =>{
    isModalOpen.value = false
}
</script>

<template>
    <div
        class="fixed top-0 left-0 flex flex-col p-5 w-[20%] h-screen gap-5 items-center font-semibold text-black border-r border-gray-300"
    >
        <a href="#" class="flex items-center mb-6 mt-10 text-3xl font-semibold ">
            <img class="w-12 h-12 mr-2 rounded-full" :src="secureDocsLogo" alt="logo">
            <div class="flex flex-col">
                <h1 class="dark:text-white">SecureDocs</h1>
                <h1 class="text-sm text-gray-500 opacity-75 dark:text-gray-200">Trusted Cloud</h1>
            </div>  
        </a>

        <div class="flex flex-col gap-12 w-full mt-3 font-semibold text-[17px]">
            <Link 
                v-for="link in links"
                :key="link.route"
                :href="$page.component == link.component ? null : route(link.route)"
                :class="[ 
                    'p-2 rounded-md w-full text-center dark:text-white dark:hover:bg-white dark:hover:text-black', 
                    $page.component == link.component ? 'bg-blue-600 text-white' : 'hover:bg-black hover:text-white' 
                ]"
            >
                <font-awesome-icon :icon="link.icon" class="text-xl"/>
                {{ link.name }}
            </Link>

            <!-- Settings Button with Modal Toggle -->
            <button 
                @click="toggleModal" 
                class="relative p-2 rounded-md w-full text-center hover:bg-black hover:text-white dark:text-white dark:hover:bg-white dark:hover:text-black"
            >
                <font-awesome-icon :icon="['fas', 'gear']" class="text-xl"/> 
                Settings 
                <font-awesome-icon :icon="['fas', 'chevron-right']" class="absolute top-3 right-2" />
            </button>

        </div>

        <!-- User Profile and Sign Out -->
        <div class="w-[80%] absolute bottom-6 font-semibold flex flex-col justify-center items-center gap-4">
            <div class="w-full flex items-center justify-center">
                <img class="w-10 h-10 mr-2 rounded-full" :src="secureDocsLogo" alt="logo">

                <div class="flex flex-col dark:text-white">
                    <h1 class="truncate">{{ $page.props.auth.user.fullname }}</h1>
                    <p class="truncate text-gray-600 dark:text-gray-300 text-[13px]">{{ $page.props.auth.user.email }}</p>
                </div>
            </div>

            <button 
                @click="logout.post(route('logout'))"
                class="w-full bg-gray-200 rounded-md p-2 hover:opacity-75"
            >
                <font-awesome-icon :icon="['fas', 'right-from-bracket']" /> Sign Out
            </button>
        </div>

        <!-- Modal for Settings Options (Account and Preferences) -->
        <div v-if="isModalOpen" class="absolute top-20 left-52 ml-2 w-full h-full flex items-center justify-center z-50">

            <div class="bg-gray-100 rounded-lg w-[150px] p-2 shadow-lg">

                <div class="flex flex-col gap-4 ">
                    <Link 
                        :href="route('profile.page')"
                        @click="closeModalWhenSelect"
                        class="text-left bg-gray-100 p-2 rounded-md hover:bg-gray-200 text-center">
                        <font-awesome-icon :icon="['fas', 'user']" /> My Profile
                    </Link>

                    <Link 
                        :href="route('apperance.page')"
                        @click="closeModalWhenSelect"
                        class="text-left bg-gray-100 p-2 rounded-md hover:bg-gray-200 text-center">
                        <font-awesome-icon :icon="['fas', 'paintbrush']" /> Apperance
                    </Link>

                </div>
            </div>

        </div>
    </div>
</template>
