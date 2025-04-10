<template>
    <v-container class="py-6">
      <v-card class="mx-auto rounded-lg" max-width="900" elevation="3">
        <!-- Header Section with Gradient -->
        <v-sheet color="primary" dark class="py-3 px-4 rounded-t-lg">
          <div class="d-flex align-center">
            <v-icon large class="mr-3">mdi-format-list-checks</v-icon>
            <h1 class="text-h4 font-weight-medium">Todo Dashboard</h1>
            <v-spacer></v-spacer>
            <v-btn color="white" class="primary--text" type="submit" @click="logout" elevation="1">
              <v-icon left>mdi-logout</v-icon>
              Logout
            </v-btn>
          </div>
        </v-sheet>
  
        <!-- New Todo Form Card -->
        <v-card class="mx-4 my-5 rounded-lg" outlined elevation="1">
          <v-card-title class="primary--text font-weight-bold pb-0">
            <v-icon color="primary" class="mr-2">mdi-plus-circle</v-icon>
            Create New Task
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitAddTodo">
              <v-row>
                <v-col cols="12" md="8">
                  <v-text-field 
                    v-model="newTodo.title" 
                    label="Task Title"
                    placeholder="What needs to be done?" 
                    outlined 
                    dense 
                    prepend-inner-icon="mdi-pencil"
                    required>
                  </v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-btn 
                    color="primary" 
                    block 
                    type="submit" 
                    height="40" 
                    :loading="isLoading"
                    class="mt-md-0 mt-0">
                    <v-icon left>mdi-plus</v-icon>
                    Add Task
                  </v-btn>
                </v-col>
                <v-col cols="12">
                  <v-textarea 
                    v-model="newTodo.description" 
                    label="Description"
                    placeholder="Add details about your task..." 
                    outlined 
                    rows="2"
                    prepend-inner-icon="mdi-text"
                    required>
                  </v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
  
        <!-- Todo List Section -->
        <v-card-title class="px-4 d-flex align-center">
          <v-icon color="primary" class="mr-2">mdi-format-list-bulleted</v-icon>
          <span class="text-h6 font-weight-bold">Your Tasks</span>
          <v-spacer></v-spacer>
          <v-text-field 
            v-model="search" 
            append-icon="mdi-magnify" 
            label="Search tasks" 
            single-line 
            hide-details
            outlined 
            dense 
            class="ml-2" 
            style="max-width: 250px">
          </v-text-field>
        </v-card-title>
  
        <!-- Data Table with Custom Styling -->
        <v-data-table 
          :headers="headers" 
          :items="todosList" 
          :search="search" 
          :items-per-page="5"
          class="elevation-0 mx-4 mb-4 rounded-lg" 
          :loading="isLoading"
          loading-text="Loading tasks... Please wait" 
          :footer-props="{
            'items-per-page-options': [5, 10, 15],
            'items-per-page-text': 'Tasks per page'
          }">
          <!-- Custom status column with visual indicators -->
          <template v-slot:item.is_completed="{ item }">
            <v-chip 
              small 
              :color="item.is_completed ? 'success' : 'warning'" 
              text-color="white" 
              class="mr-2">
              {{ item.is_completed ? 'Done' : 'Pending' }}
            </v-chip>
            <!-- Using PUT to update the todo including the updated checkbox -->
            <v-checkbox 
              v-model="item.is_completed" 
              @change="toggleComplete(item)" 
              color="success" 
              hide-details 
              dense>
            </v-checkbox>
          </template>
  
          <!-- Formatted date with icon -->
          <template v-slot:item.created_at="{ item }">
            <div class="d-flex align-center">
              <v-icon small class="mr-1 grey--text">mdi-calendar</v-icon>
              {{ formatDate(item.created_at) }}
            </div>
          </template>
  
          <!-- Action buttons -->
          <template v-slot:item.actions="{ item }">
            <v-btn icon small color="info" class="mr-1" @click="editItem(item)">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn icon small color="error" @click="deleteItem(item)">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
  
          <!-- Empty state when no todos -->
          <template v-slot:no-data>
            <div class="pa-6 text-center">
              <v-icon size="64" color="grey lighten-1">mdi-clipboard-text-outline</v-icon>
              <p class="text-h6 mt-2 grey--text">No tasks found</p>
              <p class="grey--text">Add your first task or try a different search</p>
            </div>
          </template>
        </v-data-table>
  
        <!-- Edit Dialog -->
        <v-dialog v-model="dialogEdit" max-width="600px">
          <v-card class="rounded-lg">
            <v-card-title class="primary white--text text-h5 rounded-t-lg">
              <v-icon color="white" class="mr-2">mdi-pencil</v-icon>
              Edit Task
            </v-card-title>
            <v-card-text class="pt-5">
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <v-text-field 
                      v-model="editedItem.title" 
                      label="Task Title"
                      prepend-inner-icon="mdi-format-title" 
                      outlined>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea 
                      v-model="editedItem.description" 
                      label="Description"
                      prepend-inner-icon="mdi-text" 
                      outlined 
                      rows="3">
                    </v-textarea>
                  </v-col>
                  <v-col cols="12">
                    <v-switch 
                      v-model="editedItem.is_completed" 
                      label="Mark as completed"
                      color="success" 
                      inset>
                    </v-switch>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions class="pa-4">
              <v-spacer></v-spacer>
              <v-btn color="grey darken-1" text @click="closeEdit">
                <v-icon left>mdi-close</v-icon>
                Cancel
              </v-btn>
              <v-btn color="primary" @click="saveEdit" :loading="isLoading" elevation="2">
                <v-icon left>mdi-content-save</v-icon>
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
  
        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="dialogDelete" max-width="450px">
          <v-card class="rounded-lg">
            <v-card-title class="error white--text rounded-t-lg">
              <v-icon color="white" class="mr-2">mdi-alert</v-icon>
              Confirm Deletion
            </v-card-title>
            <v-card-text class="pa-4 text-center">
              <v-icon size="64" color="error" class="my-4">mdi-delete-alert</v-icon>
              <p class="text-h6">Are you sure you want to delete this task?</p>
              <p class="grey--text">This action cannot be undone.</p>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions class="pa-4">
              <v-spacer></v-spacer>
              <v-btn color="grey darken-1" text @click="closeDelete">
                <v-icon left>mdi-close</v-icon>
                Cancel
              </v-btn>
              <v-btn color="error" @click="deleteItemConfirm" :loading="isLoading">
                <v-icon left>mdi-delete</v-icon>
                Delete
              </v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
  
        <!-- Snackbar -->
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000" bottom right>
          <div class="d-flex align-center">
            <v-icon class="mr-2" v-if="snackbar.color === 'success'">mdi-check-circle</v-icon>
            <v-icon class="mr-2" v-else-if="snackbar.color === 'error'">mdi-alert-circle</v-icon>
            {{ snackbar.text }}
          </div>
          <template v-slot:action="{ attrs }">
            <v-btn text v-bind="attrs" @click="snackbar.show = false">
              Close
            </v-btn>
          </template>
        </v-snackbar>
      </v-card>
    </v-container>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted, watch } from 'vue';
  import axios from 'axios';
  import { router } from '@inertiajs/vue3';
  
  // Accept todos as a prop (passed in from Inertia)
  const props = defineProps({
    todos: {
      type: Array,
      default: () => []
    }
  });
  
  // Enable axios to send cookies for Sanctum authentication
  axios.defaults.withCredentials = true;
  
  // Reactive state initialization
  const search = ref('');
  const isLoading = ref(false);
  const headers = ref([
    { text: 'Title', value: 'title' },
    { text: 'Description', value: 'description' },
    { text: 'Completed', value: 'is_completed' },
    { text: 'Created At', value: 'created_at' },
    { text: 'Actions', value: 'actions', sortable: false }
  ]);
  
  // Initialize a local reactive list from the passed-in prop
  const todosList = ref([...props.todos]);
  
  const newTodo = reactive({ title: '', description: '', is_completed: false });
  const editedItem = reactive({ id: null, title: '', description: '', is_completed: false });
  const defaultItem = reactive({ id: null, title: '', description: '', is_completed: false });
  const dialogEdit = ref(false);
  const dialogDelete = ref(false);
  const snackbar = reactive({ show: false, text: '', color: 'success' });
  
  // Fetch todos from the API endpoint
  async function fetchTodos() {
    isLoading.value = true;
    try {
      const response = await axios.get('/todos');
      todosList.value = response.data;
    } catch (error) {
      showSnackbar('Failed to load todos', 'error');
      console.error(error);
    } finally {
      isLoading.value = false;
    }
  }
  
  // Add a new todo via POST
  async function submitAddTodo() {
    isLoading.value = true;
    try {
      const response = await axios.post('/todos', newTodo);
      todosList.value.push(response.data);
      // Reset newTodo values
      newTodo.title = '';
      newTodo.description = '';
      newTodo.is_completed = false;
      showSnackbar('Todo added successfully', 'success');
    } catch (error) {
      showSnackbar('Failed to add todo', 'error');
      console.error(error);
    } finally {
      isLoading.value = false;
    }
  }
  
  // Toggle completion status using a full update (PUT)
  async function toggleComplete(item) {
    isLoading.value = true;
    try {
      // Prepare the updated payload
      const updatedData = { ...item, is_completed: item.is_completed };
      const response = await axios.put(`/todos/${item.id}`, updatedData);
      const index = todosList.value.findIndex(t => t.id === item.id);
      if (index !== -1) {
        todosList.value.splice(index, 1, response.data);
      }
      showSnackbar('Todo status updated', 'success');
    } catch (error) {
      // Revert checkbox change on error
      item.is_completed = !item.is_completed;
      showSnackbar('Failed to update todo status', 'error');
      console.error(error);
    } finally {
      isLoading.value = false;
    }
  }
  
  // Open the edit dialog with the selected todo
  function editItem(item) {
    Object.assign(editedItem, item);
    dialogEdit.value = true;
  }
  
  // Save changes after editing a todo
  async function saveEdit() {
    isLoading.value = true;
    try {
      const response = await axios.put(`/todos/${editedItem.id}`, editedItem);
      const index = todosList.value.findIndex(t => t.id === editedItem.id);
      if (index !== -1) {
        todosList.value.splice(index, 1, response.data);
      }
      showSnackbar('Todo updated successfully', 'success');
    } catch (error) {
      showSnackbar('Failed to update todo', 'error');
      console.error(error);
    } finally {
      isLoading.value = false;
      dialogEdit.value = false;
    }
  }
  
  // Close the edit dialog and reset the edited item
  function closeEdit() {
    dialogEdit.value = false;
    Object.assign(editedItem, defaultItem);
  }
  
  // Open the delete confirmation dialog
  function deleteItem(item) {
    Object.assign(editedItem, item);
    dialogDelete.value = true;
  }
  
  // Confirm deletion of a todo
  async function deleteItemConfirm() {
    isLoading.value = true;
    try {
      await axios.delete(`/todos/${editedItem.id}`);
      const index = todosList.value.findIndex(t => t.id === editedItem.id);
      if (index !== -1) {
        todosList.value.splice(index, 1);
      }
      showSnackbar('Todo deleted successfully', 'success');
    } catch (error) {
      showSnackbar('Failed to delete todo', 'error');
      console.error(error);
    } finally {
      isLoading.value = false;
      dialogDelete.value = false;
    }
  }
  
  // Close the delete confirmation dialog
  function closeDelete() {
    dialogDelete.value = false;
    Object.assign(editedItem, defaultItem);
  }
  
  // Format date for display (e.g., "Apr 10, 2025")
  function formatDate(dateString) {
    if (!dateString) return '';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
  }
  
  // Show a snackbar message
  function showSnackbar(text, color = 'success') {
    snackbar.show = true;
    snackbar.text = text;
    snackbar.color = color;
  }
  
  // Logout using Inertia's router
  function logout() {
    isLoading.value = true;
    axios.post('/logout')
      .then(() => {
        router.visit('/', { replace: true });
      })
      .catch(error => {
        showSnackbar('Logout failed', 'error');
        console.error(error);
      })
      .finally(() => {
        isLoading.value = false;
      });
  }
  
  // On component mount, fetch todos only if none were passed via props
  onMounted(() => {
    if (!todosList.value.length) {
      fetchTodos();
    }
  });
  
  // Watch for changes in the todos prop to update the local list
  watch(() => props.todos, (newTodos) => {
    todosList.value = [...newTodos];
  });
  </script>
  
  <style scoped>
  .theme--light.v-data-table .v-data-table__odd {
    background-color: rgba(0, 0, 0, 0.05);
  }
  </style>
  