<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
                  <q-btn flat dense round icon="fas fa-bars" aria-label="Menu" @click="toggleLeftDrawer" />

        <q-toolbar-title> Tip of My GIF </q-toolbar-title>

        <div class="row items-center q-gutter-sm">
          <div v-if="authStore.isAuthenticated" class="text-body2">
            Welcome, {{ authStore.user?.name }}
          </div>
          <q-btn
            v-if="authStore.isAuthenticated"
            flat
            dense
            round
            icon="fas fa-sign-out-alt"
            aria-label="Logout"
            @click="handleLogout"
            :loading="authStore.isLoading"
          />
          <q-btn
            v-else
            flat
            dense
            round
            icon="fas fa-sign-in-alt"
            aria-label="Login"
            @click="$router.push('/login')"
          />
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <div class="q-pa-md">
        <q-list bordered>
          <q-item-label header class="bg-black"> Application Menu </q-item-label>

          <q-item clickable v-ripple  to="/search">
            <q-item-section>SEARCH GIPHY</q-item-section>
            <q-item-section avatar>
              <q-icon color="primary" name="fas fa-search" />
            </q-item-section>
          </q-item>
          <q-separator />
          <q-item clickable v-ripple  to="/saved">
            <q-item-section>VIEW SAVED</q-item-section>
            <q-item-section avatar>
              <q-icon color="primary" name="fas fa-star" />
            </q-item-section>
          </q-item>
        </q-list>
      </div>
      <div class="q-pa-md q-mt-md">
        <q-list>
          <q-item-label header> External Links </q-item-label>

          <EssentialLink v-for="link in linksList" :key="link.title" v-bind="link" />
        </q-list>
      </div>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import EssentialLink, { type EssentialLinkProps } from 'components/EssentialLink.vue';
import { useAuthStore } from 'src/stores/auth-store';

const linksList: EssentialLinkProps[] = [
  {
    title: 'API Docs',
    caption: 'Swagger UI',
    icon: 'fas fa-graduation-cap',
    link: 'http://localhost:8000/api/documentation',
  },
  {
    title: 'Github Repo',
    caption: 'github.com/johnny13/tipofmygif',
    icon: 'fab fa-github',
    link: 'https://github.com/johnny13/tipofmygif',
  },
];

const router = useRouter();
const $q = useQuasar();
const authStore = useAuthStore();

const leftDrawerOpen = ref(false);

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value;
}

const handleLogout = async () => {
  await authStore.logout();
  $q.notify({
    type: 'positive',
    message: 'Logged out successfully!',
  });
  await router.push('/login');
};

onMounted(() => {
  void authStore.initializeAuth();
});
</script>
