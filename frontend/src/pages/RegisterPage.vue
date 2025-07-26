<template>
  <q-page class="flex flex-center">
    <div class="column items-center q-gutter-md" style="max-width: 400px; width: 100%;">
      <div class="text-h4 text-center q-mb-lg">Register</div>
      
      <q-form @submit="handleRegister" class="q-gutter-md" style="width: 100%;">
        <q-input
          v-model="form.name"
          label="Name"
          :rules="[val => !!val || 'Name is required']"
          outlined
        />

        <q-input
          v-model="form.email"
          label="Email"
          type="email"
          :rules="[val => !!val || 'Email is required', val => validateEmail(val) || 'Invalid email format']"
          outlined
        />

        <q-input
          v-model="form.password"
          label="Password"
          :type="showPassword ? 'text' : 'password'"
          :rules="[val => !!val || 'Password is required', val => val.length >= 8 || 'Password must be at least 8 characters']"
          outlined
        >
          <template v-slot:append>
            <q-icon
              :name="showPassword ? 'visibility' : 'visibility_off'"
              class="cursor-pointer"
              @click="showPassword = !showPassword"
            />
          </template>
        </q-input>

        <q-input
          v-model="form.password_confirmation"
          label="Confirm Password"
          :type="showPassword ? 'text' : 'password'"
          :rules="[
            val => !!val || 'Password confirmation is required',
            val => val === form.password || 'Passwords do not match'
          ]"
          outlined
        />

        <div v-if="authStore.error" class="text-negative text-center">
          {{ authStore.error }}
        </div>

        <q-btn
          type="submit"
          color="primary"
          label="Register"
          :loading="authStore.isLoading"
          style="width: 100%;"
          size="lg"
        />

        <div class="text-center q-mt-md">
          <q-btn
            flat
            color="primary"
            label="Already have an account? Login"
            @click="$router.push('/login')"
          />
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import { useAuthStore } from 'src/stores/auth-store';

const router = useRouter();
const $q = useQuasar();
const authStore = useAuthStore();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const showPassword = ref(false);

const validateEmail = (email: string) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

const handleRegister = async () => {
  authStore.clearError();
  
  const result = await authStore.register({
    name: form.value.name,
    email: form.value.email,
    password: form.value.password,
    password_confirmation: form.value.password_confirmation,
  });

  if (result.success) {
    $q.notify({
      type: 'positive',
      message: 'Registration successful!',
    });
    await router.push('/');
  }
};

onMounted(() => {
  // Clear any existing errors
  authStore.clearError();
});
</script> 