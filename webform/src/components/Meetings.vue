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
            placeholder="Search for name"
            label="name" track-by="id"
            :value = "selectedEmployees"
            :options="allEmployees"
            @input="updatedEmployeesSelected"
            :multiple="true"
            :max="5"
        ></multiselect>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
        Meeting length (Minutes)
        <b-form-spinbutton v-model="meeting_length"  min="10" max="300" step="5"></b-form-spinbutton>
      </div>
      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
        <br>
        <b-button variant="outline-primary" @click="getSuggestions" >SuggestMeetings</b-button>
      </div>
      <div class="col-6 col-sm-8 col-md-9 col-xl-10 ">
        <br/>
        <div v-html="message"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div v-html="days">The suggestion is shown here</div>
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
        meeting_length: 60,
        span_days: [],
        days: ``,
        message: '',
        messageShow : false
      }
    },
    methods: {
      ...mapActions([//spread to add other functions
        "fetchEmployees",
        "fetchOfficeHours",
        "fetchSuggestMeetings",
        "fetchCurrentMeetings",
        "updatedEmployeesSelected"
      ]),
      prepareAlerts(message,type = null){
        if (type == null) type = 'alert-dark'
        return `<div class="alert ${type} alert-dismissible fade show" role="alert" >
                  ${message}
                </div>`
        // return `<b-alert variant="${type}" dismissible>
        //         ${message}
        //         </b-alert>`;
// <div class="alert ${type} alert-dismissible fade show" role="alert">
//                   ${message}
//                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
//                     <span aria-hidden="true">&times;</span>
//                   </button>
//                 </div>`
      },
      getCurrentMeetings(){
        const params = {
          start: this.start,
          end: this.end,
          meeting_length: this.meeting_length,
          employee_ids: this.selectedEmployees
        };
        this.fetchCurrentMeetings(params)
      },
      getSuggestions(){
        this.message = ""
        if (this.selectedEmployees.length == 0){
          this.message = this.prepareAlerts("Please select at least one Employee!")
          return false
        }

        const start = new Date(this.start);
        const end = new Date(this.end);
        if (start > end) {
          this.message = this.prepareAlerts("End should be after than start!")
          return false
        }

        const days_count = (end - start)/86400000 + 1;
        if (days_count >= 11){
          this.message = this.prepareAlerts("The looking span should not be more than 10 days")
          return false
        }

        const params = {
          start: this.start,
          end: this.end,
          employee_ids: this.selectedEmployees,
          meeting_length: this.meeting_length
          //func : showCurrentMeetings
        };
        this.fetchSuggestMeetings(params)
      },

      visualizeSuggestMeetings(){
        const weekDayName = (ith) => {
          const array = ["SUN","MON","TUE","WED","THU","FRI","SAT"]
          return array[ith]
        }

        //the hours are planned to be shown full-width and responsive and I have used width's viewport units
        const viewportWidth = 95
        const offsetWidth = 10

        //the hoursCaptions generations the hours caption
        const hoursCaptions = () => {
          let divsHours = ``
          const difference_hours = Number(this.officeHours.end) - Number(this.officeHours.start)
          const hour_width = (viewportWidth-offsetWidth) / difference_hours
          for (let i = Number(this.officeHours.start) ; i < Number(this.officeHours.end) ; i++){
            let leftAttr = (hour_width)*(i-this.officeHours.start)+offsetWidth
            divsHours += `<div class="timeSpan left-border" style="left:${leftAttr}vw; width:${hour_width}vw">${i}</div>`
          }
          return `<div class="row-header-hours">Days ${divsHours} </div>`
        };
        //th extractAndSetDayMeetings function generates the layout for each day
        const extractAndSetDayMeetings = (oneDate) => {
          const sameDate = (date1,date2) => {
            if (
              date1.getFullYear() == date2.getFullYear() &&
              date1.getMonth() == date2.getMonth() &&
              date1.getDate() == date2.getDate()
            )
              return true;
            return false;
          };
          const difference_hours = Number(this.officeHours.end) - Number(this.officeHours.start)
          const oneHourWidth = (viewportWidth-offsetWidth) / difference_hours
          const officeHourStart = Number(this.officeHours.start)

          let divsHours = ``
          this.suggestMeetings.forEach(meeting => {
            const meetingStart = new Date (meeting.start.date)
            if (sameDate(meetingStart,oneDate)) {
              const meetingEnd = new Date(meeting.end.date)
              const length = (meetingEnd - meetingStart) / 60000 * oneHourWidth / 30/2
              const meetingStartHour = meetingStart.getHours() + meetingStart.getMinutes() / 60
              const leftAttr = (oneHourWidth) * (meetingStartHour - officeHourStart) + offsetWidth
              divsHours += `<div class="timeSpan classGreen" style="left:${leftAttr}vw; width:${length}vw"></div>`
            }
          });
          return `<div class="row-hours"> <span class="date-caption">${weekDayName(oneDate.getDay())} ${oneDate.getMonth()+1}/${oneDate.getDate()} </span> ${divsHours} </div>`
        }

        const startDate = new Date(this.start);
        const endDate = new Date(this.end);
        const days_count = (endDate - startDate)/86400000 + 1;

        let daysResults = ``
        for (let i = 1, ithDate = startDate ; i <= days_count ; i++ ){
          daysResults += extractAndSetDayMeetings(ithDate);
          ithDate.setDate(ithDate.getDate() + 1);//go to next day
        }
        this.days =  hoursCaptions() + daysResults
      }
      //construct each day
    },
    computed: {
      ...mapGetters([ "allEmployees" , "officeHours","currentMeetings","suggestMeetings","selectedEmployees"]),
    },
    watch: {
      //when there are updated suggestMeetings, it extracts meetings and shows the new layout
      suggestMeetings: function(){
        this.visualizeSuggestMeetings()
      }
    },
    created(){
      //based on component creations it fetches the employees and office hours from the backend
      this.fetchEmployees()
      this.fetchOfficeHours()
    },

  }
</script>

<!--damn scoped! -->
<style >

.row {
  margin:20px 0;
}

.row-header-hours {
  flex-flow: row;
  position: relative;
  width: 95vw;
  height:20px;
  overflow: hidden;
  overflow-x: auto;
  white-space: nowrap;
}
.row-hours {
  flex-flow: row;
  position: relative;
  width: 95vw;
  height:50px;
  overflow: hidden;
  overflow-x: auto;
  white-space: nowrap;
  border-top: 1px solid gray;
}
.timeSpan{
  position: absolute;
  opacity: 0.6;
  height: 48px;
  display:inline-block;
  box-sizing: border-box;
}
.left-border {
  border-left: 1px solid gray;
}
.classGreen {
  background-color: lightgreen;
  border: 1px solid darkgreen;
}
.date-caption {
  position: relative;
  font-weight: bold;
  top:12px;
}

</style>