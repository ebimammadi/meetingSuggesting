<template>
  <div>
    <div class="row">
      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
        Start (Earliest) Date
        <b-form-datepicker
            v-model="start"
            :min="min"
            :max="max"
            placeholder="YYYY-MM-DD"
            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
            locale="se" >
        </b-form-datepicker>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
        End (Latest) Date
        <b-form-datepicker
            v-model="end"
            :min="min"
            :max="max"
            placeholder="YYYY-MM-DD"
            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
            locale="se" >
        </b-form-datepicker>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
        Employees (Participants)
        <multiselect
            placeholder="Search"
            label="name" track-by="id"
            :value = "selectedEmployees"
            :options="allEmployees"
            @input="updatedEmployeesSelected"
            :multiple="true"
            :max="5"
        ></multiselect>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
        Meeting length
        <b-form-spinbutton v-model="meeting_length"  min="10" max="300"></b-form-spinbutton>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
        <br>
        <b-button variant="outline-success" @click="getSuggestions" >Suggestion Meetings</b-button>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
        <br>
        <b-button variant="outline-primary" @click="getCurrentMeetings" >Current Meetings</b-button>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div><h4>layout will be added here</h4></div>
        <div>current array: {{currentMeetings}}</div>
        <div>suggestion array: {{suggestMeetings}}</div>
      </div>




    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex'

  export default {
    name: "Meetings",
    data() {
      const minDate = new Date(2015,0,1)
      const maxDate = new Date(2015,2,31)
      return {
        start: '2015-01-01',
        end: '2015-01-05',
        min: minDate,
        max: maxDate,
        selected: '',
        meeting_length: 35
      }
    },
    methods: {
      ...mapActions([//spread to add other functions
        "fetchEmployees",
        "fetchOfficeHours",
        "fetchCurrentMeetings",
        "fetchSuggestMeetings",
        "updatedEmployeesSelected"
      ]),
      getSuggestions(){
        const params = {
          start: this.start,
          end: this.end,
          meeting_length: this.meeting_length,
          employee_ids: this.selectedEmployees
        };
        this.fetchSuggestMeetings(params)
      },
      getCurrentMeetings(){
        const params = {
          start: this.start,
          end: this.end,
          employee_ids: this.selectedEmployees
        };
        this.fetchCurrentMeetings(params)
      }
    },
    computed: {
      ...mapGetters([ "allEmployees" , "officeHours","currentMeetings","suggestMeetings","selectedEmployees"]),
    },
    watch: {

    },
    created(){
      this.fetchEmployees()
      this.fetchOfficeHours()
    },

  }
</script>

<style scoped>
.row {
  margin:20px 0;
}
</style>