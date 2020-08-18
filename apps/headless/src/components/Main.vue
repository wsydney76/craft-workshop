<template>
  <div>
    <div v-if="loading" class="loading container">
      Loading...
    </div>

    <div v-if="error" class="error container">
      {{ error }}
    </div>

    <div v-if="data">

      {{ data.siteInfo.defaultFeaturedImage.url }}

      <div class="entry">

        <img v-if="data.siteInfo.defaultFeaturedImage" class="featuredImage"
             :src="data.siteInfo.defaultFeaturedImage[0].url" :alt="data.entry.title"/>

        <div class="container">
          <h2>{{ data.entry.title }}</h2>

          <p><b>{{ data.entry.teaser }}</b></p>

          <blocks :blocks="data.entry.bodyContent"/>

          

        </div>
      </div>
    </div>
  </div>
</template>

<script>

    import {query} from '../queries/HomeQuery';
    import {graphQlFetchData} from '../lib/fetchData';
    import Blocks from './Blocks';


    export default {
        name: "Site.vue",

        data: function() {
            return {
                loading: false,
                data: null,
                error: null
            }
        },

        components: {Blocks},

        created: function() {
            console.log('Created');
            this.fetchData();
        },

        watch: {
            '$route': 'fetchData'
        },

        methods: {
            fetchData: function() {
                graphQlFetchData(this, query, {site:this.$route.params.site})
            },
            handleNewLine: function(str) {
                return str.replace(/(?:\r\n|\r|\n)/g, '<br />');
            }
        }
    }
</script>
<style scoped>

</style>
