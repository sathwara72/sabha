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

const API_BASE_URL = "http://localhost:8000/api";

interface UserProfile {
  name: string;
  email: string;
  role: string;
  phone?: string;
  city?: string;
  designation?: string;
  company?: string;
  bio?: string;
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
  register: (name: string, email: string, password: string) => Promise<void>;
  registerSendOtp: (name: string, email: string, password: string) => Promise<{ otp: string }>;
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

  const updateLocalUser = useCallback((newProfile: UserProfile) => {
    localStorage.setItem("sabha_user", JSON.stringify(newProfile));
    setUser(newProfile);
  }, []);

  const login = useCallback(async (email: string, password: string) => {
    const response = await fetch(`${API_BASE_URL}/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });

    if (!response.ok) {
      const errData = await response.json().catch(() => ({}));
      throw new Error(errData.message || "Invalid email or password");
    }

    const data = await response.json();
    const token = data.token;
    const profile: UserProfile = {
      name: data.user.name,
      email: data.user.email,
      role: data.user.role,
      phone: data.user.phone || "",
      city: data.user.city || "",
      designation: data.user.designation || "",
      company: data.user.company || "",
      bio: data.user.bio || "",
      avatar: data.user.avatar || "",
    };

    localStorage.setItem("sabha_token", token);
    localStorage.setItem("sabha_user", JSON.stringify(profile));
    localStorage.setItem("sabha_auth", "1");

    setUser(profile);
    setIsAuthenticated(true);
    setIsLoginOpen(false);
    setIsRegisterOpen(false);
  }, []);

  // Backwards compatibility register helper (falls back to sending OTP and confirming automatically if wanted, 
  // but we will use the two step send and confirm functions in UI)
  const register = useCallback(async (name: string, email: string, password: string) => {
    const response = await fetch(`${API_BASE_URL}/register`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, email, password }),
    });

    if (!response.ok) {
      const errData = await response.json().catch(() => ({}));
      throw new Error(errData.message || "Registration failed. Try again.");
    }

    const data = await response.json();
    const token = data.token;
    const profile: UserProfile = {
      name: data.user.name,
      email: data.user.email,
      role: data.user.role,
    };

    localStorage.setItem("sabha_token", token);
    localStorage.setItem("sabha_user", JSON.stringify(profile));
    localStorage.setItem("sabha_auth", "1");

    setUser(profile);
    setIsAuthenticated(true);
    setIsLoginOpen(false);
    setIsRegisterOpen(false);
  }, []);

  const registerSendOtp = useCallback(async (name: string, email: string, password: string) => {
    const response = await fetch(`${API_BASE_URL}/register/send-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, email, password }),
    });

    if (!response.ok) {
      const errData = await response.json().catch(() => ({}));
      throw new Error(errData.message || "Registration validation failed.");
    }

    return response.json(); // returns { message, email, otp }
  }, []);

  const registerConfirm = useCallback(async (email: string, otp: string) => {
    const response = await fetch(`${API_BASE_URL}/register/confirm`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, otp }),
    });

    if (!response.ok) {
      const errData = await response.json().catch(() => ({}));
      throw new Error(errData.message || "Invalid OTP verification code.");
    }

    const data = await response.json();
    const token = data.token;
    const profile: UserProfile = {
      name: data.user.name,
      email: data.user.email,
      role: data.user.role,
      phone: data.user.phone || "",
      city: data.user.city || "",
      designation: data.user.designation || "",
      company: data.user.company || "",
      bio: data.user.bio || "",
      avatar: data.user.avatar || "",
    };

    localStorage.setItem("sabha_token", token);
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
