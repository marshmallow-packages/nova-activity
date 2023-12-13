import IndexField from "./components/IndexField";
import DetailField from "./components/DetailField";
import FormField from "./components/FormField";

Nova.booting((app, store) => {
    app.component("index-activity", IndexField);
    app.component("detail-activity", DetailField);
    app.component("form-activity", FormField);
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
