<template>
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users</h3>

                <div class="card-tools">
                  <button class="btn btn-success btn-sm" @click="newModal()">Add new <i class="fas fa-user-plus"></i> </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Registered at</th>
                    <th>Actions</th>
                  </tr>
                  <tr v-for="user in users" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.type | upText }}</td>
                    <td>{{ user.created_at | myDate }}</td>
                    <td>
                        <a href="#" @click="editModal(user)"> <i class="fa fa-edit"></i> </a> |
                        <a href="#" @click="deleteUser(user.id)">  <i class="fa fa-trash text-red"></i> </a>
                    </td>
                  </tr>
                  
                </tbody></table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>


            <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                          <h5 v-show="!editMode" class="modal-title" id="addNewLabel">Add new</h5>
                          <h5 v-show="editMode" class="modal-title" id="addNewLabel">Update</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                          </button>
                      </div>

                      <form @submit.prevent="editMode ? updateUser() : createUser()">
                        <div class="modal-body">

                            <div class="form-group">
                              <label>Name</label>
                              <input v-model="form.name" type="text" name="name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                              <has-error :form="form" field="name"></has-error>
                            </div>

                            <div class="form-group">
                              <label>Email</label>
                              <input v-model="form.email" type="email" name="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                              <has-error :form="form" field="email"></has-error>
                            </div>

                            <div class="form-group">
                              <label>Bio</label>
                              <textarea v-model="form.bio" type="text" name="bio" class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                              <has-error :form="form" field="bio"></has-error>
                            </div>

                            <div class="form-group">
                              <label>Type</label>
                              <select v-model="form.type" name="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                <option value="" disabled>Select user role</option>  
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                                <option value="author">Author</option>
                              </select>
                              <has-error :form="form" field="type"></has-error>
                            </div>

                            <div class="form-group">
                              <label>Password</label>
                              <input v-model="form.password" type="password" name="password" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                              <has-error :form="form" field="password"></has-error>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button v-show="editMode" type="submit" class="btn btn-success">Update</button>
                            <button v-show="!editMode" type="submit" class="btn btn-primary">Save</button>
                        </div>
                      </form>
                    </div>
                </div>
            </div>

        </div>
</template>

<script>
    export default {
        data(){
          return{
            editMode: false,
            users: {},
            form: new Form({
              id: '',
              name: '',
              email: '',
              password: '',
              type: '',
              bio: '',
              photo: '',
            })
          }
        },
        methods: {
          createUser(){
            this.$Progress.start();
            this.form.post('api/user')
            .then(()=>{
                Fire.$emit('afterCreate');
                $('#addNew').modal('hide');
                toast.fire({
                    type: 'success',
                    title: 'User created in successfully'
                });
                //this.loadUsers();
                this.$Progress.finish();
            })
            .catch(()=>{

            })
            
          },
          loadUsers(){
              //axios.get("api/user").then(({ data }) => (this.users = data.data));

              var that = this;
              axios.get("api/user")
              .then(function ({data}) {
                that.users = data.data;
              })
              .catch(function (error) {
                console.log(error);
              })
          },
          deleteUser(id){
                    swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.value) {
                            this.form.delete('api/user/'+id).then(()=>{
                                swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                )
                                Fire.$emit('afterCreate');
                            })
                        }
                    }).catch(()=>{
                        Swal.fire({
                            type: 'error',
                            title: 'Error...',
                            text: 'Something went wrong!',
                        })
                    })
          },
          updateUser(){
              this.$Progress.start();
              this.form.put('api/user/'+ this.form.id)
              .then(()=>{
                  $('#addNew').modal('hide');
                  swal.fire(
                        'Updated!',
                        'Inormation has been updated.',
                        'success'
                  )
                  this.$Progress.finish();
                  Fire.$emit('afterCreate');
              })
              .catch(()=>{
                  this.$Progress.fail();
              })
          },
          newModal(){
              this.editMode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },
          editModal(user){
              this.editMode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(user);
          }
        },
        mounted() {
            this.loadUsers();
            Fire.$on('afterCreate',()=>{
               this.loadUsers(); 
            });
            //setInterval(()=> this.loadUsers(), 3000);
        }
    }
</script>
