<template>
  <component :is="View" />
</template>

<script setup>
// Reuse existing admin add/edit business form/pages
import View from '../../../pages/admin/components/AdminAddBusiness.vue'
</script>




