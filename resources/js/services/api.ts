import axios, { AxiosInstance, AxiosResponse, AxiosError, InternalAxiosRequestConfig } from 'axios';

// API Configuration
const API_CONFIG = {
  baseURL: '/api/v1',
  timeout: 30000, // 30 seconds
  retryAttempts: 3,
  retryDelay: 1000, // 1 second base delay
};

// Request tracking for abort functionality
const pendingRequests = new Map<string, AbortController>();

// Sleep utility for retry delay
const sleep = (ms: number) => new Promise(resolve => setTimeout(resolve, ms));

// Check if error is retryable
const isRetryableError = (error: AxiosError): boolean => {
  if (!error.response) return true; // Network error
  const status = error.response.status;
  return status === 408 || status === 429 || status >= 500;
};

class ApiService {
  private api: AxiosInstance;
  private isDev: boolean;

  constructor() {
    this.isDev = import.meta.env.DEV;

    this.api = axios.create({
      baseURL: API_CONFIG.baseURL,
      timeout: API_CONFIG.timeout,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    this.setupRequestInterceptor();
    this.setupResponseInterceptor();
  }

  private setupRequestInterceptor() {
    this.api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
      // Add auth token
      const token = localStorage.getItem('auth_token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }

      // Create abort controller for this request
      const requestKey = `${config.method}-${config.url}`;
      const controller = new AbortController();
      config.signal = controller.signal;
      pendingRequests.set(requestKey, controller);

      // Development logging
      if (this.isDev) {
        console.log(`[API] ${config.method?.toUpperCase()} ${config.url}`, {
          params: config.params,
          hasToken: !!token,
        });
      }

      return config;
    });
  }

  private setupResponseInterceptor() {
    this.api.interceptors.response.use(
      (response) => {
        // Clean up pending request
        const requestKey = `${response.config.method}-${response.config.url}`;
        pendingRequests.delete(requestKey);

        // Development logging
        if (this.isDev) {
          console.log(`[API] ✓ ${response.config.url}`, {
            status: response.status,
            dataSize: JSON.stringify(response.data).length,
          });
        }

        return response;
      },
      async (error: AxiosError) => {
        const config = error.config as InternalAxiosRequestConfig & { _retryCount?: number };

        // Clean up pending request
        if (config) {
          const requestKey = `${config.method}-${config.url}`;
          pendingRequests.delete(requestKey);
        }

        // Handle 401 - Unauthorized
        if (error.response?.status === 401) {
          const url = config?.url || '';
          const isAuthEndpoint = url.includes('/login') || url.includes('/register');

          if (!isAuthEndpoint) {
            const token = localStorage.getItem('auth_token');
            const isLoginPage = window.location.pathname === '/login';

            if (token && !isLoginPage) {
              localStorage.removeItem('auth_token');
              window.location.href = '/login';
            }
          }
          return Promise.reject(error);
        }

        // Retry logic for retryable errors
        if (config && isRetryableError(error)) {
          config._retryCount = config._retryCount || 0;

          if (config._retryCount < API_CONFIG.retryAttempts) {
            config._retryCount++;
            const delay = API_CONFIG.retryDelay * Math.pow(2, config._retryCount - 1);

            if (this.isDev) {
              console.log(`[API] Retry ${config._retryCount}/${API_CONFIG.retryAttempts} for ${config.url} in ${delay}ms`);
            }

            await sleep(delay);
            return this.api.request(config);
          }
        }

        // Error logging
        if (this.isDev) {
          console.error(`[API] ✗ ${config?.url}`, {
            status: error.response?.status,
            message: error.message,
            data: error.response?.data,
          });
        }

        return Promise.reject(error);
      }
    );
  }

  // Cancel all pending requests
  cancelAll() {
    pendingRequests.forEach((controller, key) => {
      controller.abort();
      pendingRequests.delete(key);
    });
  }

  // Cancel specific request
  cancel(method: string, url: string) {
    const key = `${method}-${url}`;
    const controller = pendingRequests.get(key);
    if (controller) {
      controller.abort();
      pendingRequests.delete(key);
    }
  }

  async get<T>(url: string, params?: any): Promise<T> {
    const response: AxiosResponse<T> = await this.api.get(url, { params });
    return response.data;
  }

  async post<T>(url: string, data?: any): Promise<T> {
    const response: AxiosResponse<T> = await this.api.post(url, data);
    return response.data;
  }

  async put<T>(url: string, data?: any): Promise<T> {
    const response: AxiosResponse<T> = await this.api.put(url, data);
    return response.data;
  }

  async patch<T>(url: string, data?: any): Promise<T> {
    const response: AxiosResponse<T> = await this.api.patch(url, data);
    return response.data;
  }

  async delete<T>(url: string): Promise<T> {
    const response: AxiosResponse<T> = await this.api.delete(url);
    return response.data;
  }

  // Upload file with progress callback
  async upload<T>(url: string, formData: FormData, onProgress?: (progress: number) => void): Promise<T> {
    const response: AxiosResponse<T> = await this.api.post(url, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: (event) => {
        if (onProgress && event.total) {
          onProgress(Math.round((event.loaded * 100) / event.total));
        }
      },
    });
    return response.data;
  }
}

export const api = new ApiService();
export default api;
