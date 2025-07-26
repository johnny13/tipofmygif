<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />

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
            icon="logout"
            aria-label="Logout"
            @click="handleLogout"
            :loading="authStore.isLoading"
          />
          <q-btn
            v-else
            flat
            dense
            round
            icon="login"
            aria-label="Login"
            @click="$router.push('/login')"
          />
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <q-list>
        <q-item-label header> Essential Links </q-item-label>

        <EssentialLink v-for="link in linksList" :key="link.title" v-bind="link" />
      </q-list>
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
    title: 'Docs',
    caption: 'quasar.dev',
    icon: 'school',
    link: 'https://quasar.dev',
  },
  {
    title: 'Github',
    caption: 'github.com/quasarframework',
    icon: 'code',
    link: 'https://github.com/quasarframework',
  },
  {
    title: 'Discord Chat Channel',
    caption: 'chat.quasar.dev',
    icon: 'chat',
    link: 'https://chat.quasar.dev',
  },
  {
    title: 'Forum',
    caption: 'forum.quasar.dev',
    icon: 'record_voice_over',
    link: 'https://forum.quasar.dev',
  },
  {
    title: 'Twitter',
    caption: '@quasarframework',
    icon: 'rss_feed',
    link: 'https://twitter.quasar.dev',
  },
  {
    title: 'Facebook',
    caption: '@QuasarFramework',
    icon: 'public',
    link: 'https://facebook.quasar.dev',
  },
  {
    title: 'Quasar Awesome',
    caption: 'Community Quasar projects',
    icon: 'favorite',
    link: 'https://awesome.quasar.dev',
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
