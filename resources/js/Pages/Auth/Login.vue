<script setup>
    import { useForm } from '@inertiajs/vue3';
    import authBG from '../../../../public/img/securedocs_logo.png';
    import TextInput from '../../Components/TextInput.vue';

    const formData = useForm({
        email: null,
        password: null,
    })

    const onSubmit = () => {

        formData.post('/login', {
            preserveScroll: true,

            onSuccess: () => {
                formData.reset()
            },

            onError: (errors) =>{
                console.error('Error in logging in: ', errors)
                formData.reset('password')
            }, 
        })
    }

    const loadTestAccount = () => {
        formData.email = 'test@gmail.com';
        formData.password = 'test123';
    }

</script>

<template>
    <Head title="SecureDocs Login" />

    <div class="flex justify-around items-center w-full h-full flex-col md:flex-row gap-5">

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
        

        <div class="w-full md:w-[40%] bg-white h-[73%] p-8 rounded-md flex flex-col font-semibold">
            <h1 class="text-3xl mb-1">Welcome to SecureDocs👋</h1>
            <p class="text-gray-500 text-sm">Please Sign In you Account and start securing documents.</p>

            <form class="flex flex-col gap-5 mt-6 " @submit.prevent="onSubmit">
                
                <TextInput
                    v-model="formData.email" 
                    placeholder="Email"
                    type="text"
                    :errorMessage="formData.errors.email"
                />

                <TextInput
                    v-model="formData.password" 
                    placeholder="Password"
                    type="password"
                    :errorMessage="formData.errors.password"
                />

                <button 
                    type="submit"
                    :class="[
                        'p-3 rounded-md w-full text-white hover:opacity-75',
                        formData.processing ? 'bg-gray-400' : 'bg-blue-600'
                    ]"
                    
                    :disabled="formData.processing"
                >
                    SIGN IN
                </button>

                <button 
                    type="button"
                    class="bg-black p-3 rounded-md w-full text-white hover:opacity-75"
                    @click="loadTestAccount"
                >
                    TEST ACCOUNT
                </button>


            </form>

            <div class="w-full flex justify-center gap-1 mt-4 text-sm">
                <h1 class="text-gray-600">New to our platform?</h1>
                <Link 
                    :href="route('register.page')"
                    class="text-blue-600 hover:opacity-75 hover:cursor-pointer"
                >
                    Create a New Account
                </Link>
            </div>

            

        </div>   
    </div>
    
</template>