
<script setup>
    const model = defineModel({
        type: null,
        required: true
    })

    defineProps({
        placeholder: {
            type: String,
            required: true
        },
        
        type: {
            type: String,
            required: true,
            default: "text"
        },
        
        errorMessage: String
    })
</script>

<template>
    <div class="flex flex-col">
        <input 
            :type="type" 
            :class="[
                'focus:outline-none rounded-md p-3 border border-gray-300 dark:text-black',
                {'border !border-red-500': errorMessage}
            ]" 
            :placeholder="placeholder"
            v-model="model"
        />

        <small v-if="errorMessage" class="text-red-800 mt-1">{{ errorMessage }}</small>

    </div>
</template>