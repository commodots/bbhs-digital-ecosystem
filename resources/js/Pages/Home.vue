<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import Hero from '../Components/Home/Hero.vue';
import QuickAccess from '../Components/Home/QuickAccess.vue';
import ImpactStats from '../Components/Home/ImpactStats.vue';
import LatestContent from '../Components/Home/LatestContent.vue';
import MembershipAlumni from '../Components/Home/MembershipAlumni.vue';
import StayConnected from '../Components/Home/StayConnected.vue';
</script>

<template>
    <PublicLayout>

        <Hero />

        <QuickAccess />

        <ImpactStats />

        <LatestContent />

        <MembershipAlumni />

        <StayConnected />

    </PublicLayout>
</template>