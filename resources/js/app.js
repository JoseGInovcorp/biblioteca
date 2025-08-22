import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

document.addEventListener("DOMContentLoaded", function () {
    const el = document.getElementById("autores-select");
    if (el) {
        new TomSelect(el, {
            plugins: ["remove_button"],
            placeholder: "Selecione um ou mais autores",
            persist: false,
            create: false,
            sortField: {
                field: "text",
                direction: "asc",
            },
        });
    }
});
