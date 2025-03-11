const browserSync = require('browser-sync').create();

browserSync.init({
  proxy: "localhost/mabisa2.0", // Replace with your XAMPP project URL
  files: [
    "js/**/*.js", // Watch JS files
    "css/**/*.css", // Watch CSS files
    "**/*.php" // Watch PHP files
  ],
  notify: true, // Display notifications in the browser
  open: true, // Automatically open in the browser
});

// Keep the script running
browserSync.watch("**/*").on("change", browserSync.reload);
