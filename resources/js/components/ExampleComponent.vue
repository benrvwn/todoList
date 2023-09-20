<template>
    <div class="container">
        <h1>ToDo List</h1>
        
        <div class="test alert alert-danger">
            <button class="btn btn-success my-2 px-5" @click="addTask">Add Task</button>
            <div class="task-container alert alert-light" v-for="item, index in todoList" :key="item.id">
                <div class="task-details">
                    <h4>{{ item.task_name }}</h4>
                    <p class="status">{{ item.status }}</p>
                    <p>{{ item.desc }}</p>
                </div>
                
                <div class="task-actions">
                    <button class="btn btn-secondary mx-1" @click="edit(item, index)" title="Edit"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-success mx-1" @click="mark(item, index)" title="Mark as completed"><i class="bi bi-clipboard-check"></i></button>
                    <button class="btn btn-primary mx-1" @click="viewAttach(item, index)" title="View attachment"><i class="bi bi-folder2-open"></i></button>
                    <button class="btn btn-danger mx-1" @click="remove(item, index)" title="Delete task"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        </div>
        <!-- CREATE Modal -->
        <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Task</h5>
            </div>
            <div class="modal-body">
                <label class="d-block">Task name:
                    <input type="text" name="name" class="form-control" placeholder="Enter task name" v-model="form.name"/>
                </label>
                <label class="d-block">Task Description:
                    <textarea name="desc" class="form-control" placeholder="Enter task description" v-model="form.desc"></textarea>
                </label>
                <label class="d-block">Attachment:
                    <input type="file" class="form-control" @change="selectedFiles = $event.target.files" multiple />
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="close">Close</button>
                <button type="button" class="btn btn-primary" @click="submit">Save changes</button>
            </div>
            </div>
        </div>
        </div>

        <!-- UPDATE Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Task</h5>
            </div>
            <div class="modal-body">
                <label class="d-block">Task name:
                    <input type="text" name="editName" class="form-control" v-model="formEdit.name">
                </label>
                <label class="d-block">Task Description:
                    <textarea name="editDesc" class="form-control" v-model="formEdit.desc"></textarea>
                </label>
                <label class="d-block">Add more attachment:
                    <input type="file" class="form-control" @change="addMoreFiles = $event.target.files" multiple />
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="close">Close</button>
                <button type="button" class="btn btn-primary" @click="update">Save changes</button>
            </div>
            </div>
        </div>
        </div>

        <!-- Attachment Modal -->
        <div class="modal fade" id="attachModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Attachment</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary p-1 attach-actions" v-for="item, index in attachments" :key="item.id">
                    {{ item.file_name }}
                    <div>
                        <a :href="downloadLinks(selectedName, item.file_name)" download><i class="bi bi-download"></i></a>
                        <button @click="removeAttach(item, index)"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="close">Close</button>
            </div>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
import { remove } from '@vue/shared';

    export default {
        props: ['list'],
        data() {
            return {
                todoList: this.list,
                form: {
                    name: '',
                    desc: ''
                },
                formEdit: {
                    name: '',
                    desc: '',
                },
                selectedId: null,
                attachments: null,
                selectedName: null,
                erorrs: null
            }
        },
        methods: {
            
            submit(){
                const vm = this;
                const formData = new FormData();
                if(this.selectedFiles){
                    for(let i = 0; i<this.selectedFiles.length; i++){
                        formData.append('files[]', this.selectedFiles[i]);
                    }
                }
                formData.append('name', this.form.name);
                formData.append('desc' ,this.form.desc);
                axios.post('/try', formData)
                .then(function (response) {
                    vm.todoList = response.data.data
                    vm.form.name = ""
                    vm.form.desc = ""
                    vm.selectedFiles = []
                    $('#createModal').modal('hide');
                })
                .catch(function (error) {
                    vm.errors = error.response.data.errors
                    alert(Object.values(vm.errors).join('\n'));
                    
                });
            },
            remove(item, index){
                const vm = this;
                const result = window.confirm('Are you sure you want to delete this task?');
                if(result){
                    axios.post(`/remove/${item.id}`)
                    .then(function (response) {
                        vm.todoList = response.data.data
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }  
            },
            edit(item, index){
                $('#editModal').modal('show');
                this.formEdit.name = item.task_name
                this.formEdit.desc = item.desc
                this.selectedId = item.id
            },
            update(){
                $('#editModal').modal('hide');
            
                const vm = this;
                const formData = new FormData();
                formData.append('name', this.formEdit.name);
                formData.append('desc',this.formEdit.desc);
                if(this.addMoreFiles){
                    for(let i = 0; i<this.addMoreFiles.length; i++){
                        formData.append('files[]', this.addMoreFiles[i]);
                    }
                }
                
                axios.post(`/update/${this.selectedId}`, formData)
                .then(function (response) {
                    console.log(response);
                    vm.todoList = response.data.data
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            viewAttach(item, index){
                const vm = this;
                axios.get(`/view-attachments/${item.id}`)
                .then(function (response) {
                    console.log(response);
                    vm.attachments = response.data.data
                    vm.selectedName = item.task_name
                })
                .catch(function (error) {
                    console.log(error);
                });
                $('#attachModal').modal('show');
            },
            close(){
                $('#editModal').modal('hide');
                $('#createModal').modal('hide');
                $('#attachModal').modal('hide');
            },
            addTask(){
                $('#createModal').modal('show');
            },
            downloadLinks(selectedName, item){
                return `uploads/${selectedName}/${item}`;
            },
            removeAttach(item, index){
                const result = window.confirm('Are you sure you want to delete this attachment?');
                const vm = this;
                if (result) {
                    axios.get(`/remove-attach/${vm.selectedName}/${item.id}`)
                    .then(function (response) {
                        vm.attachments = response.data.data
                        alert('File deleted successfully!');
                    })
                    .catch(function (error) {
                        console.log(error);
                    });                 
                }
            },
            mark(item, index){
                const result = window.confirm('Is this task completed?');
                const vm = this;
                if(result){
                    axios.get(`/completed/${item.id}`)
                    .then(function (response) {
                        vm.todoList = response.data.data
                        alert('Task Completed!');
                    })
                    .catch(function (error) {
                        console.log(error);
                    });   
                }
            }
        }
        
    }
</script>
