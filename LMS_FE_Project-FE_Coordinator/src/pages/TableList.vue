<template>
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <card class="strpied-tabled-with-hover"
                body-classes="table-full-width table-responsive"
          >
            <template slot="header">
              <h4 class="card-title">Faculties</h4>
              <p class="card-category">list of faculty</p>
            </template>
            <l-table class="table-hover table-striped"
                     :columns="table1.columns"
                     :data="table1.data">
            </l-table>
          </card>

        </div>

      </div>
    </div>
  </div>
</template>
<script>
  import LTable from 'src/components/Table.vue'
  import Card from 'src/components/Cards/Card.vue'
  const tableColumns = ['Id', 'faculty_name', 'program_name']
  const tableData = [{

    id: 1,
    faculty_name: 'Informatika',
    program_name: 'S1'

  },

  {
    id: 2,
    faculty_name: 'Teknik Industri',
    program_name: 'S1'
  },

  {
    id: 3,
    faculty_name: 'Akutnsi',
    program_name: 'S1'
  },

  {
    id: 4,
    faculty_name: 'Sistem Informasi',
    program_name: 'S1'
  },

  {
    id: 5,
    faculty_name: 'Manajemen',
    program_name: 'S1'
  }]
  export default {
    components: {
      LTable,
      Card
    },
    data () {
      return {
        table1: {
          columns: [...tableColumns],
          data: [...tableData]
        },
        table2: {
          columns: [...tableColumns],
          data: [...tableData]
        }
      }
    }
  }
</script>
<style>
</style>
