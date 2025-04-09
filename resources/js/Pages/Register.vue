<template>
  <v-card class="mx-auto mt-10" max-width="400" title="User Registration">
    <v-container>
      <v-text-field
        v-model="form.name"
        color="primary"
        label="Username"
        variant="underlined"
        :error-messages="form.errors.name"
      ></v-text-field>

      <v-text-field
        v-model="form.email"
        color="primary"
        label="Email"
        variant="underlined"
        :error-messages="form.errors.email"
      ></v-text-field>

      <v-text-field
        v-model="form.password"
        type="password"
        color="primary"
        label="Password"
        placeholder="Enter your password"
        variant="underlined"
        :error-messages="form.errors.password"
      ></v-text-field>
    </v-container>

    <v-divider></v-divider>

    <v-card-actions>
      <v-spacer></v-spacer>
      <Link href="/">
        <v-btn color="red">Login</v-btn>
      </Link>
      <v-btn color="success" :loading="form.processing" @click="register">
        Registration
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
})

const register = () => {
  form.post('/register')
}
</script>
