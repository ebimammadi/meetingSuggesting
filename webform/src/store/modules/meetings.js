import axios from 'axios'

// axios.defaults.baseURL = 'http://janjoo/api/'
axios.defaults.baseURL = 'https://tadbir.net/tests/meetings/api/'

const state = {
  office_hours: null,
  employees: [],
  selected_employees: [],
  current_meetings: [],
  suggestion_meetings: []
}

const getters = {
  allEmployees: state => state.employees,
  selectedEmployees: state => state.selected_employees,
  officeHours: state => state.office_hours,
  currentMeetings: state => state.current_meetings,
  suggestMeetings: state => state.suggestion_meetings
}

const actions = {
  updatedEmployeesSelected({ commit }, selected) {
    commit('updatedEmployees', selected)
  },
  async fetchEmployees({ commit }) {
    const response = await axios.get(
      '?request=get-employees'
    )
    commit('setEmployees', response.data)
  },
  async fetchOfficeHours({ commit }) {
    const response = await axios.get(
      '?request=get-office-hours'
    )
    commit('setOfficeHours', response.data)
  },
  async fetchSuggestMeetings({ commit }, params) {

    const start = `&start=${params.start} 00:00:00`;
    const end = `&end=${params.end} 23:59:00`;

    let ids = ``;
    for (let i= 0 ; i<params.employee_ids.length; i++){
      ids += `&ids[]=${params.employee_ids[i].id}`
    }

    const meeting_length = `&meeting_length=${params.meeting_length}`

    const response = await axios.get(
      `?request=get-suggestion-meetings&timezone=UTC`
      + ids + start + end + meeting_length
    )

    commit('setSuggestionMeetings', response.data)

  },
  async fetchCurrentMeetings({ commit }, params) {

    const start = `&start=${params.start} 00:00:00`;
    const end = `&end=${params.end} 23:59:00`;
    let ids = ``;
    for (let i= 0 ; i<params.employee_ids.length; i++){
      ids += `&ids[]=${params.employee_ids[i].id}`
    }

    const response = await axios.get(
    `?request=get-current-meetings&timezone=UTC`
      + ids + start + end
    )

    commit('setCurrentMeetings', response.data)
  }
}

const mutations = {
  setEmployees: (state, employees) => (state.employees = employees),
  setOfficeHours: (state, office_hours) => (state.office_hours = office_hours),
  setCurrentMeetings: (state, current_meetings) => (state.current_meetings = current_meetings),
  setSuggestionMeetings: (state, suggestion_meetings) => (state.suggestion_meetings = suggestion_meetings),
  updatedEmployees: (state, selected) => (state.selected_employees = selected)
}

export default {
  state,
  getters,
  actions,
  mutations
}