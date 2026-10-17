const menuToggle = document.querySelector(".menu-toggle");
const siteNav = document.querySelector(".site-nav");

menuToggle.addEventListener("click", () => {
  const isOpen = menuToggle.getAttribute("aria-expanded") === "true";
  menuToggle.setAttribute("aria-expanded", String(!isOpen));
  menuToggle.setAttribute("aria-label", isOpen ? "Abrir menú" : "Cerrar menú");
  siteNav.classList.toggle("is-open", !isOpen);
});

siteNav.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    menuToggle.setAttribute("aria-expanded", "false");
    menuToggle.setAttribute("aria-label", "Abrir menú");
    siteNav.classList.remove("is-open");
  });
});

const slides = [...document.querySelectorAll(".space-slide")];
const currentSlide = document.querySelector("#slide-current");
let activeSlide = 0;

function showSlide(index) {
  activeSlide = (index + slides.length) % slides.length;
  if (window.matchMedia("(max-width: 760px)").matches) {
    slides[activeSlide].scrollIntoView({
      behavior: "smooth",
      block: "nearest",
      inline: "center",
    });
  }
  currentSlide.textContent = String(activeSlide + 1).padStart(2, "0");
}

document.querySelectorAll(".carousel-arrow").forEach((button) => {
  button.addEventListener("click", () =>
    showSlide(activeSlide + (button.dataset.direction === "next" ? 1 : -1)),
  );
});

const revealObserver = new IntersectionObserver(
  (entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.12 },
);

document
  .querySelectorAll(".reveal")
  .forEach((element) => revealObserver.observe(element));

const reservationDate = document.querySelector('[name="reservation_datetime"]');
const localDate = new Date(Date.now() - new Date().getTimezoneOffset() * 60000);
reservationDate.min = localDate.toISOString().slice(0, 16);
