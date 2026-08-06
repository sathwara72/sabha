"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/lib/auth";

export default function LoginPage() {
  const router = useRouter();
  const { openLogin } = useAuth();

  useEffect(() => {
    router.replace("/");
    openLogin();
  }, [router, openLogin]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-background">
      <div className="flex flex-col items-center gap-3">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        <p className="text-xs font-medium text-muted">Opening login...</p>
      </div>
    </div>
  );
}
