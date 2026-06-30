"use client";

import React, { createContext, useContext, useState, useEffect } from "react";
import en from "@/locales/en.json";
import gu from "@/locales/gu.json";

type Language = "en" | "gu";

interface LanguageContextProps {
  language: Language;
  setLanguage: (lang: Language) => void;
  t: (key: string) => string;
}

const LanguageContext = createContext<LanguageContextProps | undefined>(undefined);

const translations: Record<Language, any> = { en, gu };

export function LanguageProvider({ children }: { children: React.ReactNode }) {
  const [language, setLanguageState] = useState<Language>("en");
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    if (typeof window !== "undefined") {
      const saved = localStorage.getItem("sabha_lang") as Language;
      if (saved === "en" || saved === "gu") {
        setLanguageState(saved);
      }
    }
    setMounted(true);
  }, []);

  const setLanguage = (lang: Language) => {
    setLanguageState(lang);
    if (typeof window !== "undefined") {
      localStorage.setItem("sabha_lang", lang);
      document.documentElement.lang = lang;
    }
  };

  const t = (key: string): string => {
    const keys = key.split(".");
    let result = translations[language];
    
    for (const k of keys) {
      if (result && result[k] !== undefined) {
        result = result[k];
      } else {
        let engFallback = translations["en"];
        for (const fallbackK of keys) {
          if (engFallback && engFallback[fallbackK] !== undefined) {
            engFallback = engFallback[fallbackK];
          } else {
            return key;
          }
        }
        return typeof engFallback === "string" ? engFallback : key;
      }
    }

    return typeof result === "string" ? result : key;
  };

  return (
    <LanguageContext.Provider value={{ language, setLanguage, t }}>
      {children}
    </LanguageContext.Provider>
  );
}

export function useLanguage() {
  const context = useContext(LanguageContext);
  if (!context) {
    throw new Error("useLanguage must be used within a LanguageProvider");
  }
  return context;
}
