<template>
    <v-container>
        <v-card class="mx-auto" max-width="800">
            <v-card-title class="text-h4 mb-2">
                Todo Dashboard
                <v-spacer></v-spacer>
                <v-btn color="error" @click="logout">Logout</v-btn>
            </v-card-title>

            <v-card class="mx-auto mb-6 pa-4" outlined>
                <v-card-title class="text-h6">Add New Todo</v-card-title>
                <v-form @submit.prevent="addTodo">
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="newTodo.title" label="Todo Title" outlined dense
                                required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="newTodo.description" label="Description" outlined rows="3"
                                required></v-textarea>
                        </v-col>
                        <v-col cols="12" class="d-flex justify-end">
                            <v-btn color="primary" type="submit" :loading="isLoading">
                                Add Todo
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card>

            <v-card-title class="text-h6">
                Your Todos
                <v-spacer></v-spacer>
                <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line hide-details dense
                    outlined class="ml-2" style="max-width: 250px"></v-text-field>
            </v-card-title>

            <v-data-table :headers="headers" :items="todos" :search="search" :items-per-page="5" class="elevation-1"
                :loading="isLoading" loading-text="Loading todos... Please wait">
                <template v-slot:item.is_completed="{ item }">
                    <v-checkbox v-model="item.is_completed" @change="toggleComplete(item)" color="success"
                        hide-details></v-checkbox>
                </template>
                <template v-slot:item.created_at="{ item }">
                    {{ formatDate(item.created_at) }}
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-icon small class="mr-2" @click="editItem(item)">
                        mdi-pencil
                    </v-icon>
                    <v-icon small @click="deleteItem(item)">
                        mdi-delete
                    </v-icon>
                </template>
            </v-data-table>

            <v-dialog v-model="dialogEdit" max-width="500px">
                <v-card>
                    <v-card-title class="text-h5">Edit Todo</v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="editedItem.title" label="Todo Title"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="editedItem.description" label="Description"
                                        rows="3"></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-checkbox v-model="editedItem.is_completed" label="Completed"
                                        color="success"></v-checkbox>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="closeEdit">Cancel</v-btn>
                        <v-btn color="blue darken-1" text @click="saveEdit" :loading="isLoading">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="dialogDelete" max-width="500px">
                <v-card>
                    <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                        <v-btn color="red darken-1" text @click="deleteItemConfirm" :loading="isLoading">Delete</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
                {{ snackbar.text }}
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
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
export default {
    props: {
        todos: Array,
        headers: Array,
    },
    setup(props) {
        const newTodo = ref({
            title: '',
            description: '',
        });
        const editedItem = ref({});
        const dialogEdit = ref(false);
        const dialogDelete = ref(false);
        const isLoading = ref(false);
        const snackbar = ref({
            show: false,
            text: '',
            color: 'success',
        });
        const search = ref('');

        const addTodo = () => {
            if (!newTodo.value.title || !newTodo.value.description) {
                snackbar.value.text = 'Please fill in all fields';
                snackbar.value.color = 'error';
                snackbar.value.show = true;
                return;
            }
            isLoading.value = true;
            // Add your logic to add a new todo
            isLoading.value = false;
        };

        const editItem = (item) => {
            editedItem.value = { ...item };
            dialogEdit.value = true;
        };

        const saveEdit = () => {
            isLoading.value = true;
            // Add your logic to save the edited item
            isLoading.value = false;
            dialogEdit.value = false;
        };

        const deleteItem = (item) => {
            editedItem.value = { ...item };
            dialogDelete.value = true;
        };

        const deleteItemConfirm = () => {
            isLoading.value = true;
            // Add your logic to delete the item
            isLoading.value = false;
            dialogDelete.value = false;
        };

        const closeEdit = () => {
            dialogEdit.value = false;
        };

        const closeDelete = () => {
            dialogDelete.value = false;
        };

        const logout = () => {
            isLoading.value = true;
            router.post('/logout', {}, {
                onFinish: () => {
                    isLoading.value = false;
                },
                onSuccess: () => {
                    // Перенаправляем на страницу входа
                    router.visit('/login');
                }
            });
        };


        return {
            newTodo,
            editedItem,
            dialogEdit,
            dialogDelete,
            isLoading,
            snackbar,
            search,
            addTodo,
            editItem,
            saveEdit,
            deleteItem,
            deleteItemConfirm,
            closeEdit,
            closeDelete,
            logout, // Тут добавляешь logout
        };
    }

}
</script>

<style scoped>
.theme--light.v-data-table .v-data-table__odd {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>