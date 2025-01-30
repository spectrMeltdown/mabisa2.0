/**
 * @returns {bool}
 * @param {string | null} url
 */
async function fileExists(url) {
  if (!url) return false;
  try {
    const response = await fetch(url, { method: 'HEAD' });
    return response.ok; // true if status is 200-299
  } catch (error) {
    console.error('Error checking file:', error);
    return false;
  }
}
