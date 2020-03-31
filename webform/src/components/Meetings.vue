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
<!--      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">-->
<!--        <br>-->
<!--        <b-button variant="outline-success" @click="getCurrentMeetings" ></b-button>-->
<!--      </div>-->
      <div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
        <br>
        <b-button variant="outline-primary" @click="getCurrentMeetings" >SuggestMeetings</b-button>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div><h4>layout will be added here</h4></div>
        <div v-html="days"></div>
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
      },

      showCurrentMeetings(){
        //
        const viewportWidth = 95
        const offsetWidth = 10
        const hoursCaptions = () => {
          let divsHours = ``
          const difference_hours = Number(this.officeHours.end) - Number(this.officeHours.start)
          const hour_width = (viewportWidth-offsetWidth) / difference_hours
          for (let i = Number(this.officeHours.start) ; i < Number(this.officeHours.end) ; i++){
            let leftAttr = (hour_width)*(i-this.officeHours.start)+offsetWidth
            divsHours += `<div class="timeSpan" style="left:${leftAttr}vw; width:${hour_width}vw">${i}</div>`
          }
          return `<div class="row-header-hours">Hours ${divsHours} </div>`
        };
        const extractDaysMeeting = (oneDate) => {
          const sameDate = (date1,date2) => {
            if (
              date1.getFullYear() == date2.getFullYear() &&
              date1.getMonth() == date2.getMonth() &&
              date1.getDay() == date2.getDay()
            )
              return true;
            return false;
          };
          console.log(oneDate)
          const difference_hours = Number(this.officeHours.end) - Number(this.officeHours.start)
          const oneHourWidth = (viewportWidth-offsetWidth) / difference_hours
          const officeHourStart = Number(this.officeHours.start)

          let divsHours = ``
          this.suggestMeetings.forEach(meeting => {
            const meetingStart = new Date (meeting.start.date)
            if (sameDate(meetingStart,oneDate)) {
              const meetingEnd = new Date(meeting.end.date)
              const length = (meetingEnd - meetingStart) / 60000 * oneHourWidth / 30/2
              // console.log('length'+length)
              // console.log(oneHourWidth)
              const meetingStartHour = meetingStart.getHours() + meetingStart.getMinutes() / 60
              // console.log('start'+meetingStartHour)
              const leftAttr = (oneHourWidth) * (meetingStartHour - officeHourStart) + offsetWidth
              // console.log("left"+leftAttr)
              divsHours += `<div class="timeSpan classGreen" style="left:${leftAttr}vw; width:${length}vw"></div>`
            }
          });
          //console.log(divsHours)
          return `<div class="row-hours"> ${oneDate.getMonth()+1}/${oneDate.getDate()} ${divsHours}</div>`
          // foreach
        }
        //const colours = ["red","lightblue","orange","teal","gold"]
        // const employeesWithColors = this.selectedEmployees.map((employee,i) =>  {
        //   return { ...employee,"color": colours[i] }
        // })
        //
        // console.log(employeesWithColors)

        const startDate = new Date(this.start);
        const endDate = new Date(this.end);
        const days_count = (endDate - startDate)/86400000 + 1;

        let results = ``
        for (let i = 1, ithDate = startDate ; i <= days_count ; i++ ){
          results += extractDaysMeeting(ithDate)
          ithDate.setDate(ithDate.getDate() + 1);
        }


        //const resultRow1 = extractDaysMeeting(startDate);

        //console.log (extractDaysMeeting(startDate))
        this.days =  hoursCaptions() + results


      }
      //construct each day
    },
    computed: {
      ...mapGetters([ "allEmployees" , "officeHours","currentMeetings","suggestMeetings","selectedEmployees"]),
    },
    watch: {
      currentMeetings: function(){
        console.log('tes')
        this.showCurrentMeetings()
      }
    },
    created(){
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
.classGreen {
  background-color: green;
  border: 1px solid green;
}


</style>