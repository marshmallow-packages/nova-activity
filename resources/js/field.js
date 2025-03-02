Nova.booting((Vue) => {
    Vue.component("index-activity", require("./components/IndexField").default);
    Vue.component(
        "detail-activity",
        require("./components/DetailField").default
    );
    Vue.component("form-activity", require("./components/FormField").default);
});

if (
    localStorage.novaTheme === "dark" ||
    (!("novaTheme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches)
) {
    document.documentElement.classList.add("tw-dark");
} else {
    document.documentElement.classList.remove("tw-dark");
}
