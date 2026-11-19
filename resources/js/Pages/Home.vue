<script setup>
import PublicLayout from '../Layouts/PublicLayout.vue';
import Hero from '../Components/Home/Hero.vue';
import QuickAccess from '../Components/Home/QuickAccess.vue';
import ImpactStats from '../Components/Home/ImpactStats.vue';
import LatestContent from '../Components/Home/LatestContent.vue';
import MembershipAlumni from '../Components/Home/MembershipAlumni.vue';
import StayConnected from '../Components/Home/StayConnected.vue';

const props = defineProps({
    home: {
        type: Object,
        default: () => ({ news: [], events: [], campaigns: [], stats: {} }),
    },
});
</script>

<template>
    <PublicLayout>
        <Hero />
        <QuickAccess />
        <ImpactStats :stats="home.stats" />
        <LatestContent :news="home.news" :events="home.events" :campaigns="home.campaigns" />
        <MembershipAlumni :spotlights="home.spotlights || []" />
        <StayConnected />
    </PublicLayout>
</template>
