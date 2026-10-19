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

const reviewForm = document.querySelector("#review-form");
const reviewGrid = document.querySelector(".review-grid");
const reviewStatus = document.querySelector("#review-status");
const reviewStorageKey = "corte-imperial-reviews";

function isReview(value) {
  return (
    value !== null &&
    typeof value === "object" &&
    typeof value.name === "string" &&
    typeof value.comment === "string" &&
    Number.isInteger(value.rating) &&
    value.rating >= 1 &&
    value.rating <= 5
  );
}

function readSavedReviews() {
  const saved = localStorage.getItem(reviewStorageKey);
  if (saved === null) return [];

  const reviews = JSON.parse(saved);
  if (!Array.isArray(reviews)) {
    throw new Error("El formato guardado de las opiniones no es válido.");
  }

  return reviews.filter(isReview);
}

function createReviewCard(review) {
  const card = document.createElement("figure");
  card.className = "review-card";

  const stars = document.createElement("div");
  stars.className = "stars";
  stars.setAttribute("aria-label", `${review.rating} de 5 estrellas`);
  stars.textContent = "★".repeat(review.rating) + "☆".repeat(5 - review.rating);

  const comment = document.createElement("blockquote");
  comment.textContent = `“${review.comment}”`;

  const caption = document.createElement("figcaption");
  const avatar = document.createElement("span");
  avatar.className = "review-avatar";
  avatar.textContent = review.name
    .trim()
    .split(/\s+/)
    .slice(0, 2)
    .map((part) => part[0])
    .join("")
    .toLocaleUpperCase("es");

  const details = document.createElement("span");
  const name = document.createElement("b");
  name.textContent = review.name;
  const source = document.createElement("small");
  source.textContent = "Opinión de un visitante";

  details.append(name, source);
  caption.append(avatar, details);
  card.append(stars, comment, caption);

  return card;
}

try {
  readSavedReviews()
    .reverse()
    .forEach((review) => reviewGrid.prepend(createReviewCard(review)));
} catch {
  reviewStatus.textContent =
    "No pudimos recuperar las opiniones guardadas en este dispositivo.";
}

reviewForm.addEventListener("submit", (event) => {
  event.preventDefault();

  const formData = new FormData(reviewForm);
  const review = {
    name: String(formData.get("review-name") ?? "").trim(),
    rating: Number(formData.get("review-rating")),
    comment: String(formData.get("review-comment") ?? "").trim(),
  };

  if (!review.name || !review.comment) {
    reviewStatus.textContent = "Completa tu nombre y opinión antes de publicar.";
    return;
  }

  reviewGrid.prepend(createReviewCard(review));

  try {
    const reviews = readSavedReviews();
    reviews.unshift(review);
    localStorage.setItem(reviewStorageKey, JSON.stringify(reviews));
    reviewStatus.textContent =
      "Gracias por compartir tu opinión. Quedó guardada solo en este dispositivo.";
  } catch {
    reviewStatus.textContent =
      "Tu opinión ya aparece en la lista, pero no se pudo guardar en este dispositivo.";
  }

  reviewForm.reset();
});
