<template>
  <div class="content">
    <div class="container-fluid">
      
     <h1>Course Schedule</h1>
     
     <table>

       <thead>
         <tr>
          <th>ID</th>
           <th>Lecture id</th>
           <th>Course id</th>
           <th>Day</th>
           <th>Session</th>
           <th>Action</th>
         </tr>
       </thead>

       <tbody id="table-body">

         <tr v-for="faculty in faculty" :key="faculty.id">
           <td>{{ faculty.id }}</td>
           <td>{{ faculty.faculty_name }}</td>
           <td>{{ faculty.faculty_name }}</td>
           <td>
             <button class="edit-btn" @click="editFaculty(faculty.id)">Edit</button>
             <button class="delete-btn" @click="deleteFaculty(faculty.id)">Delete</button>
           </td>
         </tr>

         <tr>
           <td>1</td>
           <td>RZA332</td>
           <td>INF032</td>
           <td>Senin</td>
           <td>1</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         
         <tr>
           <td>2</td>
           <td>RZA331</td>
           <td>INF044</td>
           <td>Selasa</td>
           <td>2</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         <tr>
           <td>3</td>
           <td>AHD172</td>
           <td>INF041</td>
           <td>Senin</td>
           <td>2</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         <tr>
           <td>4</td>
           <td>AHD172</td>
           <td>INF021</td>
           <td>Selasa</td>
           <td>1</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         <tr>
           <td>5</td>
           <td>AHD172</td>
           <td>INF021</td>
           <td>Selasa</td>
           <td>1</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         <tr>
           <td>6</td>
           <td>STD853</td>
           <td>INF020</td>
           <td>Senin</td>
           <td>3</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         <tr>
           <td>7</td>
           <td>STD853</td>
           <td>INF072</td>
           <td>Selasa</td>
           <td>3</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         <tr>
           <td>8</td>
           <td>PTC256</td>
           <td>INF061</td>
           <td>Rabu</td>
           <td>2</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         <tr>
           <td>9</td>
           <td>PTC256</td>
           <td>INF059</td>
           <td>Jumat</td>
           <td>1</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         <tr>
           <td>10</td>
           <td>PTC256</td>
           <td>INF077</td>
           <td>Kamis</td>
           <td>2</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

         <tr>
           <td>11</td>
           <td>PTC256</td>
           <td>INF072</td>
           <td>Kamis</td>
           <td>2</td>
           <td>
            <button class="edit-btn" @click="editFaculty(faculty.id)">Lecture Material</button>
            <button class="delete-btn" @click="deleteFaculty(faculty.id)">Student Attendance</button>
            <button class="push-btn" @click="deleteFaculty(faculty.id)">Student Assessment</button>
           </td>
         </tr>

       </tbody>
     </table>
     <div id="update-container" v-if="editingFaculty">
       <input type="text" id="update-faculty_name-input" v-model="editingFaculty.faculty_name">
       <input type="text" id="update-program_name-input" v-model="editingFaculty.program_name">
       <button id="update-btn" @click="updateFaculty">Update</button>
       <button id="cancel-btn" @click="cancelEdit">Cancel</button>
     </div>
  </div>
  </div>
 </template>
 
 <script>
 export default {
  data() {
     return {
       newFaculty: {
        faculty_name: '',
        program_name: '',
       },
       facultys: JSON.parse(localStorage.getItem('facultys')) || [],
       editingFaculty: null,
       validRegex: /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/,
     };
  },
  methods: {
     addFaculty() {
       const faculty_name = this.newFaculty.faculty_name.trim();
       const program_name = this.newFaculty.program_name.trim();
     },
     editFaculty(id) {
       this.editingFaculty = this.facultys.find((faculty) => faculty.id === id);
     },
     updateFaculty() {
       if (this.editingFaculty.email.match(this.validRegex)) {
         this.facultys = this.facultys.map((faculty) => {
           if (faculty.id === this.editingFaculty.id) {
             return this.editingFaculty;
           }
           return faculty;
         });
         localStorage.setItem('facultys', JSON.stringify(this.facultys));
         this.cancelEdit();
       } else {
         alert('Invalid email address!');
       }
     },
     cancelEdit() {
       this.editingFaculty = null;
     },
     deleteFaculty(id) {
       this.facultys = this.facultys.filter((faculty) => faculty.id !== id);
       localStorage.setItem('facultys', JSON.stringify(this.facultys));
       if (this.facultys.length == 0) {
         this.cancelEdit();
       }
     },
  },
 };
 </script>
 
 <style scoped>
 #container {
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
 }
 
 h1 {
  margin-top: 0;
 }
 
 #input-container {
  margin-bottom: 20px;
 }
 
 #faculty_name-input,
 #program_name-input,
 #update-faculty_name-input,
 #update-program_name-input {
  padding: 10px;
  font-size: 16px;
  margin-right: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
 }
 
 #faculty_name-input:focus,
 #program_name-input:focus,
 #update-faculty_name-input:focus,
 #update-program_name-input:focus {
  border-color: #66afe9;
  outline: 0;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
     0 0 8px rgba(102, 175, 233, 0.6);
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6);
 }
 
 button {
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
 }
 
 button:hover {
  background-color: #3e8e41;
 }
 
 table {
  border-collapse: collapse;
  width: 100%;
  margin-bottom: 20px;
 }
 
 th,
 td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
 }
 
 th {
  background-color: #4CAF50;
  color: white;
 }
 
 #update-container {
  display: none;
  margin-top: 20px;
 }
 
 #update-btn {
  background-color: #2196F3;
 }
 
 #cancel-btn {
  background-color: #f44336;
 }
 
 .edit-btn {
  background-color: #ffc107;
 }
 
 .delete-btn {
  background-color: #2196F3;
  margin-left: 10px;
 }
 .push-btn {
  background-color: #ffc107;
  margin-left: 10px;
 }
 </style>