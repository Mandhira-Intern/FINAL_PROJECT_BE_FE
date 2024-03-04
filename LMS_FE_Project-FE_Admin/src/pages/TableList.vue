<template>
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-12">
          <card class="strpied-tabled-with-hover"
                body-classes="table-full-width table-responsive"
          >
            <template slot="header">
              <h4 class="card-title">Staff</h4>
              <p class="card-category">List of Staff</p>
            </template>
            <l-table class="table-hover table-striped"
                     :columns="table2.columns"
                     :data="table2.data">
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
  var tableColumns = ['id', 'name', 'address', 'email', 'faculty_name']
  var tableData = [{

    id: 1,
    name: 'Cahyo',
    address: 'Yogyakarta',
    email: 'cahyo@gamil.com',
    faculty_name: 'Teknologi Industri'

  },

  {
    id: 2,
    name: 'Reno',
    address: 'Solo',
    email: 'reno@gamil.com',
    faculty_name: 'Teknologi Industri'
  },

  {
    id: 3,
    name: 'Lino',
    address: 'Jakrta',
    email: 'lino@gamil.com',
    faculty_name: 'Bisnis dan Ekonomi'
  },

  {
    id: 4,
    name: 'Sulis',
    address: 'Jakrta',
    email: 'sulis@gamil.com',
    faculty_name: 'Bisnis dan Ekonomi'
  },

  {
    id: 5,
    name: 'Akbar',
    address: 'Yogyakarta',
    email: 'akbar@gamil.com',
    faculty_name: 'Teknobio'
  },
  {
    id: 6,
    name: 'Seno',
    address: 'Solo',
    email: 'seno@gamil.com',
    faculty_name: 'Teknobio'
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
