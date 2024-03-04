<template>
    <div class="content">
      <div class="container-fluid">
        
       <h1>Lecturer</h1>
       <div id="input-container">
         <input type="text" id="name-input" placeholder="Enter name" v-model="newUser.name">
         <input type="text" id="address-input" placeholder="Enter address" v-model="newUser.address">
         <input type="text" id="phone_number-input" placeholder="Enter phone number" v-model="newUser.phone_number">
         <input type="date" id="date_of_birth-input" placeholder="Enter date of birth" v-model="newUser.date_of_birth">
         <input type="email" id="email-input" placeholder="Enter email" v-model="newUser.email">
         <input type="text" id="username-input" placeholder="Enter username" v-model="newUser.username">
         <button id="add-btn" @click="addUser">Add</button>
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
           <tr v-for="user in users" :key="user.id">
             <td>{{ user.id }}</td>
             <td>{{ user.name }}</td>
             <td>{{ user.address }}</td>
             <td>{{ user.phone_number }}</td>
             <td>{{ user.date_of_birth }}</td>
             <td>{{ user.email }}</td>
             <td>{{ user.username }}</td>
             <td>
               <button class="edit-btn" @click="editUser(user.id)">Edit</button>
               <button class="delete-btn" @click="deleteUser(user.id)">Delete</button>
             </td>
           </tr>
         </tbody>
       </table>
       <div id="update-container" v-if="editingUser">
         <input type="text" id="update-name-input" v-model="editingUser.name">
         <input type="text" id="update-address-input" v-model="editingUser.address">
         <input type="text" id="update-phone_number-input" v-model="editingUser.phone_number">
         <input type="text" id="update-date_of_birth-input" v-model="editingUser.date_of_birth">
         <input type="text" id="update-email-input" v-model="editingUser.email">
         <input type="text" id="update-username-input" v-model="editingUser.username">
         <button id="update-btn" @click="updateUser">Update</button>
         <button id="cancel-btn" @click="cancelEdit">Cancel</button>
       </div>
    </div>
    </div>
   </template>
   
   <script>
   export default {
    data() {
       return {
         newUser: {
           name: '',
           address: '',
           phone_number: '',
           date_of_birth: '',
           email: '',
           username: '',
         },
         users: JSON.parse(localStorage.getItem('users')) || [],
         editingUser: null,
         validRegex: /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/,
       };
    },
    methods: {
       addUser() {
         const name = this.newUser.name.trim();
         const address = this.newUser.address.trim();
         const phone_number = this.newUser.phone_number.trim();
         const date_of_birth = this.newUser.date_of_birth.trim();
         const email = this.newUser.email.trim();
         const username = this.newUser.username.trim();
         if (email.match(this.validRegex)) {
           if (name && email != null) {
             let id = 1;
             let val = this.users.map(function (x) {
               return x.id;
             }).indexOf(id);
             while (val != -1) {
               id++;
               val = this.users.map(function (x) {
                 return x.id;
               }).indexOf(id);
             }
             const user = {
               id: id,
               name: name,
               address: address,
               phone_number: phone_number,
               date_of_birth: date_of_birth,
               email: email,
               username: username,
             };
             this.users.push(user);
             localStorage.setItem('users', JSON.stringify(this.users));
             this.newUser.name = '';
             this.newUser.address = '';
             this.newUser.phone_number = '';
             this.newUser.date_of_birth = '';
             this.newUser.email = '';
             this.newUser.username = '';
           } else {
             alert('Name is Required');
           } 
         } else {
           alert('Invalid email address!');
         }
       },
       editUser(id) {
         this.editingUser = this.users.find((user) => user.id === id);
       },
       updateUser() {
         if (this.editingUser.email.match(this.validRegex)) {
           this.users = this.users.map((user) => {
             if (user.id === this.editingUser.id) {
               return this.editingUser;
             }
             return user;
           });
           localStorage.setItem('users', JSON.stringify(this.users));
           this.cancelEdit();
         } else {
           alert('Invalid email address!');
         }
       },
       cancelEdit() {
         this.editingUser = null;
       },
       deleteUser(id) {
         this.users = this.users.filter((user) => user.id !== id);
         localStorage.setItem('users', JSON.stringify(this.users));
         if (this.users.length == 0) {
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