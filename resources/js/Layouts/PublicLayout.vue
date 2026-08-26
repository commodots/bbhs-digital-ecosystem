<script setup>
import Header from '../Components/Navigation/Header.vue';
import Footer from '../Components/Navigation/Footer.vue';
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900">

        <Header />

        <main>
            <slot />
        </main>

        <Footer />

    </div>
</template>