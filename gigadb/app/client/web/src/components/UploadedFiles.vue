<template>
  <div class="uploaded-files">
    <h2 class="h4">Currently uploaded files</h2>
    <div v-for="file in uploads" :key="file.id">
      <div class="file">
        <a :href="file.location" target="_blank">{{ file.name }}</a>
        <button class="btn background-btn" @click="replaceFile(file.id)">Replace</button>
      </div>
    </div>
  </div>
</template>

<script>
/**
 * example of upload
 * {"id":1,"doi":"200070","name":"Placeholder-PDF.pdf","size":13170,"status":0,"location":"http://gigadb.gigasciencejournal.com/filedrop/200070/Placeholder-PDF.pdf","description":"","initial_md5":"7cae791b68cde6b785da42bc4c1d3bc1","extension":"PDF","created_at":"2024-02-13 04:47:50","updated_at":"2024-02-13 04:47:50","filedrop_account_id":1,"datatype":"","sample_ids":""}
 */
export default {
  props: {
    uploads: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    replaceFile: function (id) {
      this.$emit('replace-file', id)
    }
  }
}
</script>