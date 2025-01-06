//disable the apply filters button initially
function checkFilters() {
    const minPrice = document.getElementById('min_price').value;
    const maxPrice = document.getElementById('max_price').value;
    const genreCheckboxes = document.querySelectorAll('input[name="genre[]"]:checked');

    //if no filters are selected, disable the button
    if (minPrice === "" && maxPrice === "" && genreCheckboxes.length === 0) {
        document.getElementById('apply-filters').disabled = true;
    } else {
        document.getElementById('apply-filters').disabled = false;
    }
}

//event listener for the inputs to check if filters are selected
document.getElementById('min_price').addEventListener('input', checkFilters);
document.getElementById('max_price').addEventListener('input', checkFilters);
document.querySelectorAll('input[name="genre[]"]').forEach(function (checkbox) {
    checkbox.addEventListener('change', checkFilters);
});

//initial check on page load
checkFilters();
