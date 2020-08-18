<template>
  <div>
    <div v-for="block in blocks">
      <div v-if="block.__typename == 'bodyContent_heading_BlockType'">
        <h2 v-if="block.tag == 'h2'">{{ block.text }}</h2>
        <h3 v-if="block.tag == 'h3'">{{ block.text }}</h3>
        <h4 v-if="block.tag == 'h4'">{{ block.text }}</h4>
      </div>
      <div v-if="block.__typename == 'bodyContent_text_BlockType'">
        <p v-html="handleNewLine(block.text)"></p>
      </div>
      <div v-if="block.__typename == 'bodyContent_pullquote_BlockType'">
        <blockquote v-html="handleNewLine(block.text)"></blockquote>
      </div>
      <div v-if="block.__typename == 'bodyContent_image_BlockType'">
        <img v-bind:src="block.image[0].url" v-bind:alt="block.title"/>
        <p>{{ block.caption }}</p>
      </div>
      <div v-if="block.__typename == 'bodyContent_button_BlockType'">
        <entry-button
                :caption="block.caption"
                :title="block.target[0].title"
                :section="block.target[0].sectionHandle"
                :id="block.target[0].id"
                :slug="block.target[0].slug" />
      </div>
    </div>
  </div>
</template>

<script>
    import EntryButton from './EntryButton.vue'

    export default {
        props: ['blocks'],
        components: {EntryButton},
        methods: {
            handleNewLine(str) {
                return str.replace(/(?:\r\n|\r|\n)/g, '<br />');
            }
        }
    }
</script>
