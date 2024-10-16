<script setup>
    import SideBar from '../Components/Sidebar.vue'
</script>

<template>

    <Head>
        <link rel="icon" href="/img/securedocs_logo.png" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </Head>

    <div>
        <SideBar />

        <main class="p-7 w-full h-[180vh] dark:bg-black">
            <slot />
        </main>
    </div>

</template>