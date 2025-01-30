async function fileExists(url) {
  try {
    const response = await fetch(url, { method: "HEAD" });
    return response.ok; // true if status is 200-299
  } catch (error) {
    console.error("Error checking file:", error);
    return false;
  }
}
