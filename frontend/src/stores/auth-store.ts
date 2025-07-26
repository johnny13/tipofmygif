import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { api } from 'src/boot/axios';

export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string;
  created_at: string;
  updated_at: string;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterCredentials {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(localStorage.getItem('auth_token'));
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  // Computed properties
  const isAuthenticated = computed(() => !!token.value && !!user.value);
  const isGuest = computed(() => !isAuthenticated.value);

  // Initialize auth state
  const initializeAuth = async () => {
    if (token.value) {
      try {
        // Set default authorization header
        api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
        
        const response = await api.get('/api/user');
        user.value = response.data;
      } catch {
        // Token is invalid, clear it
        void logout();
      }
    }
  };

  // Login
  const login = async (credentials: LoginCredentials) => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await api.post('/api/login', credentials);
      const { token: authToken, user: userData } = response.data;

      token.value = authToken;
      user.value = userData;
      localStorage.setItem('auth_token', authToken);

      // Set default authorization header for future requests
      api.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;

      return { success: true };
    } catch (err: unknown) {
      const errorMessage = err && typeof err === 'object' && 'response' in err 
        ? (err.response as { data?: { message?: string } })?.data?.message || 'Login failed'
        : 'Login failed';
      error.value = errorMessage;
      return { success: false, error: error.value };
    } finally {
      isLoading.value = false;
    }
  };

  // Register
  const register = async (credentials: RegisterCredentials) => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await api.post('/api/register', credentials);
      const { token: authToken, user: userData } = response.data;

      token.value = authToken;
      user.value = userData;
      localStorage.setItem('auth_token', authToken);

      // Set default authorization header for future requests
      api.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;

      return { success: true };
    } catch (err: unknown) {
      const errorMessage = err && typeof err === 'object' && 'response' in err 
        ? (err.response as { data?: { message?: string } })?.data?.message || 'Registration failed'
        : 'Registration failed';
      error.value = errorMessage;
      return { success: false, error: error.value };
    } finally {
      isLoading.value = false;
    }
  };

  // Logout
  const logout = async () => {
    isLoading.value = true;

    try {
      if (token.value) {
        await api.post('/api/logout', {}, {
          headers: {
            Authorization: `Bearer ${token.value}`,
          },
        });
      }
    } catch (err) {
      // Even if logout fails on server, clear local state
      console.error('Logout error:', err);
    } finally {
      // Clear local state
      token.value = null;
      user.value = null;
      localStorage.removeItem('auth_token');
      delete api.defaults.headers.common['Authorization'];
      isLoading.value = false;
    }
  };

  // Clear error
  const clearError = () => {
    error.value = null;
  };

  return {
    // State
    user,
    token,
    isLoading,
    error,

    // Computed
    isAuthenticated,
    isGuest,

    // Actions
    initializeAuth,
    login,
    register,
    logout,
    clearError,
  };
}); 