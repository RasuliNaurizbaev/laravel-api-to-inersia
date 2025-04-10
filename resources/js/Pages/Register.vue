<template>
  <v-container fluid class="fill-height">
    <v-row justify="center" align="center">
      <v-col cols="12" sm="8" md="4">
        <v-card class="pa-4 elevation-3">
          <!-- Card Header -->
          <v-card-title class="justify-center">
            <span class="text-h5 font-weight-bold">Create Your Account</span>
          </v-card-title>
          <v-card-subtitle class="text-center mb-4">
            Join us to manage your tasks efficiently.
          </v-card-subtitle>

          <!-- Registration Form -->
          <v-form @submit.prevent="register">
            <v-text-field v-model="form.name" label="Username" outlined dense :error-messages="form.errors.name"
              class="mb-3"></v-text-field>

            <v-text-field v-model="form.email" label="Email" outlined dense :error-messages="form.errors.email"
              class="mb-3"></v-text-field>

            <v-text-field v-model="form.password" :type="showPassword ? 'text' : 'password'" label="Password"
              placeholder="Enter your password" outlined dense :error-messages="form.errors.password" class="mb-4"
              :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="togglePassword"></v-text-field>

            <v-card-actions class="justify-center">
              <v-btn color="primary" :loading="form.processing" type="submit">
                Register
              </v-btn>
            </v-card-actions>
          </v-form>

          <v-divider class="my-4"></v-divider>

          <!-- Already have an account -->
          <div class="text-center">
            Already have an account?
            <Link href="/">
            Login   
            </Link>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { ref } from 'vue';

const showPassword = ref(false);

function togglePassword() {
  showPassword.value = !showPassword.value;
}

const form = useForm({
  name: '',
  email: '',
  password: '',
})

const register = () => {
  form.post('/register')
}
</script>

<style scoped>
.fill-height {
  min-height: 100vh;
}
</style>
