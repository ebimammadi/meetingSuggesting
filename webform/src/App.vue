<template>
  <div id="app">
    <h3>A Multi-participant Office Meeting Test</h3>
    <Meetings/>
    <Notes/>
  </div>
</template>

<script>
import Meetings from './components/Meetings.vue'
import Notes from './components/Notes.vue'

export default {
  name: 'App',
  components: {
    Meetings,
    Notes
  }
}
</script>

<style>
#app {
  font-family:Arial, sans-serif, Avenir, Helvetica;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  margin: 20px;
}
</style>
