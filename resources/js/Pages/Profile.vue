<script setup>
  import Swal from 'sweetalert2';
  import defaultProfile from '../../../public/img/default_profile.png';
  import TextInput from '../Components/TextInput.vue';

  import { useForm, usePage } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import { ThemeSelector } from '../../helpers/ThemeSelector';

  ThemeSelector();

  const { props } = usePage();
  const user = props.auth.user;
  const picture = ref(props.picture);
  
  console.log("picture: ", picture);
  
  const fileInput = ref(null);

  const inputFormData = useForm({
    user_id: Number(user.id),
    fullName: user.fullname,
    email: user.email,
    oldPassword: null,
    newPassword: null
  });


  const imageFormData =  useForm({
    file: null
  });


  const onSubmit = () => {
    inputFormData.post('/edit/profile', {
      preserveScroll: true,

      onSuccess: () => {
        Swal.fire({
          position: "top-end",
          icon: "success",
          title: "Edit Profile Successfully",
          showConfirmButton: false,
          timer: 1500
        });

        inputFormData.reset('oldPassword');
        inputFormData.reset('newPassword');
      },

      onError: (errors) => {
        console.error('Error in logging in: ', errors);
        inputFormData.reset('oldPassword');
        inputFormData.reset('newPassword');
      },
    });
  };

  
  const handleFileUploadClick = () => fileInput.value.click();

  const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
      imageFormData.file = file
    }

    uploadProfile()
  };

  const uploadProfile = () => {
    imageFormData.post('/upload/profile/picture', {
      preserveScroll: true,

      onSuccess: () => {
        Swal.fire({
          position: "top-end",
          icon: "success",
          title: "Profile Picture Uploaded Successfully",
          showConfirmButton: false,
          timer: 1500
        });

        window.location.href = "/profile";


      },

      onError: (errors) => console.error('Error in uploading profile picture: ', errors)
    })
  }

</script>

<template>
  <div class="flex flex-col w-full items-center dark:text-white">
    <div class="relative w-full md:w-1/2 ">
      <h1 class="font-semibold text-2xl relative z-10 pl-12 md:pl-0">My Profile</h1>
      
      <!-- Underline using pseudo-element -->
      <div class="absolute inset-0 flex items-center justify-center mt-10">
        <div class="w-full h-[2px] bg-gray-400 -z-10 dark:bg-gray-600"></div>
      </div>
    </div>

    <h1 class="mt-8">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Illo non, blanditiis aspernatur facilis.</h1>

    <div class="w-full md:w-1/2 flex flex-col-reverse md:flex-row justify-center md:justify-around gap-16 gap-5 mt-8 font-semibold">
        <form class="w-full flex flex-col gap-5" @submit.prevent="onSubmit">
          
          <TextInput
            v-model="inputFormData.fullName"
            type="text"
            placeholder="Full Name"
            :errorMessage="inputFormData.errors.fullName"
          />

          <TextInput
            v-model="inputFormData.email"
            type="text"
            placeholder="Email"
            :errorMessage="inputFormData.errors.email"
          />

          <TextInput
            v-model="inputFormData.oldPassword"
            type="password"
            placeholder="Old Password"
            :errorMessage="inputFormData.errors.oldPassword"
          />

          <TextInput
            v-model="inputFormData.newPassword"
            type="password"
            placeholder="New Password"
            :errorMessage="inputFormData.errors.newPassword"
          />

          <button 
            type="submit" 
            class="bg-blue-600 rounded-md p-3 text-white hover:opacity-75"
          >
            Update Profile
          </button>
            
        </form>

        <div class="relative w-full flex justify-center">
          <img 
            v-if="picture.length > 0"
            v-for="pic in picture"
            :src="pic.url" 
            alt="User Profile Picture"
            class="w-[40%] md:w-full rounded-full bg-white p-5"
          />

          <img 
            v-if="picture.length == 0"
            :src="defaultProfile" 
            alt="User Profile Picture"
            class="w-[40%] md:w-full rounded-full bg-white p-5"
          />

          <font-awesome-icon 
            :icon="['fas', 'pen']" 
            class="absolute bg-black rounded-full p-3 text-white bottom-[3px] md:bottom-[70px] left-[125px] sm:left-[220px] md:left-[-8px] text-lg dark:text-blue-600 hover:cursor-pointer hover:opacity-75 dark:bg-gray-300"
            @click="handleFileUploadClick"
          />

          <input type="file" ref="fileInput" class="hidden" @change="handleFileChange" accept="image/*" />

        </div>

        
    </div>

  </div>
</template>
