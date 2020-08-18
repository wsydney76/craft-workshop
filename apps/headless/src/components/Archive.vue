<template>
  <div class="archive">

    <div v-if="loading" class="loading container">
      Loading...
    </div>

    <div v-if="error" class="error container">
      {{ error }}
    </div>

    <div v-if="data">
      <div class="entry" v-for="entry in data.entries" :key="entry.id"
           @click="fireShowEntryEvent(entry.sectionHandle, entry.id, entry.slug)">
        <img v-if="entry.featuredImage[0]" class="featuredImage" :src="entry.featuredImage[0].url" :alt="entry.title"/>
        <div class="container">
          <h2>{{ entry.title }}</h2>
          <p>{{ entry.shortDescription }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    import {graphQlFetchData} from '../lib/fetchData';
    import {query} from '../queries/ArchiveQuery';

    export default {
        name: "Archive.vue",
        data: function() {
            return {
                loading: false,
                data: null,
                error: null
            }
        },

        created: function() {
            this.fetchArchiveData();
        },

        watch: {
            '$route': 'fetchArchiveData'
        },

        methods: {
            fetchArchiveData() {
                graphQlFetchData(this, query, {site:this.$route.params.site, section:this.$route.params.slug});
            },
            fireShowEntryEvent(section, id, slug) {
                this.$router.push({name: 'entry', params: {site: this.$route.params.site, section: section, id: id, slug: slug}});

            }
        }
    }
</script>

<style scoped>
  .archive .entry {
    cursor: pointer;
  }
</style>
