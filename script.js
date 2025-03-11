// script.js
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const table = document.querySelector("table tbody");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const search = form.querySelector("input[name='search']").value;
        const url = `?search=${encodeURIComponent(search)}`;
        window.location.href = url;
    });
});