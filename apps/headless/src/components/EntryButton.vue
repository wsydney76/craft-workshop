<template>
  <div class="clickable" @click="fireShowEntryEvent(section, id, slug)">
    <span v-if="caption">{{ caption }}</span>
    <span v-else>{{ title }}</span>
  </div>
</template>

<script>
    export default {
        props: ['caption', 'slug', 'section', 'id', 'title'],
        methods: {
            fireShowEntryEvent(section, id, slug) {
                this.$router.push({name: 'entry', params: {site: this.$route.params.site, section: section, id: id, slug: slug}});
            }
        }
    }
</script>

<style>
  .clickable {
    border: 1px solid #ccc;
    background-color: #1DA1F2;
    color: #fff;
    padding: 4px;
    margin-top: 6px;
    margin-bottom: 18px;
    display: inline-block;
    cursor: pointer;
  }
</style>
