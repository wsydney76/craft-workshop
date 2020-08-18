<template>
  <div>
    <strong>{{ $t('Original Title') }}: </strong>{{ entry.originalTitle }}<br/>
    <strong>{{ $t('Release Year') }}: </strong>{{ entry.releaseYear }}<br/>

    <h2>{{ $t('Cast') }}</h2>
    <div v-for="role in entry.cast">
      <strong>{{ role.roleName }}</strong><br/>

      <entry-button
              v-for="person in role.persons"
              :key="person.id"
              :caption="person.title"
              :site="person.site"
              section="person"
              :id="person.id"
              :slug="person.slug" />

    </div>
  </div>
</template>

<script>
    import EntryButton from './EntryButton.vue'

    export default {
        props: ['entry'],
        components: {EntryButton}
    }
</script>
