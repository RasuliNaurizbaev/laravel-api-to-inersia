<template>
  <v-container class="d-flex justify-center align-center" style="min-height: 100vh">
    <v-card class="mx-auto pa-6" width="500" elevation="8" rounded="xl">
      <v-card-title class="text-h4 text-center mb-4">Login</v-card-title>

      <v-form @submit.prevent="loginForm.post('/login')" ref="formRef">
        <v-text-field
          v-model="loginForm.email"
          label="Email"
          type="email"
          required
          clearable
          :error-messages="loginForm.errors.email"
        ></v-text-field>

        <v-text-field
          v-model="loginForm.password"
          label="Password"
          type="password"
          required
          clearable
          :error-messages="loginForm.errors.password"
        ></v-text-field>

        <v-alert
          v-if="Object.keys(loginForm.errors).length && !loginForm.errors.email && !loginForm.errors.password"
          type="error"
          dense
          outlined
          class="mt-2"
        >
          Invalid credentials. Please try again.
        </v-alert>

        <v-btn
          color="primary"
          type="submit"
          block
          class="mt-4"
          :loading="loginForm.processing"
          :disabled="loginForm.processing"
        >
          Login
        </v-btn>
      </v-form>

      <div class="text-center mt-4">
        Don’t have an account?
        <Link href="/register" class="text-primary text-decoration-underline">Register</Link>
      </div>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const formRef = ref(null)

const loginForm = useForm({
  email: '',
  password: ''
})
</script>
