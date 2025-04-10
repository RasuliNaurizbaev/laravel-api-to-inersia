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
                                <v-text-field v-model="newTodo.title" label="Task Title"
                                    placeholder="What needs to be done?" outlined dense prepend-inner-icon="mdi-pencil"
                                    required></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-btn color="primary" block type="submit" height="40" :loading="isLoading"
                                    class="mt-md-0 mt-0">
                                    <v-icon left>mdi-plus</v-icon>
                                    Add Task
                                </v-btn>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="newTodo.description" label="Description"
                                    placeholder="Add details about your task..." outlined rows="2"
                                    prepend-inner-icon="mdi-text" required></v-textarea>
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
                <v-text-field v-model="search" append-icon="mdi-magnify" label="Search tasks" single-line hide-details
                    outlined dense class="ml-2" style="max-width: 250px"></v-text-field>
            </v-card-title>

            <!-- Data Table with Custom Styling -->
            <v-data-table :headers="headers" :items="todos" :search="search" :items-per-page="5"
                class="elevation-0 mx-4 mb-4 rounded-lg" :loading="isLoading"
                loading-text="Loading tasks... Please wait" :footer-props="{
                    'items-per-page-options': [5, 10, 15],
                    'items-per-page-text': 'Tasks per page'
                }">
                <!-- Custom status column with better visual indicators -->
                <template v-slot:item.is_completed="{ item }">
                    <v-chip small :color="item.is_completed ? 'success' : 'warning'" text-color="white" class="mr-2">
                        {{ item.is_completed ? 'Done' : 'Pending' }}
                    </v-chip>
                    <v-checkbox v-model="item.is_completed" @change="toggleComplete(item)" color="success" hide-details
                        dense></v-checkbox>
                </template>

                <!-- Formatted date with icon -->
                <template v-slot:item.created_at="{ item }">
                    <div class="d-flex align-center">
                        <v-icon small class="mr-1 grey--text">mdi-calendar</v-icon>
                        {{ formatDate(item.created_at) }}
                    </div>
                </template>

                <!-- Improved action buttons -->
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

            <!-- Edit Dialog with improved styling -->
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
                                    <v-text-field v-model="editedItem.title" label="Task Title"
                                        prepend-inner-icon="mdi-format-title" outlined></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="editedItem.description" label="Description"
                                        prepend-inner-icon="mdi-text" outlined rows="3"></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-switch v-model="editedItem.is_completed" label="Mark as completed"
                                        color="success" inset></v-switch>
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

            <!-- Enhanced Snackbar -->
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

<script>
import { router } from '@inertiajs/vue3'; // Import the router
import { ref } from 'vue';

export default {
    data() {
        return {
            search: '',
            isLoading: false,
            headers: [
                { text: 'Title', value: 'title' },
                { text: 'Description', value: 'description' },
                { text: 'Completed', value: 'is_completed' },
                { text: 'Created At', value: 'created_at' },
                { text: 'Actions', value: 'actions', sortable: false }
            ],
            todos: [],
            newTodo: { title: '', description: '', is_completed: false },
            editedItem: { id: null, title: '', description: '', is_completed: false },
            defaultItem: { id: null, title: '', description: '', is_completed: false },
            dialogEdit: false,
            dialogDelete: false,
            snackbar: { show: false, text: '', color: 'success' }
        };
    },

    mounted() {
        this.fetchTodos();
    },

    methods: {
        async fetchTodos() {
            this.isLoading = true;
            try {
                const response = await fetch('/todos', {
                    headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                });
                if (!response.ok) throw new Error('Failed to fetch todos');
                const data = await response.json();
                this.todos = data;
            } catch (error) {
                this.showSnackbar('Failed to load todos', 'error');
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },

        async submitAddTodo() {
            this.isLoading = true;
            try {
                const response = await fetch('/todos', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${localStorage.getItem('token')}` },
                    body: JSON.stringify(this.newTodo)
                });
                if (!response.ok) throw new Error('Failed to add todo');
                const data = await response.json();
                this.todos.push(data);
                this.newTodo = { title: '', description: '', is_completed: false };
                this.showSnackbar('Todo added successfully', 'success');
            } catch (error) {
                this.showSnackbar('Failed to add todo', 'error');
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },

        async toggleComplete(item) {
            this.isLoading = true;
            try {
                const response = await fetch(`/todos/${item.id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${localStorage.getItem('token')}` },
                    body: JSON.stringify({ is_completed: item.is_completed })
                });
                if (!response.ok) throw new Error('Failed to update todo status');
                this.showSnackbar('Todo status updated', 'success');
            } catch (error) {
                item.is_completed = !item.is_completed;
                this.showSnackbar('Failed to update todo status', 'error');
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },

        editItem(item) {
            this.editedItem = Object.assign({}, item);
            this.dialogEdit = true;
        },

        async saveEdit() {
            this.isLoading = true;
            try {
                const response = await fetch(`/todos/${this.editedItem.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${localStorage.getItem('token')}` },
                    body: JSON.stringify(this.editedItem)
                });
                if (!response.ok) throw new Error('Failed to update todo');
                const updatedTodo = await response.json();
                const index = this.todos.findIndex(t => t.id === updatedTodo.id);
                if (index !== -1) this.todos.splice(index, 1, updatedTodo);
                this.showSnackbar('Todo updated successfully', 'success');
            } catch (error) {
                this.showSnackbar('Failed to update todo', 'error');
                console.error(error);
            } finally {
                this.isLoading = false;
                this.dialogEdit = false;
            }
        },

        closeEdit() {
            this.dialogEdit = false;
            this.editedItem = Object.assign({}, this.defaultItem);
        },

        deleteItem(item) {
            this.editedItem = Object.assign({}, item);
            this.dialogDelete = true;
        },

        async deleteItemConfirm() {
            this.isLoading = true;
            try {
                const response = await fetch(`/todos/${this.editedItem.id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
                });
                if (!response.ok) throw new Error('Failed to delete todo');
                const index = this.todos.findIndex(t => t.id === this.editedItem.id);
                if (index !== -1) this.todos.splice(index, 1);
                this.showSnackbar('Todo deleted successfully', 'success');
            } catch (error) {
                this.showSnackbar('Failed to delete todo', 'error');
                console.error(error);
            } finally {
                this.isLoading = false;
                this.dialogDelete = false;
            }
        },

        closeDelete() {
            this.dialogDelete = false;
            this.editedItem = Object.assign({}, this.defaultItem);
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        },

        showSnackbar(text, color = 'success') {
            this.snackbar = { show: true, text, color };
        },

        logout() {
            this.isLoading = true;
            router.post('/logout', {}, {
                onFinish: () => {
                    this.isLoading = false;
                },
                onSuccess: () => {
                    router.visit('/', {
                        replace: true,
                        preserveState: false,
                        only: [],
                    });
                },
                onError: (errors) => {
                    this.isLoading = false;
                    this.showSnackbar('Logout failed', 'error');
                    console.error('Logout Errors:', errors);
                },
            });
        }
    }
};
</script>

<style scoped>
.theme--light.v-data-table .v-data-table__odd {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>