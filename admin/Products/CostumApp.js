//Search Bar
document.addEventListener("DOMContentLoaded", function() {
    //Seach Bar 

    const searchBar = document.getElementById("searchBar");
    const tableRows = document.querySelectorAll(".user-list tr"); // Select all rows except the header row

    searchBar.addEventListener("keyup", function() {

        const searchText = searchBar.value.toLowerCase();

        tableRows.forEach(row => {
            const productNameElement = row.querySelector("td:nth-child(3)");

            if (productNameElement) {
                const productName = productNameElement.textContent.toLowerCase();

                if (productName.includes(searchText)) {
                    row.style.display = "table-row";
                } else {
                    row.style.display = "none";
                }
            }
        });
    });


});


