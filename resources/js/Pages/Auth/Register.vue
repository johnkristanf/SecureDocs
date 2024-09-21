<script setup>
    import { useForm } from '@inertiajs/vue3';
    import authBG from '../../../../public/img/securedocs_logo.png';
    import TextInput from '../../Components/TextInput.vue';
import { computed } from 'vue';

    const formData = useForm({
        fullname: null,
        email: null,
        password: null,
        password_confirmation: null,
    })

    const onSubmit = () => {
        formData.post('/register', {
            preserveScroll: true,
            onSuccess: () => formData.reset(),
            onError: () => formData.reset('password', 'password_confirmation')
        })
    }

    const hasFormErrors = computed(() => {
        return Object.keys(formData.errors).length > 0;
    });
</script>

<template>
    <Head title="SecureDocs Register" />

    <div class="flex justify-around items-center w-full h-full">

        <div class="flex items-center">
            <img 
                :src="authBG" 
                alt="Authentication Background" 
                width="150"
                height="150"
            />

            <div class="flex flex-col font-semibold">
                <h1 class="text-6xl">SecureDocs</h1>
                <p class="text-gray-600">Your Trusted Partner for Safe, Seamless, and Secure Document Management.</p>
            </div>

        </div>
        

        <div :class="`w-[40%] bg-white ${hasFormErrors ? 'h-full' : 'h-[85%]'} p-8 rounded-md flex flex-col font-semibold`">
            <h1 class="text-3xl mb-1">Welcome to SecureDocs👋</h1>
            <p class="text-gray-500 text-sm">Please Sign Up and get started</p>

            <form class="flex flex-col gap-5 mt-6" @submit.prevent="onSubmit">

                <TextInput 
                    placeholder="Full Name" 
                    type="text"
                    v-model="formData.fullname" 
                    :errorMessage="formData.errors.fullname"
                />

                <TextInput 
                    placeholder="Email" 
                    type="text"
                    v-model="formData.email" 
                    :errorMessage="formData.errors.email"
                />

                <TextInput 
                    placeholder="Password" 
                    type="password"
                    v-model="formData.password" 
                    :errorMessage="formData.errors.password"
                />

                <TextInput 
                    placeholder="Repeat Password" 
                    type="password"
                    v-model="formData.password_confirmation" 
                    :errorMessage="formData.errors.password_confirmation"
                />


                <button 
                    type="submit"
                    :class="[
                        'p-3 rounded-md w-full text-white hover:opacity-75',
                        formData.processing ? 'bg-gray-400' : 'bg-blue-600'
                    ]"
                    :disabled="formData.processing"
                >
                    SIGN UP
                </button>

            </form>

            <div class="w-full flex justify-center gap-1 mt-4 text-sm">
                <h1 class="text-gray-600">Already have an account?</h1>
                <Link 
                    :href="route('login.page')"
                    class="text-blue-600 hover:opacity-75 hover:cursor-pointer"
                >
                    Sign in your account
                </Link>
            </div>

            

        </div>   
    </div>
    
</template>