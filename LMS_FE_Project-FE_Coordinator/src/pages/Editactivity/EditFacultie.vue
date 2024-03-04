<template>
    <card>
      <h4 slot="header" class="card-title">Edit Profile</h4>
      <form>
        <div class="row">
          <div class="col-md-5">
            <base-input type="text"
                      label="Faculty"
                      :disabled="true"
                      placeholder="Light dashboard"
                      v-model="user.faculty_name">
            </base-input>
          </div>

          <div class="col-md-4">
            <base-input type="text"
                      label="Program Name"
                      placeholder=""
                      v-model="user.program_name">
            </base-input>
          </div>
        </div>
  
        <div class="text-center">
          <button type="submit" class="btn btn-info btn-fill float-right" @click.prevent="updateProfile">
            Add
          </button>
          
        </div>
        <div class="clearfix"></div>
      </form>
    </card>
  </template>
  <script>
    import Card from 'src/components/Cards/Card.vue'
  
    export default {
      components: {
        Card
      },
      data () {
        return {
          user: {
            faculty_name: '	Ilmu Sosial dan Politik',
            program_name: 'S1'
          }
        }
      },
      methods: {
        updateProfile () {
          alert('Your data: ' + JSON.stringify(this.user))
        }
      }
    }
  
  </script>
  <style>
  
  </style>
  