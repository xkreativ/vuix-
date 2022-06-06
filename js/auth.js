const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".auth-wrapper");

sign_up_btn.addEventListener("click", () => {
  container.classList.add("sign-up-mode");
  document.cookie = "auth-toggle=1";
});

sign_in_btn.addEventListener("click", () => {
  container.classList.remove("sign-up-mode");
  document.cookie = "auth-toggle=0";
});
