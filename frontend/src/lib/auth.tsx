"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useState,
  ReactNode,
} from "react";
import LoginModal from "@/components/shared/LoginModal";
import RegisterModal from "@/components/shared/RegisterModal";
import { API_BASE_URL } from "@/lib/config";

function formatDateForInput(dateStr?: string | null): string {
  if (!dateStr) return "";
  const str = String(dateStr).trim();
  if (!str || str === "null" || str === "undefined") return "";
  const match = str.match(/^(\d{4}-\d{2}-\d{2})/);
  if (match) return match[1];
  return "";
}

interface UserProfile {
  name: string;
  email: string;
  role: string;
  phone?: string;
  city?: string;
  native_city?: string;
  birth_date?: string;
  anniversary_date?: string;
  residence_address?: string;
  avatar?: string;
}

interface AuthContextValue {
  isAuthenticated: boolean;
  isReady: boolean;
  user: UserProfile | null;
  isLoginOpen: boolean;
  isRegisterOpen: boolean;
  openLogin: () => void;
  closeLogin: () => void;
  openRegister: () => void;
  closeRegister: () => void;
  login: (email: string, password: string) => Promise<void>;
  demoLogin: () => Promise<void>;
  register: (name: string, email: string, password: string) => Promise<void>;
  registerSendOtp: (name: string, email: string, password: string) => Promise<{ message: string; email: string }>;
  registerConfirm: (email: string, otp: string) => Promise<void>;
  logout: () => void;
  updateLocalUser: (newProfile: UserProfile) => void;
}

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [user, setUser] = useState<UserProfile | null>(null);
  const [isReady, setIsReady] = useState(false);
  const [isLoginOpen, setIsLoginOpen] = useState(false);
  const [isRegisterOpen, setIsRegisterOpen] = useState(false);

  useEffect(() => {
    try {
      const token = localStorage.getItem("sabha_token");
      const savedUser = localStorage.getItem("sabha_user");
      if (token && savedUser) {
        setUser(JSON.parse(savedUser));
        setIsAuthenticated(true);
      }
    } catch (e) {
      console.error("Failed to parse saved user in AuthProvider:", e);
    }
    setIsReady(true);
  }, []);

  const updateLocalUser = useCallback((updates: Partial<UserProfile>) => {
    setUser((prev) => {
      if (!prev) return null;
      const updated = {
        ...prev,
        ...updates,
        birth_date: updates.birth_date !== undefined ? formatDateForInput(updates.birth_date) : prev.birth_date,
        anniversary_date: updates.anniversary_date !== undefined ? formatDateForInput(updates.anniversary_date) : prev.anniversary_date,
      };
      try {
        localStorage.setItem("sabha_user", JSON.stringify(updated));
      } catch {
        /* ignore */
      }
      return updated;
    });
  }, []);

  const login = useCallback(async (email: string, password: string) => {
    const response = await fetch(`${API_BASE_URL}/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email, password }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || "Invalid credentials");
    }

    const profile: UserProfile = {
      name: data.user.name,
      email: data.user.email,
      role: data.user.role || "user",
      phone: data.user.phone || "",
      city: data.user.city || "",
      native_city: data.user.native_city || "",
      birth_date: formatDateForInput(data.user.birth_date),
      anniversary_date: formatDateForInput(data.user.anniversary_date),
      residence_address: data.user.residence_address || "",
      avatar: data.user.avatar || "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop",
    };

    localStorage.setItem("sabha_token", data.token);
    localStorage.setItem("sabha_user", JSON.stringify(profile));
    localStorage.setItem("sabha_auth", "1");

    setUser(profile);
    setIsAuthenticated(true);
    setIsLoginOpen(false);
    setIsRegisterOpen(false);
  }, []);

  const demoLogin = useCallback(async () => {
    const response = await fetch(`${API_BASE_URL}/demo-login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || "Demo login failed");
    }

    const profile: UserProfile = {
      name: data.user.name,
      email: data.user.email,
      role: data.user.role || "user",
      phone: data.user.phone || "",
      city: data.user.city || "",
      native_city: data.user.native_city || "",
      birth_date: formatDateForInput(data.user.birth_date),
      anniversary_date: formatDateForInput(data.user.anniversary_date),
      residence_address: data.user.residence_address || "",
      avatar: data.user.avatar || "https://images.unsplash.com/photo-1527980965255-d3b416303d12?w=150&h=150&fit=crop",
    };

    localStorage.setItem("sabha_token", data.token);
    localStorage.setItem("sabha_user", JSON.stringify(profile));
    localStorage.setItem("sabha_auth", "1");

    setUser(profile);
    setIsAuthenticated(true);
    setIsLoginOpen(false);
    setIsRegisterOpen(false);
  }, []);

  const register = useCallback(async (name: string, email: string, password: string) => {
    const response = await fetch(`${API_BASE_URL}/register`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ name, email, password }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || "Registration failed");
    }

    const profile: UserProfile = {
      name: data.user.name,
      email: data.user.email,
      role: data.user.role || "user",
      phone: data.user.phone || "",
      city: data.user.city || "",
      native_city: data.user.native_city || "",
      birth_date: formatDateForInput(data.user.birth_date),
      anniversary_date: formatDateForInput(data.user.anniversary_date),
      residence_address: data.user.residence_address || "",
      avatar: data.user.avatar || "https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&h=150&fit=crop",
    };

    localStorage.setItem("sabha_token", data.token);
    localStorage.setItem("sabha_user", JSON.stringify(profile));
    localStorage.setItem("sabha_auth", "1");

    setUser(profile);
    setIsAuthenticated(true);
    setIsLoginOpen(false);
    setIsRegisterOpen(false);
  }, []);

  const registerSendOtp = useCallback(async (name: string, email: string, password: string) => {
    let response: Response;
    try {
      response = await fetch(`${API_BASE_URL}/register/send-otp`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
        },
        body: JSON.stringify({ name, email, password }),
      });
    } catch {
      throw new Error("Cannot connect to server. Please make sure the backend is running.");
    }

    let data: any = {};
    try {
      data = await response.json();
    } catch {
      throw new Error("Unexpected server response. Please try again.");
    }

    if (!response.ok) {
      if (data.errors) {
        const firstField = Object.keys(data.errors)[0];
        const firstMsg = data.errors[firstField]?.[0];
        throw new Error(firstMsg || data.message || "Validation failed.");
      }
      throw new Error(data.message || "Failed to send verification code.");
    }

    return { message: data.message, email: data.email };
  }, []);

  const registerConfirm = useCallback(async (email: string, otp: string) => {
    const response = await fetch(`${API_BASE_URL}/register/confirm`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email, otp }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || "Invalid or expired OTP");
    }

    const profile: UserProfile = {
      name: data.user.name,
      email: data.user.email,
      role: data.user.role || "user",
      phone: data.user.phone || "",
      city: data.user.city || "",
      native_city: data.user.native_city || "",
      birth_date: formatDateForInput(data.user.birth_date),
      anniversary_date: formatDateForInput(data.user.anniversary_date),
      residence_address: data.user.residence_address || "",
      avatar: data.user.avatar || "https://images.unsplash.com/photo-1599566150163-29194dcaad36?w=150&h=150&fit=crop",
    };

    localStorage.setItem("sabha_token", data.token);
    localStorage.setItem("sabha_user", JSON.stringify(profile));
    localStorage.setItem("sabha_auth", "1");

    setUser(profile);
    setIsAuthenticated(true);
    setIsLoginOpen(false);
    setIsRegisterOpen(false);
  }, []);

  const logout = useCallback(() => {
    try {
      localStorage.removeItem("sabha_token");
      localStorage.removeItem("sabha_user");
      localStorage.removeItem("sabha_auth");
    } catch {
      /* ignore */
    }
    setUser(null);
    setIsAuthenticated(false);
  }, []);

  const openLogin = useCallback(() => {
    setIsLoginOpen(true);
    setIsRegisterOpen(false);
  }, []);
  const closeLogin = useCallback(() => setIsLoginOpen(false), []);

  const openRegister = useCallback(() => {
    setIsRegisterOpen(true);
    setIsLoginOpen(false);
  }, []);
  const closeRegister = useCallback(() => setIsRegisterOpen(false), []);

  return (
    <AuthContext.Provider
      value={{
        isAuthenticated,
        isReady,
        user,
        isLoginOpen,
        isRegisterOpen,
        openLogin,
        closeLogin,
        openRegister,
        closeRegister,
        login,
        demoLogin,
        register,
        registerSendOtp,
        registerConfirm,
        logout,
        updateLocalUser,
      }}
    >
      {children}
      <LoginModal />
      <RegisterModal />
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error("useAuth must be used within AuthProvider");
  return ctx;
}
