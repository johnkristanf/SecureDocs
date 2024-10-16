<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import secureDocsLogo from '../../../public/img/securedocs_logo.png';
import defaultProfile from '../../../public/img/default_profile.png';

const { props } = usePage();
const picture = ref(props.picture);

// Store sidebar visibility state
const isSidebarOpen = ref(false);

// Manage window width reactivity
const windowWidth = ref(window.innerWidth); // Initialize with the current window width

// Event listener to track window resize
const updateWindowWidth = () => {
  windowWidth.value = window.innerWidth;
};

onMounted(() => {
  // Add event listener to track window resize
  window.addEventListener('resize', updateWindowWidth);
});

onUnmounted(() => {
  // Cleanup event listener when component is destroyed
  window.removeEventListener('resize', updateWindowWidth);
});

// Computed property to determine whether to show the sidebar
const showSidebar = computed(() => {
  return isSidebarOpen.value || windowWidth.value >= 1024;
});

// Modal and links management
const isModalOpen = ref(false);
const links = [
  { name: "Project Files", route: 'project_files.page', component: 'ProjectFiles', icon: ['fas', 'file'] },
  { name: "Recycle Bin", route: 'recycle_bin.page', component: 'RecycleBin', icon: ['fas', 'trash-can'] },
];

const logout = useForm({
  payload: null
});

const toggleModal = () => {
  isModalOpen.value = !isModalOpen.value;
};

const closeModalWhenSelect = () => {
  isModalOpen.value = false;
};

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<template>
  <!-- Toggle Button for Small Screens -->
  <button 
    @click="toggleSidebar" 
    class="lg:hidden p-3 fixed top-5 left-5 z-50 bg-black text-white rounded-md dark:bg-white dark:text-black">
    <font-awesome-icon :icon="['fas', 'bars']" class="text-2xl" />
  </button>

  <!-- Sidebar (Hidden by default on small screens) -->
  <div 
    v-if="showSidebar"
    class="fixed top-0 left-0 flex flex-col p-5 bg-black lg:bg-white sm:w-[50%] md:w-[40%] lg:w-[20%] z-[9999] h-screen gap-5 items-center font-semibold text-black border-r border-gray-300 dark:bg-white dark:text-black transition-all duration-300 transform md:translate-x-0"
    :class="{
      '-translate-x-full': !isSidebarOpen && windowWidth < 768,
      'translate-x-0': isSidebarOpen || windowWidth >= 768
    }"
  >

    <!-- Toggle Button for Closing Sidebar -->

    <button 
        @click="toggleSidebar" 
        class="lg:hidden p-3 fixed top-3 left-3 z-50 bg-black text-white rounded-md dark:bg-white dark:text-black">
        <font-awesome-icon :icon="['fas', 'bars']" class="text-2xl" />
    </button>


    <!-- Logo and Title -->
    <a href="#" class="flex items-center mb-6 mt-10 text-3xl font-semibold ">
      <img class="w-12 h-12 mr-2 rounded-full" :src="secureDocsLogo" alt="logo">
      <div class="flex flex-col">
        <h1 class="dark:text-black text-white lg:text-black">SecureDocs</h1>
        <h1 class="text-sm text-gray-500 opacity-75 dark:text-gray-400">Trusted Cloud</h1>
      </div>  
    </a>


    <!-- Navigation Links -->
    <div class="flex flex-col gap-12 w-full mt-3 font-semibold text-[17px]">
      <Link 
        v-for="link in links"
        :key="link.route"
        :href="$page.component == link.component ? null : route(link.route)"
        :class="[ 
          'p-2 rounded-md w-full text-center text-white lg:text-black dark:text-black dark:hover:bg-gray-300 dark:hover:text-black', 
          $page.component == link.component ? 'bg-blue-600 text-white' : 'hover:bg-gray-200 hover:text-black' 
        ]"
        @click="closeModalWhenSelect"
      >
        <font-awesome-icon :icon="link.icon" class="text-xl" />
        {{ link.name }}
      </Link>


      <!-- Settings Button with Modal Toggle -->
      <button 
        @click="toggleModal" 
        class="relative p-2 rounded-md w-full text-center text-white lg:text-black hover:bg-gray-200 hover:text-black dark:text-black dark:hover:bg-white dark:hover:text-black"
      >
        <font-awesome-icon :icon="['fas', 'gear']" class="text-xl" /> 
        Settings 
        <font-awesome-icon :icon="['fas', 'chevron-right']" class="absolute top-3 right-2" />
      </button>
    </div>


    <!-- User Profile and Sign Out -->
    <div class="w-[80%] absolute bottom-6 font-semibold flex flex-col justify-center items-center gap-4">
      <div class="w-full flex items-center justify-center">
        <img 
          v-if="picture.length > 0"
          v-for="pic in picture"
          class="w-10 h-10 mr-2 rounded-full" 
          :src="pic.url" 
          alt="logo"
        />

        <img 
          v-if="picture.length == 0"
          :src="defaultProfile" 
          class="w-10 h-10 mr-2 rounded-full" 
          alt="logo"
        />

        <div class="flex flex-col dark:text-black text-white lg:text-black">
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
            <font-awesome-icon :icon="['fas', 'paintbrush']" /> Appearance
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
