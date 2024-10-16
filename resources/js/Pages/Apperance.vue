<script setup>
    import secureDocsLogo from '../../../public/img/securedocs_logo.png';
    import { ThemeSelector } from '../../helpers/ThemeSelector';

    ThemeSelector();
 

    const setLightTheme = () => {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }

    const setNightTheme = () => {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }


    const themes = [
        {name: "Light Theme", img_src: secureDocsLogo, icon: ['fas', 'sun'], themeSelected: setLightTheme},
        {name: "Night Theme", img_src: secureDocsLogo, icon: ['fas', 'moon'], themeSelected: setNightTheme},
    ]

</script>

<template>
    <div class="flex flex-col w-full items-center ">
        <div class="relative w-full md:w-1/2 ">
            <h1 class="font-semibold text-2xl relative z-10 dark:text-white ml-12 md:ml-0">Apperance</h1>
            
            <div class="absolute inset-0 flex items-center justify-center mt-10">
                <div class="w-full h-[2px] bg-gray-400 -z-10 "></div>
            </div>
        </div>

        <h1 class="mt-8 dark:text-white">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Illo non, blanditiis aspernatur facilis.</h1>

        <div class="w-full md:w-1/2 flex justify-around gap-16 gap-5 mt-9 font-semibold">
            
            <div 
                v-for="theme in themes"
                :key="theme.name"
                class="flex flex-col items-center w-1/2 h-64 border border-gray-400 p-3 rounded-md hover:shadow-black hover:shadow-md transition-shadow duration-300 hover:cursor-pointer dark:text-white dark:hover:shadow-white hover:shadow-md"
                @click="theme.themeSelected()"
            >
                <div class="flex items-center gap-3">
                    <h1 class="text-xl">{{ theme.name }}</h1>
                    <font-awesome-icon 
                        :icon="theme.icon" 
                        :class="`text-3xl ${theme.name == 'Light Theme' ? 'text-yellow-600': 'text-gray-400 '}`"
                    />
                </div>

                <img :src="theme.img_src" alt="Light Theme Web" width="220">
            </div>

            
        </div>

        
    </div>

</template>
