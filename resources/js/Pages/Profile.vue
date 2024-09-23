<script setup>
  import secureDocsLogo from '../../../public/img/securedocs_logo.png'
</script>

<template>
  <div class="flex flex-col w-full items-center dark:text-white">
    <div class="relative w-1/2 ">
      <h1 class="font-semibold text-2xl relative z-10">My Profile</h1>
      
      <!-- Underline using pseudo-element -->
      <div class="absolute inset-0 flex items-center justify-center mt-10">
        <div class="w-full h-[2px] bg-gray-400 -z-10 dark:bg-gray-600"></div>
      </div>
    </div>

    <h1 class="mt-8">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Illo non, blanditiis aspernatur facilis.</h1>

    <div class="w-1/2 flex justify-around gap-16 gap-5 mt-8 font-semibold">
        <form class="w-full flex flex-col gap-5 ">
          <input type="text" class="w-full focus:outline-none border border-gray-200 rounded-md p-2 dark:text-black" placeholder="Full Name">

          <input type="text" class="w-full focus:outline-none border border-gray-200 rounded-md p-2 dark:text-black" placeholder="Full Name">
          <input type="password" class="w-full focus:outline-none border border-gray-200 rounded-md p-2 dark:text-black" placeholder="Full Name">
          <input type="password" class="w-full focus:outline-none border border-gray-200 rounded-md p-2 dark:text-black" placeholder="Full Name">

          <button 
            type="submit" 
            class="bg-blue-600 rounded-md p-3 text-white hover:opacity-75"
          >
            Update Profile
          </button>
            
        </form>

        <div class="relative">
          <img 
            :src="secureDocsLogo" 
            alt="User Profile Picture"
            class="w-full  rounded-full bg-white p-5"
          />

          <font-awesome-icon 
            :icon="['fas', 'pen']" 
            class="absolute bg-black rounded-full p-3 text-white bottom-[70px] left-[-8px] text-lg dark:text-blue-600"
          />
        </div>

        
    </div>

  </div>
</template>
