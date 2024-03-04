<template>
 
 </template>
 
 <script>
 
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