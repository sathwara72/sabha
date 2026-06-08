const API_BASE_URL = "http://localhost:8000/api";

function getHeaders(contentType: string | null = "application/json") {
  const headers: Record<string, string> = {};
  if (contentType) {
    headers["Content-Type"] = contentType;
  }
  if (typeof window !== "undefined") {
    const token = localStorage.getItem("sabha_token");
    if (token) {
      headers["Authorization"] = `Bearer ${token}`;
    }
  }
  return headers;
}

export async function fetchBusinesses() {
  const response = await fetch(`${API_BASE_URL}/businesses`);
  if (!response.ok) throw new Error("Failed to fetch businesses");
  return response.json();
}

export async function fetchEvents() {
  const response = await fetch(`${API_BASE_URL}/events`);
  if (!response.ok) throw new Error("Failed to fetch events");
  return response.json();
}

export async function fetchStatistics() {
  const response = await fetch(`${API_BASE_URL}/statistics`);
  if (!response.ok) throw new Error("Failed to fetch statistics");
  return response.json();
}

export async function fetchGallery() {
  const response = await fetch(`${API_BASE_URL}/gallery`);
  if (!response.ok) throw new Error("Failed to fetch gallery");
  return response.json();
}

export async function submitBusiness(formData: FormData) {
  const response = await fetch(`${API_BASE_URL}/businesses`, {
    method: "POST",
    headers: getHeaders(null), // Multipart upload
    body: formData,
  });
  if (!response.ok) {
    const errData = await response.json().catch(() => ({}));
    throw new Error(errData.message || "Failed to submit business");
  }
  return response.json();
}

export async function forgotPassword(email: string) {
  const response = await fetch(`${API_BASE_URL}/forgot-password`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email }),
  });
  if (!response.ok) {
    const errData = await response.json().catch(() => ({}));
    throw new Error(errData.message || "Failed to process request");
  }
  return response.json();
}

export async function registerSendOtp(data: any) {
  const response = await fetch(`${API_BASE_URL}/register/send-otp`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
  if (!response.ok) {
    const err = await response.json().catch(() => ({}));
    throw new Error(err.message || "Registration failed");
  }
  return response.json();
}

export async function registerConfirm(email: string, otp: string) {
  const response = await fetch(`${API_BASE_URL}/register/confirm`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, otp }),
  });
  if (!response.ok) {
    const err = await response.json().catch(() => ({}));
    throw new Error(err.message || "Invalid OTP verification code");
  }
  return response.json();
}

export async function updateProfile(data: FormData) {
  const response = await fetch(`${API_BASE_URL}/user/profile`, {
    method: "POST",
    headers: getHeaders(null),
    body: data,
  });
  if (!response.ok) {
    const err = await response.json().catch(() => ({}));
    throw new Error(err.message || "Failed to update profile");
  }
  return response.json();
}

export async function getUserBusiness() {
  const response = await fetch(`${API_BASE_URL}/user/business`, {
    headers: getHeaders(),
  });
  if (!response.ok) throw new Error("Failed to fetch user business");
  return response.json();
}

// Admin API calls
export async function fetchAllBusinessesAdmin() {
  const response = await fetch(`${API_BASE_URL}/admin/businesses`, {
    headers: getHeaders(),
  });
  if (!response.ok) throw new Error("Failed to fetch all businesses for admin");
  return response.json();
}

export async function approveBusiness(id: number) {
  const response = await fetch(`${API_BASE_URL}/admin/businesses/${id}/approve`, {
    method: "POST",
    headers: getHeaders(),
  });
  if (!response.ok) throw new Error("Failed to approve business");
  return response.json();
}

export async function rejectBusiness(id: number, rejectionReason: string) {
  const response = await fetch(`${API_BASE_URL}/admin/businesses/${id}/reject`, {
    method: "POST",
    headers: getHeaders(),
    body: JSON.stringify({ rejection_reason: rejectionReason }),
  });
  if (!response.ok) throw new Error("Failed to reject business");
  return response.json();
}

export async function createEventAdmin(eventData: any) {
  const response = await fetch(`${API_BASE_URL}/admin/events`, {
    method: "POST",
    headers: getHeaders(),
    body: JSON.stringify(eventData),
  });
  if (!response.ok) throw new Error("Failed to create event");
  return response.json();
}

export async function fetchUsersAdmin() {
  const response = await fetch(`${API_BASE_URL}/admin/users`, {
    headers: getHeaders(),
  });
  if (!response.ok) throw new Error("Failed to fetch users");
  return response.json();
}

export async function uploadGalleryImage(formData: FormData) {
  const response = await fetch(`${API_BASE_URL}/admin/gallery/upload`, {
    method: "POST",
    headers: getHeaders(null),
    body: formData,
  });
  if (!response.ok) {
    const errData = await response.json().catch(() => ({}));
    throw new Error(errData.message || "Failed to upload gallery image");
  }
  return response.json();
}

export async function updateStatistic(id: number, statData: { value: string; label: string }) {
  const response = await fetch(`${API_BASE_URL}/admin/statistics/${id}`, {
    method: "POST",
    headers: getHeaders(),
    body: JSON.stringify(statData),
  });
  if (!response.ok) throw new Error("Failed to update statistic");
  return response.json();
}

export async function fetchSettings() {
  const response = await fetch(`${API_BASE_URL}/settings`);
  if (!response.ok) throw new Error("Failed to fetch settings");
  return response.json();
}

export async function updateSettingsAdmin(settingsData: Record<string, any>) {
  const response = await fetch(`${API_BASE_URL}/admin/settings`, {
    method: "POST",
    headers: getHeaders(),
    body: JSON.stringify({ settings: settingsData }),
  });
  if (!response.ok) {
    const errData = await response.json().catch(() => ({}));
    throw new Error(errData.message || "Failed to update settings");
  }
  return response.json();
}

export async function fetchReviews(businessId: number) {
  const response = await fetch(`${API_BASE_URL}/businesses/${businessId}/reviews`);
  if (!response.ok) throw new Error("Failed to fetch reviews");
  return response.json();
}

export async function submitReview(businessId: number, reviewData: { content: string; rating: number }) {
  const response = await fetch(`${API_BASE_URL}/businesses/${businessId}/reviews`, {
    method: "POST",
    headers: getHeaders(),
    body: JSON.stringify(reviewData),
  });
  if (!response.ok) {
    const errData = await response.json().catch(() => ({}));
    throw new Error(errData.message || "Failed to submit review");
  }
  return response.json();
}

