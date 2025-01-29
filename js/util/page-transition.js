// page transition
// TODO: change this to use the 'pageshow' and 'pagehide' events instead

// document.addEventListener("click", async (e) => {
//   const link = e.target.closest('a[data-transition="view"]');
//   if (!link) return;
//   e.preventDefault();

//   if (!document.startViewTransition) {
//     console.log("View page transition not supported.");
//     location.href = link.href;
//     return;
//   }
//   document.startViewTransition(() => {
//     document.body.classList.add("fade");
//     new Promise((res) => {
//       setTimeout(res, 300);
//     }).then(() => {
//       location.href = link.href;
//       document.body.classList.remove("fade");
//     });
//   });
// });
