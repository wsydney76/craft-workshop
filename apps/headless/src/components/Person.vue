<template>
  <div>
    <div v-if="entry.born">
      <strong>{{ $t('Born') }}: </strong>{{ entry.born }}
    </div>
    <div v-if="entry.died">
      <strong>{{ $t('Died') }}: </strong>{{ entry.died }}<br/>
    </div>
    <h2>{{ $t('Films') }}</h2>


    <div v-for="role in entry.roles">
      <strong>{{ role.roleName }}</strong><br/>
      <entry-button
              :key="role.film.id"
              :caption="role.film.title"
              section="film"
              :id="role.film.id"
              :slug="role.film.slug"/>
    </div>
    <p v-html="handleNewLine(entry.bio)"></p>
  </div>
</template>

<script>
    import EntryButton from './EntryButton.vue'

    export default {
        props: ['entry'],
        components: {EntryButton},
        methods: {
            handleNewLine(str) {
                return str.replace(/(?:\r\n|\r|\n)/g, '<br />');
            }
        }
    }
</script>
