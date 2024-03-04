<template>
  <div class="content">
    <div class="container-fluid">
      
     <h1>Student</h1>
     <div id="input-container">
       <input type="text" id="name-input" placeholder="Enter name" v-model="newStudent.name">
       <input type="text" id="address-input" placeholder="Enter address" v-model="newStudent.address">
       <input type="text" id="phone_number-input" placeholder="Enter phone number" v-model="newStudent.phone_number">
       <input type="date" id="date_of_birth-input" placeholder="Enter date of birth" v-model="newStudent.date_of_birth">
       <input type="email" id="email-input" placeholder="Enter email" v-model="newStudent.email">
       <input type="text" id="username-input" placeholder="Enter username" v-model="newStudent.username">
       <button id="add-btn" @click="addStudent">Add</button>
     </div>
     <table>
       <thead>
         <tr>
           <th>ID</th>
           <th>Name</th>
           <th>Address</th>
           <th>Phone Number</th>
           <th>Date Of birth</th>
           <th>Email</th>
           <th>Username</th>
           <th>Actions</th>
         </tr>
       </thead>
       <tbody id="table-body">
         <tr v-for="student in students" :key="student.id">
           <td>{{ student.id }}</td>
           <td>{{ student.name }}</td>
           <td>{{ student.address }}</td>
           <td>{{ student.phone_number }}</td>
           <td>{{ student.date_of_birth }}</td>
           <td>{{ student.email }}</td>
           <td>{{ student.username }}</td>
           <td>
             <button class="edit-btn" @click="editStudent(student.id)">Edit</button>
             <button class="delete-btn" @click="deleteStudent(student.id)">Delete</button>
           </td>
         </tr>
       </tbody>
     </table>
     <div id="update-container" v-if="editingStudent">
       <input type="text" id="update-name-input" v-model="editingStudent.name">
       <input type="text" id="update-address-input" v-model="editingStudent.address">
       <input type="text" id="update-phone_number-input" v-model="editingStudent.phone_number">
       <input type="text" id="update-date_of_birth-input" v-model="editingStudent.date_of_birth">
       <input type="text" id="update-email-input" v-model="editingStudent.email">
       <input type="text" id="update-username-input" v-model="editingStudent.username">
       <button id="update-btn" @click="updateStudent">Update</button>
       <button id="cancel-btn" @click="cancelEdit">Cancel</button>
     </div>
  </div>
  </div>
 </template>
 
 <script>
 export default {
  data() {
     return {
       newStudent: {
         name: '',
         address: '',
         phone_number: '',
         date_of_birth: '',
         email: '',
         username: '',
       },
       users: JSON.parse(localStorage.getItem('students')) || [],
       editingStudent: null,
       validRegex: /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/,
     };
  },
  methods: {
     addStudent() {
       const name = this.newStudent.name.trim();
       const address = this.newStudent.address.trim();
       const phone_number = this.newStudent.phone_number.trim();
       const date_of_birth = this.newStudent.date_of_birth.trim();
       const email = this.newStudent.email.trim();
       const username = this.newStudent.username.trim();
       if (email.match(this.validRegex)) {
         if (name && email != null) {
           let id = 1;
           let val = this.students.map(function (x) {
             return x.id;
           }).indexOf(id);
           while (val != -1) {
             id++;
             val = this.students.map(function (x) {
               return x.id;
             }).indexOf(id);
           }
           const student = {
             id: id,
             name: name,
             address: address,
             phone_number: phone_number,
             date_of_birth: date_of_birth,
             email: email,
             username: username,
           };
           this.students.push(student);
           localStorage.setItem('students', JSON.stringify(this.students));
           this.newStudent.name = '';
           this.newStudent.address = '';
           this.newStudent.phone_number = '';
           this.newStudent.date_of_birth = '';
           this.newStudent.email = '';
           this.newStudent.username = '';
         } else {
           alert('Name is Required');
         } 
       } else {
         alert('Invalid email address!');
       }
     },
     editStudent(id) {
       this.editingUser = this.students.find((student) => student.id === id);
     },
     updateStudent() {
       if (this.editingStudent.email.match(this.validRegex)) {
         this.users = this.students.map((student) => {
           if (student.id === this.editingStudent.id) {
             return this.editingStudent;
           }
           return student;
         });
         localStorage.setItem('students', JSON.stringify(this.students));
         this.cancelEdit();
       } else {
         alert('Invalid email address!');
       }
     },
     cancelEdit() {
       this.editingStudent = null;
     },
     deleteUser(id) {
       this.users = this.students.filter((student) => student.id !== id);
       localStorage.setItem('students', JSON.stringify(this.students));
       if (this.students.length == 0) {
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
 
 #name-input,
 #address-input,
 #phone_number-input,
 #date_of_birth-input,
 #email-input,
 #username-input,
 #update-name-input,
 #update-address-input,
 #update-phone_number-input,
 #update-date_of_birth-input,
 #update-email-input,
 #update-username-input {
  padding: 10px;
  font-size: 16px;
  margin-right: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
 }
 
 #name-input:focus,
 #address-input:focus,
 #phone_number-input:focus,
 #date_of_birth-input:focus,
 #email-input:focus,
 #username-input:focus,
 #update-name-input:focus,
 #update-address-input:focus,
 #update-phone_number-input:focus,
 #update-date_of_birth-input:focus,
 #update-email-input:focus,
 #update-username-input:focus {
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
  background-color: #dc3545;
  margin-left: 10px;
 }
 </style>