<template>
  <vue-page-transition name="fade-in-down">
    <slot>
      <router-view :key="$route.path"/>
    </slot>
  </vue-page-transition>
</template>

<script>
export default {
  name: "Child",
};
</script>
