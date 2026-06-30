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
  demoLogin: () => Promise<void>;
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
    await new Promise((r) => setTimeout(r, 1000));
    
    const profile: UserProfile = {
      name: "Demo User",
      email: email,
      role: email.includes("admin") ? "admin" : "user",
      phone: "",
      city: "",
      designation: "",
      company: "",
      bio: "",
      avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop",
    };

    localStorage.setItem("sabha_token", "dummy-token");
    localStorage.setItem("sabha_user", JSON.stringify(profile));
    localStorage.setItem("sabha_auth", "1");

    setUser(profile);
    setIsAuthenticated(true);
    setIsLoginOpen(false);
    setIsRegisterOpen(false);
  }, []);

  const demoLogin = useCallback(async () => {
    await new Promise((r) => setTimeout(r, 1000));
    
    const profile: UserProfile = {
      name: "Demo Guest",
      email: "demo@example.com",
      role: "user",
      phone: "",
      city: "",
      designation: "",
      company: "",
      bio: "",
      avatar: "https://images.unsplash.com/photo-1527980965255-d3b416303d12?w=150&h=150&fit=crop",
    };

    localStorage.setItem("sabha_token", "dummy-token");
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
    await new Promise((r) => setTimeout(r, 1000));
    
    const profile: UserProfile = {
      name: name,
      email: email,
      role: "user",
      avatar: "https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&h=150&fit=crop",
    };

    localStorage.setItem("sabha_token", "dummy-token");
    localStorage.setItem("sabha_user", JSON.stringify(profile));
    localStorage.setItem("sabha_auth", "1");

    setUser(profile);
    setIsAuthenticated(true);
    setIsLoginOpen(false);
    setIsRegisterOpen(false);
  }, []);

  const registerSendOtp = useCallback(async (name: string, email: string, password: string) => {
    await new Promise((r) => setTimeout(r, 1000));
    return { message: "OTP sent successfully", email, otp: "1234" };
  }, []);

  const registerConfirm = useCallback(async (email: string, otp: string) => {
    await new Promise((r) => setTimeout(r, 1000));
    
    if (otp !== "1234") {
      throw new Error("Invalid OTP verification code.");
    }

    const profile: UserProfile = {
      name: "New User",
      email: email,
      role: "user",
      phone: "",
      city: "",
      designation: "",
      company: "",
      bio: "",
      avatar: "https://images.unsplash.com/photo-1599566150163-29194dcaad36?w=150&h=150&fit=crop",
    };

    localStorage.setItem("sabha_token", "dummy-token");
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
