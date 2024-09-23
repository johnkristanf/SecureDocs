<script setup>
    import SideBar from '../Components/Sidebar.vue'
</script>

<template>

    <Head>
        <link rel="icon" href="/img/securedocs_logo.png" type="image/x-icon">
    </Head>

    <div>
        <SideBar />

        <main class="p-7 w-full h-[150vh] dark:bg-black">
            <slot />
        </main>
    </div>

</template>