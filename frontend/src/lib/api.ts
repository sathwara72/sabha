const API_BASE_URL = "http://localhost:8000/api";

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

// Admin API calls
export async function fetchAllBusinessesAdmin() {
  const response = await fetch(`${API_BASE_URL}/admin/businesses`);
  if (!response.ok) throw new Error("Failed to fetch all businesses for admin");
  return response.json();
}

export async function approveBusiness(id: number) {
  const response = await fetch(`${API_BASE_URL}/admin/businesses/${id}/approve`, {
    method: "POST",
  });
  if (!response.ok) throw new Error("Failed to approve business");
  return response.json();
}

export async function rejectBusiness(id: number) {
  const response = await fetch(`${API_BASE_URL}/admin/businesses/${id}/reject`, {
    method: "POST",
  });
  if (!response.ok) throw new Error("Failed to reject business");
  return response.json();
}

export async function createEventAdmin(eventData: any) {
  const response = await fetch(`${API_BASE_URL}/admin/events`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(eventData),
  });
  if (!response.ok) throw new Error("Failed to create event");
  return response.json();
}

export async function fetchUsersAdmin() {
  const response = await fetch(`${API_BASE_URL}/admin/users`);
  if (!response.ok) throw new Error("Failed to fetch users");
  return response.json();
}
