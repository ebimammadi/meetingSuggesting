<template>
  <ul>
    <li>This apps tries to find suggestion meetings between the selected dates for the selected participants.</li>
    <li>This simple app is written in vueJS, and it receives data from the api, its backend with PHP.</li>
    <li>It applies vue, bootstrap-vue, vuex, axios.</li>
    <li>The codes can be found in this repository <a href="https://github.com/ebimammadi/meetingSuggesting" target="_blank">https://github.com/ebimammadi/meetingSuggesting</a>.</li>

    <li>Current  data is available between 2015-01-01 and 2015-03-31 for test.</li>
    <li>Max possible time span is 10 days.</li>
    <li>It receives the current participant names & ids from the server.</li>
    <li>Current timezone is considered as UTC both on the frontend and backend.</li>
    <li>You can select upto 5 participants.</li>
    <li>The possible suggestion meetings are in green colour, there can be some time overlaps, the white colours are already occupied.</li>


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