<template>
  <ul>
    <li>This apps tries to find suggestion spans for the meeting between the selected dates for the selected participants.</li>
    <li>This simple app is written in vueJS, and it receives data from the api, its backend with PHP</li>
    <li>It applies vue, vuex, bootstrap-vue, axios.</li>
    <li>The codes can be found in this repository https://github.com/ebimammadi/meetingSuggesting.</li>

    <li>Current  data is available between 2015-01-01 and 2015-03-31 for test.</li>
    <li>Max possible time span is 10 days.</li>
    <li>It receives the current participant names & ids from the server.</li>
    <li>Current timezone is considered as UTC both on the frontend and backend.</li>
    <li>You can select upto 5 participants.</li>

  </ul>
</template>

<script>
  export default {
    name: "Notes"
  }
</script>

<style scoped>
ul {
  border-top: 1px solid gray;
  margin-top: 40px;
  padding: 20px;
}
</style>