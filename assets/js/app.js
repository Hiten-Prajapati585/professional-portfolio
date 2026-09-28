document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".navbar .nav-link").forEach((a) =>
    a.addEventListener("click", () => {
      const nav = document.querySelector(".navbar-collapse");
      if (nav?.classList.contains("show"))
        bootstrap.Collapse.getOrCreateInstance(nav).hide();
    }),
  );
});
