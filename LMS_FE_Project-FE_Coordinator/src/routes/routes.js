import DashboardLayout from '../layout/DashboardLayout.vue'
// GeneralViews
import NotFound from '../pages/NotFoundPage.vue'

// Admin pages
import Overview from 'src/pages/Overview.vue'
import Faculties from 'src/pages/Faculties.vue'
import Studyprogram from 'src/pages/Studyprogram.vue'
import Subjects from 'src/pages/Subjects.vue'
import Lecturer from 'src/pages/Lecturer.vue'
import Students from 'src/pages/Students.vue'
import Academicyears from 'src/pages/Academicyears.vue'
import UserProfile from 'src/pages/UserProfile.vue'
import TableList from 'src/pages/TableList.vue'
import Typography from 'src/pages/Typography.vue'
import Icons from 'src/pages/Icons.vue'
import Maps from 'src/pages/Maps.vue'
import Notifications from 'src/pages/Notifications.vue'
import Upgrade from 'src/pages/Upgrade.vue'

const routes = [
  {
    path: '/',
    component: DashboardLayout,
    redirect: '/admin/overview'
  },
  {
    path: '/admin',
    component: DashboardLayout,
    redirect: '/admin/overview',
    children: [
      {
        path: 'overview',
        name: 'Overview',
        component: Overview
      },
      {
        path: 'faculties',
        name: 'Faculties',
        component: Faculties
      },
      {
        path: 'studyprogram',
        name: 'Studyprogram',
        component: Studyprogram
      },
      {
        path: 'subjects',
        name: 'Subjects',
        component: Subjects
      },
      {
        path: 'lecturer',
        name: 'Lecturer',
        component: Lecturer
      },
      {
        path: 'students',
        name: 'Students',
        component: Students
      },
      {
        path: 'academicyears',
        name: 'Academicyears',
        component: Academicyears
      },
      {
        path: 'user',
        name: 'User',
        component: UserProfile
      },
      {
        path: 'notifications',
        name: 'Notifications',
        component: Notifications
      }
    ]
  },
  { path: '*', component: NotFound }
]

/**
 * Asynchronously load view (Webpack Lazy loading compatible)
 * The specified component must be inside the Views folder
 * @param  {string} name  the filename (basename) of the view to load.
function view(name) {
   var res= require('../components/Dashboard/Views/' + name + '.vue');
   return res;
};**/

export default routes
