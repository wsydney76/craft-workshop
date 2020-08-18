<template>
  <div class="entry">

    <div v-if="loading" class="loading container">
      Loading...
    </div>

    <div v-if="error" class="error container">
      {{ error }}
    </div>

    <div v-if="data">
      <img v-if="data.entry.featuredImage[0]" class="featuredImage" v-bind:src="data.entry.featuredImage[0].url" v-bind:alt="data.entry.title"/>

      <main class="main container">
        <h1>{{ data.entry.title }}</h1>

        <p><strong>{{ data.entry.teaser }}</strong></p>

        <div v-if="data.entry.sectionHandle == 'film'">
          <film :entry=data.entry />
        </div>

        <div v-if="data.entry.sectionHandle == 'person'">
          <person :entry=data.entry />
        </div>

        <blocks :blocks="data.entry.bodyContent"/>

      </main>
    </div>
  </div>
</template>

<script>
    import Film from './Film.vue';
    import Person from './Person.vue';
    import Blocks from './Blocks.vue';
    import {graphQlFetchData} from '../lib/fetchData';
    import {query} from '../queries/EntryQuery';

    export default {
        data: function() {
            return {
                loading: false,
                data: null,
                error: null
            }
        },

        components: {Film, Person, Blocks},

        created: function() {
            this.$i18n.locale = this.$route.params.site;
            this.fetchEntryData();
        },

        watch: {
            '$route': 'fetchEntryData'
        },

        methods: {

            fetchEntryData() {
                graphQlFetchData(this, query, {site:this.$route.params.site, id:this.$route.params.id});
            }
        }
    }
</script>

<style>

</style>
