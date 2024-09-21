<script setup>
    import { useForm } from '@inertiajs/vue3';
import secureDocsLogo from '../../../public/img/securedocs_logo.png';

    const links = [
        {name:"Project Files", route: 'project_files.page', component: 'ProjectFiles', icon: ['fas', 'file']},
        {name:"Recycle Bin", route: 'recycle_bin.page', component: 'RecycleBin', icon: ['fas', 'trash-can']},
        {name:"Settings", route: 'settings.page', component: 'Settings', icon: ['fas', 'gear']},
    ]

    const logout = useForm({
        payload: null
    })
    
</script>

<template>
    <div
        class="fixed top-0 left-0 flex flex-col p-5 w-[20%] h-screen gap-5 items-center font-semibold text-black border-r border-gray-300"
    >
        <a href="#" class="flex items-center mb-6 mt-10 text-3xl font-semibold ">
            <img class="w-12 h-12 mr-2 rounded-full" :src="secureDocsLogo" alt="logo">
            <div class="flex flex-col">
                <h1>SecureDocs</h1>
                <h1 class="text-sm text-gray-500 opacity-75">Trusted Cloud</h1>
            </div>  
        </a>

        <div class="flex flex-col gap-12 w-full mt-3 font-semibold text-[17px]">
            
            <Link 
                v-for="link in links"
                :key="link.route"
                :href="$page.component == link.component ? null : route(link.route)"
                :class="[
                    'p-2 rounded-md w-full text-center',
                    $page.component == link.component ? 'bg-blue-600 text-white' : 'hover:bg-black hover:text-white'
                ]"
            >
                <font-awesome-icon :icon="link.icon" class="text-xl"/>
                {{ link.name }}
            </Link>

        </div>

        <div class="absolute bottom-6 font-semibold flex flex-col justify-center gap-4">
            <div class="flex items-center">
                <img class="w-10 h-10 mr-2 rounded-full" :src="secureDocsLogo" alt="logo">

                <div class="flex flex-col">
                    <h1 class="truncate">{{ $page.props.auth.user.fullname }}</h1>
                    <p class="truncate text-gray-600 text-[13px]">{{ $page.props.auth.user.email }}</p>
                </div>
            </div>

            <button 
                @click="logout.post(route('logout'))"
                class="bg-gray-200 rounded-md p-2 hover:opacity-75"
                >
                <font-awesome-icon :icon="['fas', 'right-from-bracket']" /> Sign Out
            </button>
        </div>

    </div>
</template>