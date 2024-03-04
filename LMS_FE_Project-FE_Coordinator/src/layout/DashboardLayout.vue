<template>
  <div class="wrapper">
    <side-bar>
      <mobile-menu slot="content"></mobile-menu>

      <sidebar-link to="/admin/overview">
        <i class="nc-icon nc-chart-pie-35"></i>
        <p>Dashboard</p>
      </sidebar-link>

      <sidebar-link to="/admin/faculties">
        <i class="nc-icon nc-single-copy-04"></i>
        <p>Administrative</p>
      </sidebar-link>

      <sidebar-link to="/admin/studyprogram">
        <i class="nc-icon nc-chart-bar-32"></i>
        <p>Lecturer Performance</p>
      </sidebar-link>

      <sidebar-link to="/admin/subjects">
        <i class="nc-icon nc-chat-round"></i>
        <p>Evaluation</p>
      </sidebar-link>

      <sidebar-link to="/admin/user">
        <i class="nc-icon nc-circle-09"></i>
        <p>User</p>
      </sidebar-link>

    </side-bar>
    <div class="main-panel">
      <top-navbar></top-navbar>

      <dashboard-content @click="toggleSidebar">

      </dashboard-content>

      <content-footer></content-footer>
    </div>
  </div>
</template>
<style lang="scss">

</style>
<script>
  import TopNavbar from './TopNavbar.vue'
  import ContentFooter from './ContentFooter.vue'
  import DashboardContent from './Content.vue'
  import MobileMenu from './MobileMenu.vue'
  export default {
    components: {
      TopNavbar,
      ContentFooter,
      DashboardContent,
      MobileMenu
    },
    methods: {
      toggleSidebar () {
        if (this.$sidebar.showSidebar) {
          this.$sidebar.displaySidebar(false)
        }
      }
    }
  }

</script>
