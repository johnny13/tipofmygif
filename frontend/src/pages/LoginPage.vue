<template>
  <q-page class="flex flex-center">
    <div class="column items-center q-gutter-md" style="max-width: 400px; width: 100%;">
      <div class="text-h4 text-center q-mb-lg">Login</div>
      
      <q-form @submit="handleLogin" class="q-gutter-md" style="width: 100%;">
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
          :rules="[val => !!val || 'Password is required']"
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

        <div v-if="authStore.error" class="text-negative text-center">
          {{ authStore.error }}
        </div>

        <q-btn
          type="submit"
          color="primary"
          label="Login"
          :loading="authStore.isLoading"
          style="width: 100%;"
          size="lg"
        />

        <div class="text-center q-mt-md">
          <q-btn
            flat
            color="primary"
            label="Don't have an account? Register"
            @click="$router.push('/register')"
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
  email: '',
  password: '',
});

const showPassword = ref(false);

const validateEmail = (email: string) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

const handleLogin = async () => {
  authStore.clearError();
  
  const result = await authStore.login({
    email: form.value.email,
    password: form.value.password,
  });

  if (result.success) {
    $q.notify({
      type: 'positive',
      message: 'Login successful!',
    });
    await router.push('/');
  }
};

onMounted(() => {
  // Clear any existing errors
  authStore.clearError();
});
</script> 