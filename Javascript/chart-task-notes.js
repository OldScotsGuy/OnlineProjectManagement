
var tasks = document.getElementsByClassName("chart-task");
var i;
for (i = 0; i < tasks.length; i++) {
    tasks[i].addEventListener("click", function() {
        // this is the element the click listener is applied to - ie the task bar div element
        this.classList.toggle("active");
        var tdParent = this.parentElement;          // <td> element the div sits in
        var trParent = tdParent.parentElement;      // <tr> the <td> element sits in
        var notes = trParent.nextElementSibling;    // <tr> adjacent to task bar

        // Toggle that element
        if (notes.style.display === "table-row") {
            notes.style.display = "none";
        } else {
            notes.style.display = "table-row";
        }
    });
}